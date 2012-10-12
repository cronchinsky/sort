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

// Get the sort id from the url.
$sid = optional_param('sid', 0, PARAM_INT); // sort ID
$pid = optional_param('pid', 0, PARAM_INT); // problem ID

// Load any problems within this activity
$problems = $DB->get_records('sort_problem',array('sid' => $sid));

// No problems.
if (empty($problems)) {
  print_error('No problems exist!');
}// 99 Problems.

// Load the sort record from the sid in the url, and load the course and cm.
$sort = $DB->get_record('sort',array('id' => $sid));
$course = $DB->get_record('course',array('id' => $sort->course));
if ($course->id) {
    $cm = get_coursemodule_from_instance('sort', $sort->id, $course->id, false, MUST_EXIST);
}
else {
    print_error('Could not find the course / activity!');
}

// Pre-made moodley goodness
require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
add_to_log($course->id, 'sort', 'view', "allscores.php?sid=$sort->id", $USER->username, $cm->id);

/// Print the page header
$PAGE->set_url('/mod/sort/allscores.php', array('sid' => $sort->id));
$PAGE->set_title("My Scores for " . $sort->name);
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->add_body_class('sort-user-scores-grid-view');
$PAGE->requires->css('/mod/sort/css/sort.css');
sort_set_display_type($sort);

// Output starts here
echo $OUTPUT->header();

echo $OUTPUT->heading("My Class Chart for $sort->name");

// Get categories from sort object. 
$categories = sort_get_categories($sort->id, $context);
$num_categories = sizeof($categories);

// Loop through the problems and create an array of their ids
$pids = array();
foreach ($problems as $problem) {
  $pids[] = $problem->id;
}


// Get all student work associated with this problem.
$studentworks = $DB->get_records_list('sort_studentwork', 'pid', $pids, 'name');

// Construct the SQL to look for all classifications by the current user for
// any of the student work in this problem.
$where = "";
foreach($studentworks as $studentwork) {
  if ($where == "") $where .= "swid = $studentwork->id ";
  else $where .= "OR swid = $studentwork->id ";
}
if ($where == "") print_error('No ' . get_string('samplename','sort') . ' found for this problem!');
$where = "(" . $where . ")";
$where .= " AND uid = $USER->id";
$classifications = $DB->get_records_select('sort_classification',$where);


// Loop through the classifications and create arrays of the student work names
// as well as the classifications indexed by the student work name.
$sw_names = array();
$classifications_indexed = array();
foreach ($classifications as $classification) {
  $studentwork = $studentworks[$classification->swid];
  $classifications_indexed[$studentwork->name][$studentwork->pid][$classification->category] = $classification;
  $sw_names[] = $studentwork->name;
}

// Get the unique names - these will be the table rows.
$sw_names = array_unique($sw_names);

// Begin constructing the table.
$table = "
<table class='sort-my-scores-table'>
  <tr>
    <th rowspan='1' class='sort-right-border'></th>";

// Loop through the problems and an overall header for each one.
 foreach ($problems as $problem) {
   $table .= "<th class='sort-left-border sort-right-border sort-table-problem-header' colspan='$num_categories'>$problem->name</th>";
 }
 $table .="</tr><tr> <td class='category'>" . get_string('samplename_caps','sort') . "</td>";  
 
 // Loop through the problems again and make a 2nd header row with the categories
 // for each one.
 foreach ($problems as $problem) {
   $category_counter = 0;
   
   foreach ($categories as $category) {
     if ($category_counter == 0) {
       $class = 'sort-left-border category';
     }
     else if ($category_counter == $num_categories - 1) {
       $class = 'sort-right-border category';
     }
     else {
       $class = 'category';
     }
     $category_counter++;
   $table .= "<td class='$class'>$category->category</td>";
 
   }
 }
 $table .="</tr>";
 
 
 // Sort the student work names to make them appear in alphabetical order.
 // Loop through the names and fill in the rows for each student work name.
sort($sw_names);
foreach ($sw_names as $name) {
  $table .= "<tr>";
  $table .= "<td>$name</td>";
  foreach ($problems as $problpem) {
    $category_counter = 0;
    foreach ($categories as $category) {
      $class = "";
      if ($category_counter == 0) $class = ' class="sort-left-border" ';
      if ($category_counter == sizeof($categories) - 1) $class = ' class="sort-right-border" ';
      if (isset($classifications_indexed[$name][$problem->id][$category->id])) {
        $table .= "<td$class>X</td>";
      }
      else {
        $table .= "<td$class></td>";
      }
      $category_counter++;
    }
  }
}

$table .= "</table>";
echo $table;


// Go back to the sort activity main page.
echo "<div class='sort-action-links'>";
echo "<span><a href='problem.php?id=$pid'>Back to Sort</a></span>";
echo "</div>";  

// Finish the page
echo $OUTPUT->footer();
