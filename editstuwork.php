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
 * Creates new pieces of student work and edits existing ones.
 *
 * @package    mod
 * @subpackage sort
 * @copyright  2012 EdTech Leaders Online
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

// Include the edit form.
require_once(dirname(__FILE__) . '/sort_edit_studentwork_form.php');

// Pull the pid and/or swid from the url.
$pid = optional_param('pid', 0, PARAM_INT); // sort ID
$swid = optional_param('swid', 0, PARAM_INT); // student work ID
// Get the problem from the pid.
$problem = $DB->get_record('sort_problem', array('id' => $pid));
if (!$problem) {
  print_error('That problem does not exist!');
}

// Get the sort activity, course, etc from the problem.
$sort = $DB->get_record('sort', array('id' => $problem->sid));
$course = $DB->get_record('course', array('id' => $sort->course));
if ($course->id) {
  $cm = get_coursemodule_from_instance('sort', $sort->id, $course->id, false, MUST_EXIST);
}
else {
  error('Could not find the course / sort activity!');
}

// Moodley goodness.
require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
add_to_log($course->id, 'sort', 'view', "editstuwork.php?pid=$pid", $problem->name, $cm->id);


// Only editors can see this page.
require_capability('mod/sort:edit', $context);

// Set the page header. Needs to happen before the form code in order to stick, but I'm not sure why - CR
$PAGE->set_url('/mod/sort/editstuwork.php', array('pid' => $pid, 'swid' => $swid));
$PAGE->set_title(format_string("Editing student work."));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->add_body_class('sort-stuwork-edit-form');

// Sort CSS styles.
$PAGE->requires->css('/mod/sort/css/sort.css');


sort_set_display_type($sort);

// All student work for the problem.
$studentworks = $DB->get_records('sort_studentwork', array('pid' => $problem->id));

// If there's a swid in the url, we're editing an exisitng student work
if ($swid != 0) {
  // Get a piece of existing student work to load in the draft area
  $stuwork = $DB->get_record('sort_studentwork', array('id' => $swid));
  // If there is no student work, the swid is funky.
  if (!$stuwork) {
    print_error('Can not find any student work');
  }
  // This helps with the form.  studentworkname is the form element's name
  $stuwork->studentworkname = $stuwork->name;
}


// Load the form.
$mform = new sort_new_studentwork_form("/mod/sort/editstuwork.php?pid=$pid&swid=$swid", array('studentworks' => $studentworks, 'this_studentwork' => $stuwork));

// If the form was cancelled, redirect.
if ($mform->is_cancelled()) {
  redirect("problem.php?id=$pid");
}
else {

  
  //Set up the draft area.
  $draftitemid = file_get_submitted_draft_itemid('attachments');
  file_prepare_draft_area($draftitemid, $context->id, 'mod_sort', 'studentwork', $stuwork->id, array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 50));

  // This also helps with the form, since attachments is the form element name.
  $stuwork->attachments = $draftitemid;

  // Put the existing data into the form.
  $mform->set_data($stuwork);
  
  // If there's data in the form...
  if ($results = $mform->get_data()) {
    
    // If the the data is for a new piece of student work...
    if ($swid == 0) {
      // Save the new student work as a new record.
      $stuwork->pid = $pid;
      $stuwork->name = $results->studentworkname;
      $new_record = $DB->insert_record('sort_studentwork', $stuwork);
      file_save_draft_area_files($results->attachments, $context->id, 'mod_sort', 'studentwork', $new_record);
    }
    else {
      // We're updaing existing work.
      $stuwork->name = $results->studentworkname;
      $updated_record = $DB->update_record('sort_studentwork', $stuwork);
      file_save_draft_area_files($results->attachments, $context->id, 'mod_sort', 'studentwork', $stuwork->id);
    }
    // Now redirect back to the problem page with the new / updated data.
    redirect("problem.php?id=$pid");
  }
}

// Begin page output
echo $OUTPUT->header();
echo $OUTPUT->heading("Manage Student Work for {$problem->name}");


echo "<p>Select a piece of student work to edit, or click \"Add New\" to create a new piece of student work to sort.</p>";
echo "<ul class='sort-student-work-pager'>";
foreach ($studentworks as $studentwork) {
  $class = ($swid == $studentwork->id) ? "class=\"sort-pager-current\"" : ""; 
  echo '<li ' . $class . '><a href="' . $CFG->wwwroot . '/mod/sort/editstuwork.php?pid=' . $studentwork->pid . '&amp;swid=' . $studentwork->id . '">' . $studentwork->name . '</a></li>';
}
$class = (!$swid) ? ' class="sort-pager-current" ' : "";
echo '<li' . $class . '><a href="' . $CFG->wwwroot . '/mod/sort/editstuwork.php?pid=' . $studentwork->pid . '">Add New</a></li>';
echo "</ul>";

echo "<div class='sort-manage-form-wrapper'>";
if ($swid) echo $OUTPUT->heading("Editing $stuwork->name");
else echo $OUTPUT->heading("Adding New Student Work");

//displays the form
$mform->display();
if ($swid) echo "<p class='sort-delete-link'><a href='deletestuwork.php?swid=$swid'>Delete This Student Work Sample</a></p>";
echo "</div>";
// Finish the page
echo $OUTPUT->footer();









