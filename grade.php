<?php
require_once("../../config.php");
$id   = required_param('id', PARAM_INT); // Course module ID
$PAGE->set_url('/mod/sort/grade.php', array('id'=>$id));

if (! $cm = get_coursemodule_from_id('sort', $id)) {
    print_error('invalidcoursemodule');
}

if (! $sort = $DB->get_record("sort", array("id"=>$cm->instance))) {
    print_error('invalidid', 'sort');
}

if (! $course = $DB->get_record("course", array("id"=>$sort->course))) {
    print_error('coursemisconf', 'sort');
}

require_login($course, false, $cm);

//if (has_capability('mod/sort:grade', get_context_instance(CONTEXT_MODULE, $cm->id))) {
//    redirect('report.php?id='.$cm->id);
//} else {
    redirect('view.php?id='.$cm->id);
//}