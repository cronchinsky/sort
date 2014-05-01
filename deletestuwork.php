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

// Pull the pid and/or swid from the url.
$swid = optional_param('swid', 0, PARAM_INT); // student work ID
$confirm = optional_param('confirm', 0, PARAM_INT); // student work ID
// Get the stuwork from the swid
$stuwork = $DB->get_record('sort_studentwork', array('id' => $swid));
if (!$stuwork) {
  print_error('That ' . get_string('samplename','sort') . ' does not exist.  It cannot be deleted');
}

// Get the sort activity, course, etc from the stuwork.
$problem = $DB->get_record('sort_problem',array('id' => $stuwork->pid));
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
add_to_log($course->id, 'sort', 'view', "deletestuwork.php?swid=$swid", "Deleting student work", $cm->id);


// Only editors can see this page.
require_capability('mod/sort:edit', $context);


if ($confirm && $swid) {
  $DB->delete_records('sort_classification', array('swid' => $swid));
  $DB->delete_records('sort_studentwork', array('id' => $swid));
  redirect("editstuwork.php?pid=$problem->id");
}

// Set the page header.
$PAGE->set_url('/mod/sort/deletestuwork.php', array('swid' => $swid));
$PAGE->set_title(format_string("Editing " . get_string("samplename","sort")));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->add_body_class('sort-stuwork-edit-form');

// Sort CSS styles.
$PAGE->requires->css('/mod/sort/css/sort.css');

sort_set_display_type($sort);

// Begin page output
echo $OUTPUT->header();



echo $OUTPUT->confirm("Are you sure you want to delete " . get_string("samplename","sort") . " $stuwork->name from $problem->name?  Any participant classifications and explanations will be lost.","deletestuwork.php?swid=$swid&confirm=1","editstuwork.php?pid=$problem->id&swid=$swid");

echo $OUTPUT->footer();










