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

// Get the problem from the 
$pid = optional_param('pid', 0, PARAM_INT); // problem ID
$problem = $DB->get_record('sort_problem',array('id' => $pid));
if (!$problem) {
  error('That problem does not exist!');
}

$sort = $DB->get_record('sort',array('id'=>$problem->sid));
$course = $DB->get_record('course',array('id' => $sort->course));
if ($course->id) {
    $cm         = get_coursemodule_from_instance('sort', $sort->id, $course->id, false, MUST_EXIST);
}
else {
    error('Could not find the course!');
}


require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

//print_object($USER);

add_to_log($course->id, 'sort', 'view', "scores.php?pid=$problem->id", $USER->username, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/sort/scores.php', array('pid' => $problem->id));
$PAGE->set_title(format_string($problem->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->requires->css('/mod/sort/css/sort.css');


// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
$PAGE->add_body_class('sort-user-scores-grid-view');

sort_set_display_type($sort);

// Output starts here
echo $OUTPUT->header();

echo $OUTPUT->heading("Your Classifications for $problem->name");

// Get categories from sort object.
$categories = sort_get_categories($sort->id, $context);

//Get all classifications associated with this problem for this user.
$studentworks = $DB->get_records('sort_studentwork', array('pid' => $problem->id), 'name');
$where = "";
foreach($studentworks as $studentwork) {
  if ($where == "") $where .= "swid = $studentwork->id ";
  else $where .= "OR swid = $studentwork->id ";
}

if ($where == "") print_error('No ' . get_string('samplename','sort') . ' found for this problem!');

$where .= " AND uid = $USER->id";

$classifications = $DB->get_records_select('sort_classification',$where);

foreach ($classifications as $classification) {
  $classifications_indexed[$classification->swid] = $classification;
}

$table = "
<table class='sort-my-scores-table'>
  <tr>
    ";
foreach ($categories as $category) {
  $table .= "<th>$category->name</th>";
}


foreach ($studentworks as $studentwork) {
$table .= "<tr><td>$studentwork->name</td>";
}
foreach ($categories as $category) {
  if (isset($classifications_indexed[$studentwork->id]) && $classifications_indexed[$studentwork->id]->category == $category->category) {
    $table .="<td>X</td>";
  }
  else {
    $table .="<td></td>";
  }
}


$table .="</tr>";

$table .= "</table>";
echo $table;
echo "<div class='sort-action-links'>";
echo "<span><a href='problem.php?id=$pid'>Back to the Problem</a></span>";
echo "</div>";

// Finish the page

echo $OUTPUT->footer();
