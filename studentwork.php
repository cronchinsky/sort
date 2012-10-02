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
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage sort
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/sort_comment_form.php');

$swid = optional_param('id', 0, PARAM_INT); // studentwork ID
$pid = optional_param('pid', 0, PARAM_INT); // pid

$studentwork = $DB->get_record('sort_studentwork',array('id' => $swid));

if ($studentwork) {
  $this_studentwork = $studentwork;
  $pid = $studentwork->pid;
}
else {
  if (!$pid) {
    print_error('That student sample or problem does not exist!');
  }
}
$studentworks = $DB->get_records('sort_studentwork', array('pid' => $pid), 'name ASC');

$swids = "";
foreach ($studentworks as $studentwork) {
  if ($swids == "") $swids = "($studentwork->id";
  else $swids.=  ",$studentwork->id";
}
$swids.=")";


if (optional_param('pid', 0, PARAM_INT)) {
  if ($swids == ")") {
    print_error("No student work has been created yet!");
  }
  $user_classification = $DB->get_record_select('sort_classification', "uid = $USER->id AND swid IN $swids ",array(),'*',IGNORE_MULTIPLE);
  if (!$user_classification) {
    redirect("problem.php?id=$pid","You need to sort some student work before you can examine these results");
  }
  $this_studentwork = $studentworks[$user_classification->swid];
  $swid = $this_studentwork->id;
}



$problem = $DB->get_record('sort_problem',array('id'=>$this_studentwork->pid));
$sort = $DB->get_record('sort', array('id' => $problem->sid));
$course = $DB->get_record('course',array('id' => $sort->course));
if ($course->id) {
    $cm  = get_coursemodule_from_instance('sort', $sort->id, $course->id, false, MUST_EXIST);
}
else {
    error('Could not find the problem sort activity or course!');
}

// require_login has to come before additional settings to the $PAGE variable, or somethings (like
// pagelayout are lost -- CR
require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

/// Set the page info

$PAGE->set_url('/mod/sort/studentwork.php', array('id' => $swid));
$PAGE->set_title(format_string("Viewing $this_studentwork->name"));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$PAGE->add_body_class('sort-student-work-view');

$PAGE->requires->css('/mod/sort/css/jquery-ui.css');
$PAGE->requires->js('/mod/sort/scripts/jquery.min.js');
$PAGE->requires->js('/mod/sort/scripts/sort-comments.js');
$PAGE->requires->css('/mod/sort/css/sort.css');
$PAGE->requires->js('/mod/sort/scripts/jquery-ui.min.js');

sort_set_display_type($sort);

add_to_log($course->id, 'sort', 'view', "studentwork.php?id=$swid", $this_studentwork->name, $cm->id);


// Get categories from sort object.
$categories = sort_get_categories($sort->id, $context);

$arguments = array(
    'contextid' => $context->id,
    'component' => 'mod_sort',
    'filearea' => 'studentwork',
  );

$image = $DB->get_record_select('files', "filesize <> 0 AND component = 'mod_sort' AND contextid = '$context->id' AND filearea= 'studentwork' AND itemid = $swid");


$this_classification = $DB->get_record('sort_classification', array('swid' => $swid, 'uid' => $USER->id));
if (!$this_classification) {
  print_error('You haven\'t sorted this piece of student work yet!');
}

$mform = new sort_comment_form("studentwork.php?id=$swid", array('classification' => $this_classification));


$classifications = $DB->get_records_select('sort_classification',"uid = $USER->id AND swid IN $swids ", NULL, 'commenttime ASC');

foreach($classifications as $classification) {
  $classifications_indexed[$classification->swid] = $classification;
}

$score_totals = unserialize($problem->previous_data);

$uids = array();
$all_classifications = $DB->get_records('sort_classification', array('swid' => $swid));

/* START: prefill code: TO REMOVE */
$prefill = array(
		15 => array(
			1 => 0,
			2 => 7,
			3 => 0,
			4 => 3,
    ),
		14 => array(
			1 => 9,
			2 => 0,
			3 => 0,
			4 => 1,
		),
		13 => array(
			1 => 0,
			2 => 0,
			3 => 10,
			4 => 0,
		),
		12 => array(
			1 => 10,
			2 => 0,
			3 => 0,
			4 => 0,
		),
		11 => array(
			1 => 0,
			2 => 0,
			3 => 8,
			4 => 2,
		),
		10 => array(
			1 => 0,
			2 => 10,
			3 => 0,
			4 => 0,
		),
		9 => array(
			1 => 0,
			2 => 8,
			3 => 0,
			4 => 2,
		),
		8 => array(
			1 => 0,
			2 => 0,
			3 => 10,
			4 => 0,
		),
		7 => array(
			1 => 10,
			2 => 0,
			3 => 10,
			4 => 0,
		),
		6 => array(
			1 => 10,
			2 => 0,
			3 => 0,
			4 => 0,
		),
		5 => array(
			1 => 0,
			2 => 0,
			3 => 8,
			4 => 2,
		),
		4 => array(
			1 => 8,
			2 => 0,
			3 => 0,
			4 => 2,
		),
		3 => array(
			1 => 6,
			2 => 0,
			3 => 2,
			4 => 2,
		),
		2 => array(
			1 => 0,
			2 => 7,
			3 => 0,
			4 => 3,
		),
		1 => array(
			1 => 0,
			2 => 0,
			3 => 10,
			4 => 0,
		),

	);

/* END: prefill code TO REMOVE */
 
if ($pid == 6) {
  for ($i = 1; $i <= 4; $i++) {
      $score_totals[$i] = $prefill[$swid][$i];
  }
}


foreach($all_classifications as $classification) {
  $score_totals[$classification->category] += 1;
  $uids[] = $classification->uid;
}

$all_votes_total = array_sum($score_totals);

foreach ($score_totals as $key=>$total) {
   $percentages[$key] = round($total/$all_votes_total * 100);
}
  
  
$users = $DB->get_records_list('user','id',$uids);


if ($results = $mform->get_data()) {
  $this_classification->commenttext = $results->commenttext['text'];
  $this_classification->commenttime = time();
  $DB->update_record('sort_classification', $this_classification);
  redirect("studentwork.php?id=$swid");
}





echo $OUTPUT->header();



$sw_links = array();
echo "<div class='sort-student-work-pager'>";
echo "<h4>Select a work sample to view</h4>";
echo "<ul>";
foreach ($studentworks as $studentwork) {
  if (isset($classifications_indexed[$studentwork->id])) {
    $classes = ($studentwork->id == $swid) ? " class='sort-pager-current' " : "";
    $sw_links[$studentwork->name] = "<li$classes><a href='studentwork.php?id=$studentwork->id'>$studentwork->name</a></li>";
  }
}

ksort($sw_links);
foreach ($sw_links as $link) {
  echo $link;
}
echo "</ul>";
echo "</div>";
echo "<div class='sort-student-work-wrapper'>";
echo $OUTPUT->heading("Sample " . $this_studentwork->name);
echo "<div class='sort-work-sample-wrapper'>";
echo "<div class='sort-work-sample'>";
echo "<img src='" . sort_get_image_file_url($image) . "' class='sort-work-sample-image' />";
echo "</div>";
echo "</div>";


echo "<div class='sort-classification-wrapper'>";

echo "<div class='sort-user-classification-wrapper'><div class='sort-user-classification'>";
echo "<h3>How did I sort the work?</h3>";
$cat_index = $this_classification->category;
echo "<p><em>" . $categories[$cat_index]->category . "</em></p>";
echo "</div></div>";
echo "<div class='sort-others-classification-wrapper'><div class='sort-others-classification'>";
echo "<h3>How have others sorted the work?</h3>";
echo "<table class='sort-others-table'>";

foreach ($percentages as $cat_index => $precentage) {
  echo "<tr><td><em>" . $categories[$cat_index]->category . "</em></td><td>" . $precentage . "% (" . $score_totals[$cat_index] ." classifications)</td></tr>";
}
echo "</table>";
echo "</div></div>";

echo "</div>";
echo '<div class="sort-studentwork-comment-form">';
echo "<h3 class='sort-studentwork-comment-form-header'><span class='ui-icon ui-icon-triangle-1-e sort-explanation-triangle'></span><span class='sort-explanation-header'>My Explanation - Give reasons for your classification</span></h3>";
echo "<div class='sort-studentwork-comment-form-body'>";
if (isset($this_classification->commenttime)) unset($this_classification->commenttime);

echo $mform->display();

echo "</div>";
echo "</div>";
echo '<div class="sort-studentwork-comments">';

echo "<h3>Explanations</h3>";
echo "<p>View other participant's explanations.</p>";
echo "<div id='sort-filter-container' >Filter: <select id='sort-comment-filter'>";
echo "<option value='0'>- All -</option>";
foreach ($categories as $cat_id => $category)  {
  echo "<option value='$cat_id'>$category->category</option>";
}
  
echo "</select></div>
";
$first = true;
foreach ($all_classifications as $classification) {
  if (!is_null($classification->commenttext)) {
    $class = "sort-comment-box sort-comment-category-$classification->category";
    if ($first) {
      $first = false;
      $class .= ' sort-comment-first';
    }
    echo "<div class='$class'>";
    //echo "<div class='sort-comment-username'><strong>" . $users[$classification->uid]->username . "</strong> - <em>" . $categories[$classification->category]->category . "</em></div> ";
    echo "<div class='sort-comment-username'><em>" . $categories[$classification->category]->category . "</em></div> ";
    echo "<div class='sort-comment-body'>" . format_text($classification->commenttext) . "</div>";
    echo "</div>";
  }
}

echo '</div>';
echo "<div class='sort-action-links'>";
echo '<span class="sort-back-problem-link-box"><a href="problem.php?id=' . $problem->id . '">Back to Sort</a></span>';
echo "</div>";
echo "</div>";

// Finish the page
echo $OUTPUT->footer();
