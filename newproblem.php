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
 * @copyright  2012 EdTech Leaders Online
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

// Include the new problem form.
require_once(dirname(__FILE__) . '/sort_new_problem_form.php');

// Get the sort activity id from the url.
$sid = optional_param('sid', 0, PARAM_INT); // sort ID
// Load the sort activity.
$sort = $DB->get_record('sort', array('id' => $sid));
if (!$sort) {
  print_error('That sort activity does not exist!');
}

// Get the course and cm.
$course = $DB->get_record('course', array('id' => $sort->course));
if ($course->id) {
  $cm = get_coursemodule_from_instance('sort', $sort->id, $course->id, false, MUST_EXIST);
}
else {
  error('Could not find the course!');
}

// Moodley goodness.
require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
add_to_log($course->id, 'sort', 'view', "newproblem.php?sid=$sid", $sort->name, $cm->id);

// Make sure we have an editor.
require_capability('mod/sort:edit',$context);

// Page header.
$PAGE->set_url('/mod/sort/newproblem.php', array('sid' => $sort->id));
$PAGE->set_title(format_string("Adding a new problem"));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->add_body_class('sort-problem-add-form');

// Output starts here
$mform = new sort_new_problem_form("/mod/sort/newproblem.php?sid={$sort->id}");

sort_set_display_type($sort);

// If the form was cancelled, return to the problem page.
if ($mform->is_cancelled()) {
  redirect("view.php?s={$sort->id}");
}
// Otherwise, if there are results from the form ...
else if ($results = $mform->get_data()) {
  // Load the data into a problem object and save it to the DB.
  $problem->sid = $sid;
  $problem->name = $results->problemname;
  $DB->insert_record('sort_problem', $problem);
  redirect("view.php?s={$sort->id}");
}
else {
  echo $OUTPUT->header();
  echo $OUTPUT->heading("Adding a new problem to {$sort->name}");

  // Display the form.
  $mform->display();

  // Finish the page
  echo $OUTPUT->footer();
}







