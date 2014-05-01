<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__) . '/sort_grade_form.php');
$id = optional_param('id', 0, PARAM_INT); // course_module ID, or

$cm         = get_coursemodule_from_id('sort', $id, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$sort  = $DB->get_record('sort', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

//Get users enrolled in the course.
$enrols = $DB->get_records('enrol',array('courseid' => $course->id));
$enrolids = array_keys($enrols);
$enrolments = $DB->get_records_list('user_enrolments','enrolid', $enrolids);
$userids = array();
foreach ($enrolments as $enrolment) {
  $userids[] = $enrolment->userid;
}
$users = $DB->get_records_list('user','id', $userids);


// Get counts of sort records for each student and apply them to the $users array
$problems = $DB->get_records('sort_problem', array('sid' => $sort->id));
$studentworks = $DB->get_records_list('sort_studentwork', 'pid', array_keys($problems));
$classifications = $DB->get_records_list('sort_classification', 'swid', array_keys($studentworks));

$mform = new sort_grade_form("/mod/sort/setgrades.php?id={$cm->id}", array('problems' => $problems, 'studentworks' => $studentworks, 'classifications' => $classifications, 'users' => $users));
if ($results = $mform->get_data()) {
  
}

add_to_log($course->id, 'sort', 'view', "setgrades.php?id={$cm->id}", $sort->name, $cm->id);

$PAGE->set_url('/mod/sort/setgrades.php', array('id' => $cm->id));
$PAGE->set_title(format_string('Setting Grades for ' . $sort->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->requires->css('/mod/sort/css/sort.css');

echo $OUTPUT->header();

   foreach ($classifications as $classification) {
      if (isset($users[$classification->uid])) {
        $users[$classification->uid]->classifications +=1;
        if (!is_null($classification->commenttext)) {
          $users[$classification->uid]->comments += 1;
        }
      }
    }

    foreach ($users as $user) {
      echo '<div class="sort-grade-user-box">';
      echo "<h3 class='sort-grade-user-box-header'>$user->username</h3>";
      echo "# of Classifications: $user->classifications";
      echo "<br /># of Comments: $user->comments";
      echo '</div>';
    }
    
    echo "<div class='sort-action-links'>";
    echo "<span><a href='/grade/report/grader/index.php?id=$course->id'>Back to the Course Grading Page</a></span>";
    echo "</div>";

echo $OUTPUT->footer();