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


require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/sort_new_studentwork_form.php');

$pid = optional_param('pid', 0, PARAM_INT); // sort ID
$problem = $DB->get_record('sort_problem',array('id' => $pid));
if (!$problem) {
  print_error('That problem does not exist!!');
}

$sort = $DB->get_record('sort',array('id' => $problem->sid));

$course = $DB->get_record('course',array('id' => $sort->course));

if ($course->id) {
    $cm = get_coursemodule_from_instance('sort', $sort->id, $course->id, false, MUST_EXIST);
}
else {
    error('Could not find the course / sort activity!');
}


require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

add_to_log($course->id, 'sort', 'view', "newstuwork.php?pid=$pid", $problem->name, $cm->id);


$PAGE->set_url('/mod/sort/newstuwork.php', array('pid' => $pid));
$PAGE->set_title(format_string("Adding a new piece of student work."));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
$PAGE->add_body_class('sort-stuwork-add-form');


$mform = new sort_new_studentwork_form("/mod/sort/newstuwork.php?pid=$pid");

if ($mform->is_cancelled()) {
    redirect("problem.php?id=$pid");
} else if ($results = $mform->get_data()) {
  $stuwork->pid = $pid;
  $stuwork->name = $results->studentworkname;
  $new_record = $DB->insert_record('sort_studentwork',$stuwork);
  file_save_draft_area_files($results->attachments, $context->id, 'mod_sort', 'attachment', $new_record );
  redirect("problem.php?id=$pid");
} else {
  echo $OUTPUT->header();
  echo $OUTPUT->heading("Adding a new piece of student work to {$problem->name}");
  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
  // or on the first display of the form.
 
  //displays the form
  $mform->display();
  
  // Finish the page
  echo $OUTPUT->footer();
}








