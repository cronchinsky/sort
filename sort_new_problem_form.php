<?php

require_once($CFG->libdir . "/formslib.php");

class sort_new_problem_form extends moodleform {
 
    function definition() {
        global $CFG;
 
        $mform =& $this->_form; // Don't forget the underscore! 
        
        $mform->addElement('text', 'problemname', get_string('problemname', 'sort'));
        $mform->addRule('problemname', 'This field is required', 'required');
        $this->add_action_buttons();
    }                           
}                               