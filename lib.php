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
 * Library of interface functions and constants for module sort
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the sort specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod
 * @subpackage sort
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** example constant */
//define('NEWMODULE_ULTIMATE_ANSWER', 42);

////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function sort_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:         return true;
        default:                        return null;
    }
}

/**
 * Saves a new instance of the sort into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $sort An object from the form in mod_form.php
 * @param mod_sort_mod_form $mform
 * @return int The id of the newly inserted sort record
 */
function sort_add_instance(stdClass $sort, mod_sort_mod_form $mform = null) {
    global $DB;

    $sort->timecreated = time();

    # You may have to add extra stuff in here #

    return $DB->insert_record('sort', $sort);
}

/**
 * Updates an instance of the sort in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $sort An object from the form in mod_form.php
 * @param mod_sort_mod_form $mform
 * @return boolean Success/Fail
 */
function sort_update_instance(stdClass $sort, mod_sort_mod_form $mform = null) {
    global $DB;

    $sort->timemodified = time();
    $sort->id = $sort->instance;

    # You may have to add extra stuff in here #

    return $DB->update_record('sort', $sort);
}

/**
 * Removes an instance of the sort from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function sort_delete_instance($id) {
    global $DB;

    if (! $sort = $DB->get_record('sort', array('id' => $id))) {
        return false;
    }

    # Delete any dependent records here #

    $DB->delete_records('sort', array('id' => $sort->id));

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return stdClass|null
 */
function sort_user_outline($course, $user, $mod, $sort) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $sort the module instance record
 * @return void, is supposed to echp directly
 */
function sort_user_complete($course, $user, $mod, $sort) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in sort activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function sort_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link sort_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function sort_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@see sort_get_recent_mod_activity()}

 * @return void
 */
function sort_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function sort_cron () {
    return true;
}

/**
 * Returns an array of users who are participanting in this sort
 *
 * Must return an array of users who are participants for a given instance
 * of sort. Must include every user involved in the instance,
 * independient of his role (student, teacher, admin...). The returned
 * objects must contain at least id property.
 * See other modules as example.
 *
 * @param int $sortid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */
function sort_get_participants($sortid) {
    return false;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function sort_get_extra_capabilities() {
    return array();
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * Is a given scale used by the instance of sort?
 *
 * This function returns if a scale is being used by one sort
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $sortid ID of an instance of this module
 * @return bool true if the scale is used by the given sort instance
 */
function sort_scale_used($sortid, $scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('sort', array('id' => $sortid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of sort.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param $scaleid int
 * @return boolean true if the scale is used by any sort instance
 */
function sort_scale_used_anywhere($scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('sort', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the give sort instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $sort instance object with extra cmidnumber and modname property
 * @return void
 */
function sort_grade_item_update(stdClass $sort) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $item = array();
    $item['itemname'] = clean_param($sort->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax']  = $sort->grade;
    $item['grademin']  = 0;

    grade_update('mod/sort', $sort->course, 'mod', 'sort', $sort->id, 0, null, $item);
}

/**
 * Update sort grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $sort instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 * @return void
 */
function sort_update_grades(stdClass $sort, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $grades = array(); // populate array of grade objects indexed by userid

    grade_update('mod/sort', $sort->course, 'mod', 'sort', $sort->id, 0, $grades);
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function sort_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * Serves the files from the sort file areas
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return void this should never return to the caller
 */
function sort_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);

	$fs = get_file_storage();
    $relativepath = implode('/', $args);
	$fullpath = "/$context->id/mod_sort/$filearea/$relativepath";
	
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        send_file_not_found();
    }

    // send the file
    send_stored_file($file, 0, 0, false); // download MUST be forced - security!
    									  // CR turned forced download off.
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding sort nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the sort module instance
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
function sort_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm) {
}

/**
 * Extends the settings navigation with the sort settings
 *
 * This function is called when the context for the page is a sort module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $sortnode {@link navigation_node}
 */
function sort_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $sortnode=null) {
}


// ETLO Additions

/**
 * Returns a URL to an image file given a DB record for the file.
 */
function sort_get_image_file_url($record) {
  global $CFG;
  
  return $CFG->wwwroot . "/pluginfile.php/$record->contextid/$record->component/$record->filearea/$record->itemid$record->filepath$record->filename"; 
}


/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See {@link get_array_of_activities()} in course/lib.php
 *
 * @param object $coursemodule
 * @return object info
 */
function sort_get_coursemodule_info($coursemodule) {
    global $CFG;
    global $DB;
    require_once("$CFG->libdir/resourcelib.php");

 if (!$sort = $DB->get_record('sort', array('id'=>$coursemodule->instance))) {    
    return NULL;
 }
    
    $info = new stdClass();
    $info->name = $sort->name;
	
    if ($sort->display != RESOURCELIB_DISPLAY_POPUP) {
        return $info;
    }
    $fullurl = "$CFG->wwwroot/mod/sort/view.php?id=$coursemodule->id&amp;inpopup=1";
    $width  = empty($sort->popupwidth)  ? 620 : $sort->popupwidth;
    $height = empty($sort->popupheight) ? 450 : $sort->popupheight;
    $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
    $info->extra = "onclick=\"window.open('$fullurl', '', '$wh'); return false;\"";
    return $info;
}


function sort_set_display_type($sort) {
  global $CFG;
  require_once("$CFG->libdir/resourcelib.php");
  
  switch ($sort->display) {
    case RESOURCELIB_DISPLAY_EMBED:
      break;
    default:
      global $PAGE;
      $PAGE->set_pagelayout('popup');
      break;
  }
} 


/**
 * Debugger.
 */
function etlo_debug($variables) {
  echo "<pre>" . var_export($variables,TRUE) . "</pre>";
}