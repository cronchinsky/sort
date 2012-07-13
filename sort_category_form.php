<?php

require_once($CFG->libdir . "/formslib.php");

class sort_category_form extends moodleform {
 
    function definition() {
        global $CFG;
        $mform =& $this->_form;
        
        $mform->addElement('text', 'category', 'Category Name');
        $mform->addRule('category', 'This field is required', 'required');
        $mform->addElement('filemanager', 'image', 'Category Example Image', null,
                    array('subdirs' => 0, 'maxbytes' => 33554432, 'maxfiles' => 1,
                          ));
        
        $mform->addElement('textarea', 'exampletext', 'Example Explanation');
        $mform->addRule('exampletext','This field is required','required');
        
        $this->add_action_buttons(FALSE, "Save");
        
    }
    
    public function data_preprocessing(&$data)  {
      if ($this->current->instance) {
        $itemid = file_get_submitted_draft_itemid('image');
        file_prepare_draft_area($itemid, $context->id, 'mod_sort', 'categoryimage', $category->id, array('subdirs' => 0, 'maxfiles' => 1));
      }
    }
}  