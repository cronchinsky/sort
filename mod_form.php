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
 * The main sort configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod
 * @subpackage sort
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/annotate/locallib.php');


/**
 * Module instance settings form
 */
class mod_sort_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
    
    	global $CFG, $DB;

        $mform = $this->_form;
        
        $config = get_config('sort');

        //-------------------------------------------------------------------------------
        // Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('sortname', 'sort'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'sortname', 'sort');

        // Adding the standard "intro" and "introformat" fields
        $this->add_intro_editor();
        //-------------------------------------------------------------------------------
        // Adding the rest of sort settings, spreeading all them into this fieldset
        // or adding more fieldsets ('header' elements) if needed for better logic
          $mform->addElement('header', 'sort-categories-fieldset-header sort-category-1', 'Category 1');
          
          $mform->addElement('text', 'category_1', get_string('category1', 'sort'));
          $mform->addRule('category_1', 'This field is required', 'required');
          $mform->addElement('filemanager', 'category_1_exampleimage', 'Category 1 Example Image', null,
                    array('subdirs' => 0, 'maxbytes' => 33554432, 'maxfiles' => 1,
                          ));
          $mform->addRule('category_1_exampleimage', 'This field is required', 'required');
          $mform->addElement('textarea', 'category_1_exampletext', 'Example Explanation');
          $mform->addRule('category_1_exampletext','This field is required','required');
          
          $mform->addElement('header', 'sort-categories-fieldset-header sort-category-2', 'Category 2');
          $mform->addElement('text', 'category_2', get_string('category2', 'sort'));
          $mform->addRule('category_2', 'This field is required', 'required');
          $mform->addElement('filemanager', 'category_1_exampleimage', 'Category 2 Example Image', null,
                    array('subdirs' => 0, 'maxbytes' => 33554432, 'maxfiles' => 1,
                          ));
          $mform->addRule('category_2_exampleimage', 'This field is required', 'required');
          $mform->addElement('textarea', 'category_2_exampletext', 'Example Explanation');
          $mform->addRule('category_2_exampletext','This field is required','required');
          
          $mform->addElement('header', 'sort-categories-fieldset-header sort-category-3', 'Category 3');
          $mform->addElement('text', 'category_3', get_string('category3', 'sort'));
          $mform->addRule('category_3', 'This field is required', 'required');
          $mform->addElement('filemanager', 'category_3_exampleimage', 'Category 3 Example Image', null,
                    array('subdirs' => 0, 'maxbytes' => 33554432, 'maxfiles' => 1,
                          ));
          $mform->addRule('category_3_exampleimage', 'This field is required', 'required');
          $mform->addElement('textarea', 'category_3_exampletext', 'Example Explanation');
          $mform->addRule('category_3_exampletext','This field is required','required');
          
        $mform->addElement('header', 'sort-categories-fieldset-header sort-category-4', 'Category 4');
          $mform->addElement('text', 'category_4', get_string('category4', 'sort'));
          $mform->addRule('category_4', 'This field is required', 'required');
          $mform->addElement('filemanager', 'category_4_exampleimage', 'Category 4 Example Image', null,
                    array('subdirs' => 0, 'maxbytes' => 33554432, 'maxfiles' => 1,
                          ));
          $mform->addRule('category_4_exampleimage', 'This field is required', 'required');
          $mform->addElement('textarea', 'category_4_exampletext', 'Example Explanation');
          $mform->addRule('category_4_exampletext','This field is required','required');
    
    
    	//----------------------------------------------------------------------------------
 		 $mform->addElement('header', 'optionssection', 'Display options');
        
        if ($this->current->instance) {
            $options = resourcelib_get_displayoptions(explode(',', $config->displayoptions), $this->current->display);
        } else {
            $options = resourcelib_get_displayoptions(explode(',', $config->displayoptions));
        }

        if (count($options) == 1) {
            $mform->addElement('hidden', 'display');
            $mform->setType('display', PARAM_INT);
            reset($options);
            $mform->setDefault('display', key($options));
        } else {
            $mform->addElement('select', 'display', 'Display Type', $options);
            $mform->setDefault('display', $config->display);
            $mform->setAdvanced('display', $config->display_adv);
        }

        if (array_key_exists(RESOURCELIB_DISPLAY_POPUP, $options)) {
            $mform->addElement('text', 'popupwidth', 'Pop-Up Width (in pixels)', array('size'=>3));
            if (count($options) > 1) {
                $mform->disabledIf('popupwidth', 'display', 'noteq', RESOURCELIB_DISPLAY_POPUP);
            }
            $mform->setType('popupwidth', PARAM_INT);
            $mform->setDefault('popupwidth', $config->popupwidth);
            $mform->setAdvanced('popupwidth', $config->popupwidth_adv);

            $mform->addElement('text', 'popupheight', 'Pop-Up Height (in pixels)', array('size'=>3));
            if (count($options) > 1) {
                $mform->disabledIf('popupheight', 'display', 'noteq', RESOURCELIB_DISPLAY_POPUP);
            }
            $mform->setType('popupheight', PARAM_INT);
            $mform->setDefault('popupheight', $config->popupheight);
            $mform->setAdvanced('popupheight', $config->popupheight_adv);
        }

       
		//------------------------------------------------------------------------------------------------
        


        
        //-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();
        //-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();
    }
    
    
    
  public function data_preprocessing(&$data) {
     if ($this->current->instance) {


      $draftitemid1 = file_get_submitted_draft_itemid('category_1_exampleimage');
      $draftarea1 = file_prepare_draft_area($draftitemid1, $this->context->id, 'mod_sort', 'categoryimages', 0);

      
     }
  }
  




}
