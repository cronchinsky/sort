<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__) . '/sort_category_form.php');

$catid = optional_param('catid', 0, PARAM_INT);
$sid = optional_param('sid', 0, PARAM_INT);

if (!$catid) {
    error('sid not provided or not found.');  
}

$sort  = $DB->get_record('sort', array('id' => $sid), '*', MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $sort->course), '*', MUST_EXIST);
$cm         = get_coursemodule_from_instance('sort', $sort->id, $course->id, false, MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

add_to_log($course->id, 'sort', 'editcategory', "editcategory.php?catid=$catid", $sort->name, $cm->id);

//$DB = new moodle_database();

$category = $DB->get_record('sort_category', array('id' => $catid));



$mform = new sort_category_form("editcategory.php?sid=$sid&catid=$catid");
$results = $mform->get_data();
if ($results) {
  $category->category = $results->category;
  $category->exampletext = $results->exampletext;
  file_save_draft_area_files($results->image, $context->id, 'mod_sort', 'categoryimage', $category->id);
  $DB->update_record('sort_category', $category);
  redirect("managecategories.php?sid=$sid");
      
}

$draftitemid = file_get_submitted_draft_itemid('categoryimage');
file_prepare_draft_area($draftitemid, $context->id, 'mod_sort', 'categoryimage', $category->id, array('subdirs' => 0, 'maxfiles' => 1));
$category->image = $draftitemid;

$mform->set_data($category);
/// Print the page header
$PAGE->set_url('/mod/sort/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($sort->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->requires->css('/mod/sort/css/sort.css');
sort_set_display_type($sort);

echo $OUTPUT->header();
echo "<h2>Editing Category</h2>";
$mform->display();

echo "<div class='sort-action-links'>";
echo "<span class='sort-category-edit-back-link'><a class='sort-category-edit-back-link' href='managecategories.php?sid=$sid'>Back</a></span>";
echo "</div>";
echo $OUTPUT->footer();