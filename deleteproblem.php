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

// Pull the pid
$pid = optional_param('pid', 0, PARAM_INT); // problem id
$confirm = optional_param('confirm', 0, PARAM_INT); // student work ID

// Get the problem from the pid
$problem = $DB->get_record('sort_problem', array('id' => $pid));
if (!$problem) {
  print_error('That problem does not exist.  It cannot be deleted');
}

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
add_to_log($course->id, 'sort', 'view', "deleteproblem.php?pid=$pid", "Deleting problem", $cm->id);


// Only editors can see this page.
require_capability('mod/sort:edit', $context);


if ($confirm && $pid) {
  $studentworks = $DB->get_records('sort_studentwork',array('pid' => $problem->id));
  if ($studentworks) {
    $DB->delete_records_list('sort_classification', 'swid', array_keys($studentworks));
    $DB->delete_records('sort_studentwork', array('pid' => $problem->id));
  }
  $DB->delete_records('sort_problem',array('id' => $problem->id));
  
  redirect("view.php?s=$sort->id");
}

// Set the page header.
$PAGE->set_url('/mod/sort/editstuwork.php', array('pid' => $pid, 'swid' => $swid));
$PAGE->set_title(format_string("Editing student work."));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->add_body_class('sort-stuwork-edit-form');

// Sort CSS styles.
$PAGE->requires->css('/mod/sort/css/sort.css');

// Begin page output
echo $OUTPUT->header();



echo $OUTPUT->confirm("Are you sure you want to delete $problem->name?  Any student work and participant classificatinos will be lost.","deleteproblem.php?pid=$pid&confirm=1","view.php?s=$sort->id");

echo $OUTPUT->footer();










