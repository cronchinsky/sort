<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__) . '/sort_category_form.php');

$sid = optional_param('sid', 0, PARAM_INT); // course_module ID, or
$deleteid = optional_param('deleteid', 0, PARAM_INT);

if (!$sid) {
    error('sid not provided or not found.');  
}

$sort  = $DB->get_record('sort', array('id' => $sid), '*', MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $sort->course), '*', MUST_EXIST);
$cm         = get_coursemodule_from_instance('sort', $sort->id, $course->id, false, MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

add_to_log($course->id, 'sort', 'managecategories', "managecategories.php?id={$cm->id}", $sort->name, $cm->id);



/// Print the page header
$PAGE->set_url('/mod/sort/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($sort->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->requires->css('/mod/sort/css/sort.css');
sort_set_display_type($sort);

$mform = new sort_category_form("managecategories.php?sid=$sid");

//$DB = new moodle_database();
if ($deleteid) {
  $DB->delete_records('sort_category', array('id' => $deleteid));
}

$results = $mform->get_data();
if ($results) {
  $results->sid = $sid;
  $newid = $DB->insert_record('sort_category', $results);
  file_save_draft_area_files($results->image, $context->id, 'mod_sort', 'categoryimage', $newid);
}


$categories = $DB->get_records('sort_category', array('sid' => $sid));



echo $OUTPUT->header();
echo "<h2>Manage Categories</h2>";
if (!$categories) {
  echo "<p>This Activity Has No Categories.</p>";
}
else {
  foreach ($categories as $category) {
    echo "<div class='sort-category-info'>";
      echo "<h3 class='sort-category-info-name'>$category->category</h3>";
      echo "<p class='sort-category-info-exampletext'>$category->exampletext</p>";
      echo "<a href='managecategories.php?deleteid=$category->id&sid=$sid'>Delete</a> | <a href='editcategory.php?catid=$category->id&sid=$sid'>Edit</a>";
    echo "</div>";
  }

  
    
}
if ($categories) {
  echo "<div class='sort-action-links'>";
    echo "<span><a href='view.php?s=$sid'>Sort menu</a></span>";
  echo "</div>";
  
} 

echo "<div class='sort-category-add-box'>";
echo "<h3>Add a New Category</h3>";
    $mform->display();
  
  echo "</div>";
echo $OUTPUT->footer();