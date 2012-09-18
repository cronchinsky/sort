
<?php

require_once($CFG->libdir . "/formslib.php");

class sort_new_studentwork_form extends moodleform {
 
    function definition() {
        global $CFG;
 
        $mform =& $this->_form; // Don't forget the underscore! 
        
        $mform->addElement('text', 'studentworkname', 'Name');
        $mform->addRule('studentworkname', 'This field is required', 'required');
        
        $mform->addElement('filemanager', 'attachments', 'Student work image', null,
                    array('subdirs' => 0, 'maxbytes' => 33554432, 'maxfiles' => 1,
                          ));
        $mform->addRule('attachments', 'This field is required', 'required');
        $this->add_action_buttons();
    }                           
}                               
