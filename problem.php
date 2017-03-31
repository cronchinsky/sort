  <?php

  // This file is part of Moodle - http://moodle.org/
  //
  // Moodle is free software: you can redistribute it and/or modify
  // it under the terms of the GNU General Public License as published by
  // the Free Software Foundation, either version 3 of the License, or
  // (at your option) any later version.
  //
  // Moodle is distributed in the hope that it will be useful,
  // but WITHOUT ANY WARRANTY; without even the implied warranty of
  // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  // GNU General Public License for more details.
  //
  // You should have received a copy of the GNU General Public License
  // along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

  /**
   * Prints a particular instance of a problem in the sort module.
   *
   * @package    mod
   * @subpackage sort
   * @copyright  2011 Your Name
   * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
   */
  require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
  require_once(dirname(__FILE__) . '/lib.php');

  // Add in the classify form.
  require_once(dirname(__FILE__) . '/sort_classify_form.php');

  // Grab the pid from the url
  $id = optional_param('id', 0, PARAM_INT); // problem ID

  $put_last = optional_param('putLast',"",PARAM_TEXT);
  $nojs_swid = optional_param('swid',0,PARAM_INT);
  $nojs_category = optional_param('cat',0,PARAM_INT);

  // Load the problem from the url ID
  $problem = $DB->get_record('sort_problem', array('id' => $id));

  // If the problem is not found, throw an error
  if (!$problem) {
    error('That problem does not exist!');
  }

  // Load the sort activity, course, and cm context from the problem, and up the chain.
  $sort = $DB->get_record('sort', array('id' => $problem->sid));
  $course = $DB->get_record('course', array('id' => $sort->course));
  if ($course->id) {
    $cm = get_coursemodule_from_instance('sort', $sort->id, $course->id, false, MUST_EXIST);
  }
  else {
    error('Could not find the course!');
  }

  // This is some moodle stuff that seems to be necessary :)
  require_login($course, true, $cm);
  $context = context_module::instance($cm->id);

  // Log this page view.
  add_to_log($course->id, 'sort', 'view', "problem.php?id={$cm->id}", $problem->name, $cm->id);

  $categories = sort_get_categories($sort->id, $context);

  // If we have no js, update the individual categorization from the url
  if ($nojs_category && $nojs_swid) {
    $classification = $DB->get_record('sort_classification', array('swid' => $nojs_swid, 'uid' => $USER->id));
    if ($classification) {
      $classification->category = $nojs_category;
      $DB->update_record('sort_classification',$classification);
    }
    else {
      unset($classification);
      $classification->category = $nojs_category;
      $classification->swid = $nojs_swid;
      $classification->uid = $USER->id;
      $DB->insert_record('sort_classification',$classification);
    }
  }


  //Get all pieces of student work associated with this problem.
  $studentworks = $DB->get_records('sort_studentwork', array('pid' => $problem->id));

  if ($studentworks) {
  
  // if SW gets enters A, B, C... then it is displayed ..C, B, A: so reverse for now.
  $studentworks = array_reverse($studentworks, true);
  
  // Get an array of swids for the student work here.
  $swids = array_keys($studentworks);
  $swids = array_combine($swids,$swids);
  
  // Check to see if there are any correct answers
  $has_correct = $sort->has_correct;
  
  // If there is student work associated with this problem, load any classifications
  // from this user for any of these swids.  Yay implode!
  if ($swids) {
    $classifications = $DB->get_records_select('sort_classification', "uid = $USER->id AND swid IN (" . implode(",", $swids) . ") ");
  }

  $classifications_indexed = array();
  foreach ($classifications as $classification) {
    $studentworks[$classification->swid]->category = $classification->category;
    $classifications_indexed[$classification->swid] = $classification;
  }
  }
  /// Print the page header

    $PAGE->set_url('/mod/sort/problem.php', array('id' => $problem->id));
    $PAGE->set_title(format_string($problem->name));
    $PAGE->set_heading(format_string($course->fullname));
    $PAGE->set_context($context);


  // Add the necssary CSS and JS for the sort activity, including jquery (and ui).
    $PAGE->requires->css('/mod/sort/css/jquery-ui.css');
    $PAGE->requires->css('/mod/sort/css/sort.css');

    $PAGE->requires->js('/mod/sort/scripts/jquery.min.js');
    $PAGE->requires->js('/mod/sort/scripts/jquery-ui.min.js');
    $PAGE->requires->js('/mod/sort/scripts/sort.js');
    $PAGE->add_body_class('sort-problem-view');

    sort_set_display_type($sort);




  // If we do have student work that is unsorted, include the classify form
  // which has hidden elements and a save button.
  if ($studentworks) {
    $mform = new sort_classify_form("/mod/sort/problem.php?id={$problem->id}", array('studentworks' => $studentworks, 'sort' => $sort, 'categories' => $categories));

    // Get the results.  If there are results, the user has submitted their sorts.
    // Loop through each of their sorts and insert a classification record.
    if ($form_results = $mform->get_data()) {

      $form_swids = explode(',', $form_results->swids);
      array_pop($form_swids);
      $form_results = (array) $form_results;
      $sort_array = (array) $sort;

      foreach ($form_swids as $swid) {
        unset($classification);
        $classification->category = $form_results['studentwork_classify_' . $swid];
        $classification->swid = $swid;
        $classification->uid = $USER->id;

        // If this is set, there was previous data.
        if (isset($studentworks[$swid]->category)) {
          $classification->id = $classifications_indexed[$swid]->id;
          if ($classification->category == 0) {
            $DB->delete_records('sort_classification', array('id'=>$classification->id));
          }
          else {
            $DB->update_record('sort_classification',$classification);
          }
        }
        else { // We have new classifications.
          if ($classification->category !=0) {
            $DB->insert_record('sort_classification',$classification);
          }
        }

      }

      // Redirect, the form has been submitted and the data processed.
      redirect("problem.php?id={$problem->id}");
    }


    // Output starts here
    echo $OUTPUT->header();
    echo $OUTPUT->heading($sort->name);



    $arguments = array(
      'contextid' => $context->id,
      'component' => 'mod_sort',
      'filearea' => 'studentwork',
    );

    // Find all image files tied to the swids for sortable student work.
    $files = $DB->get_records_select('files', "filesize <> 0 AND component = 'mod_sort' AND contextid = '$context->id' AND filearea= 'studentwork' AND itemid IN (" . implode(",", $swids) . ") ");

    // Create a url_index of image file urls indexed by the swid.
    $url_index = array();
    foreach ($files as $file) {
      $url_index[$file->itemid] = sort_get_image_file_url($file);
    }


  

      // Create a box for the drag and drop interface.
      echo '<div class="sort-drag-interface ui-widget ui-helper-clearfix">';

      $category_html = array(
        '1' => '',
        '2' => '',
        '3' => '',
        '4' => '',
      );

      // Create a ul that serves as the 'gallery' or sortable items.  Add the li
      // In propper form for jquery ui for each student work.
      echo '<ul id="sort-gallery" class="sort-gallery ui-helper-reset ui-helper-clearfix">';
      echo "<li class='sort-gallery-end-message'><p>No More Student Work to Classify</p></li>";
      $gallery ="";
      $last_items = array();
      $put_last_swids = explode(',',$put_last);

      foreach ($studentworks as $studentwork) {
          $image_url = $url_index[$studentwork->id];  
          $put_last_url = ($put_last == "") ? "problem.php?id=$id&amp;putLast=$studentwork->id" : "problem.php?id=$id&amp;putLast=$put_last,$studentwork->id";

          $item = "<li id='studentwork_$studentwork->id' class='ui-widget-content ui-corner-tr sort-draggable sort-studentwork' data-correct='$studentwork->correct_answer'>";
          $item .= '<h5 class="ui-widget-header">' . $studentwork->name . "</h5>";
          $item .= '<img src="' . $image_url . '" alt="' . addslashes($studentwork->name) . '" />';
          $item .= '<a href="' . $image_url . '" title="View larger image" class="ui-icon ui-icon-magnifying">View larger</a>';
          $item .= '<a href="' . $put_last_url . '" title="Next Piece of Student Work" class="sort-next-button ui-icon ui-icon-next-arrow">Next Piece of Student Work</a>';
       
          $item .= '<ul class="sort-actions-list">';
          foreach ($categories as $key => $category) {
            $item .= "<li><a href='problem.php?id=$id&amp;swid=$studentwork->id&amp;cat=$key'>$category->category</a></li>";
          }
          $item .= '</ul>';
          $item .= '</li>';
          if (isset($studentwork->category)) {
            $categories[$studentwork->category]->html .= $item;
          }
          else {
            if (in_array($studentwork->id, $put_last_swids)) {
              $last_items[$studentwork->id] = $item;
            }
            else {
              $gallery .= $item;
            }
          }
      }
      echo implode('',$last_items);
      echo $gallery;
      echo '</ul>';


      // Loop through the categories, skipping 0 which is reserved for 'all' and
      // add a category bin to drop stuff in.
      echo "<div id='category-container'>";
      foreach ($categories as $key => $category) {
        if ($key != 0) {
          echo "<div id='category_$key' class='sort-category ui-widget-content ui-state-default'>";
          echo "<h4 class='ui-widget-header'>$category->category</h4>";
         
          // validator doesn't like the empty <ul>'s..
          echo '<ul class="sort-gallery ui-helper-reset ui-helper-clearfix">';
          echo $category->html;
          echo '</ul>';
          echo "</div>";
        }
      }
      // Display the hidden form and its submit buttons.
      echo "<div id='correct-key' style='display:none'>Green Highlighting = Correct</div>";
      $mform->display();
      echo '</div>';



  }
  else {
    // Output starts here
    echo $OUTPUT->header();
    // If there's no student work tied to this problem, the teachers should add some.
    echo "<div class='sort-no-work-wrapper'><h2>No Student Work!  Add some!</h2>";
  }


 // Begin action links at the bottom.
  echo "<div class='sort-action-links'>";
  if (empty($has_correct)) echo '<span class="sort-participant-results-box"><a id="participant" href="studentwork.php?pid=' . $problem->id . '">Participant responses</a></span>';
  if (empty($has_correct)) echo "<span class='sort-see-all-scores-link-box'><a id='allscores' href='allscores.php?sid=$sort->id&amp;pid=$problem->id'>My class chart</a></span>";
  echo '<span class="sort-back-link-box"><a id="sortmenu" href="view.php?s=' . $sort->id . '">Sort menu</a></span>';
  if (has_capability('mod/sort:edit', $context)) {
  echo '<span class="sort-edit-stuwork-link-box"><a href="editstuwork.php?pid=' . $problem->id . '">Manage student work</a></span>';
  }
  if (!empty($has_correct)) {
    echo '<span class="sort-show-correct"><a class="sort-show-correct-link sort-show-correct-show" href="#">Check Work</a></span>';
  }
  echo "</div>";

    echo '</div>';

    if (empty($has_correct)) {
	      // Directions box for sort
	      echo "<fieldset class='sort-directions-box'>";
	      echo "<legend><span class='ui-icon ui-icon-triangle-1-e'></span><span class='sort-directions-text'>Directions and Examples</span></legend>";
	      echo "<div class='sort-directions-content'><p>Sort the student work into the different categories below.  You can click on the image of student work and drag it to one of the different categories. If you are unsure where to place the student's work and are not leaning towards one of the categories,it is ok to skip the piece and leaving it as unsorted. </p><p class='ui-icon-magnifying'>Click the magnifying glass to enlarge the image.</p><p class='ui-icon-next-arrow'>Click the arrow to move to the next image to sort.</p><h4 id='save'>After you have finished sorting, be sure to click 'Save changes' to permanently save your work.</h4>";
	      echo "<p>After saving your work, you can click on:</p>";
	      echo "<ul><li><em>Participant responses</em> to see how others sorted the work.</li><li><em>My class chart</em> to see a summary chart based on how you have sorted the student work into the categories.</li></ul>";
	      echo "<div class='sort-examples-accordion'>";
	    echo "<h3>Examples</h3>";
	    echo '<div id="accordion">';
	
	    foreach ($categories as $key=>$category) {
	      if ($key != 0) {
	        echo "<h3>" . $category->category . "</h3>";
	        echo "<div class='sort-examples-accordion-body'>";
	        echo "<p><img src='" . sort_get_image_file_url($category->image) . "' /></p>";
	        echo "<p>$category->exampletext</p>";
	        echo "</div>";
	      }
	    }
	    echo '</div>';
	    echo '</div>';
	      echo '</div>';
	      echo "</fieldset>";
	  	} else {
		   // Directions box for checked sort
		   echo "<fieldset class='sort-directions-box'>";
		   echo "<legend><span class='ui-icon ui-icon-triangle-1-e'></span><span class='sort-directions-text'>Directions</span></legend>";
		   echo "<div class='sort-directions-content'><p>Move each math problem to the appropriate bin.</p>";
		   echo "<p>Select the 'Check work' button to check.</p>";   
		   echo "<p>Problems that are in the correct bin will be highlighted green.</p>";
		   echo "</div>";
		   echo "</fieldset>";
	  }
      sort_add_attribution_line();
  // Finish the page
  echo $OUTPUT->footer();
