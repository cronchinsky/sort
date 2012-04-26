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
 * Prints a particular instance of sort
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

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$s  = optional_param('s', 0, PARAM_INT);  // sort instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('sort', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $sort  = $DB->get_record('sort', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($s) {
    $sort  = $DB->get_record('sort', array('id' => $s), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $sort->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('sort', $sort->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

add_to_log($course->id, 'sort', 'view', "view.php?id={$cm->id}", $sort->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/sort/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($sort->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->requires->css('/mod/sort/css/sort.css');
sort_set_display_type($sort);



// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('sort-'.$somevar);

// Output starts here
echo $OUTPUT->header();

if ($sort->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('sort', $sort, $cm->id), 'generalbox mod_introbox', 'sortintro');
}

$sid = $sort->id;

$problems = $DB->get_records('sort_problem', array('sid' => $sid));

if ($problems) {
  echo $OUTPUT->heading('Problems in this Sorting Activity');
  echo "<div class='sort-problem-list-wrapper'>";
  echo "<ul class='sort-problem-list'>";
  foreach ($problems as $problem) {
    echo '<li><a href="' . $CFG->wwwroot . '/mod/sort/problem.php?id=' . $problem->id . '">' . $problem->name . '</a>';
    if (has_capability('mod/sort:edit', $context)) {
      echo " &nbsp;&nbsp;<a href='deleteproblem.php?pid=$problem->id'><img src='" . $OUTPUT->pix_url('t/delete') . "' alt='delete' /></a>";
    }
    
    echo '</li>';
  }
  echo "</ul>";
  echo "</div>";
}
else {
  echo $OUTPUT->heading('There are no problems in this sorting activity.');
}
echo "<div class='sort-action-links'>";
echo "<div class='sort-see-all-scores-link-box'><a href='allscores.php?sid=$sort->id'>See All of My Scores</a></div>";
if (has_capability('mod/sort:edit', $context)) echo "<div class='sort-add-problem-link-box'><a href='" . $CFG->wwwroot . '/mod/sort/newproblem.php?sid=' . $sid . "'>Add a new problem</a></div>";
echo "</div>";
// Finish the page
echo $OUTPUT->footer();
