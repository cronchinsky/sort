<?php

require_once($CFG->libdir . "/formslib.php");

class sort_new_problem_form extends moodleform {
 
    function definition() {
        global $CFG;
 
        $mform =& $this->_form; // Don't forget the underscore!
        $sort = $this->_customdata['sort'];

        $mform->addElement('text', 'problemname', get_string('problemname', 'sort'));
        $mform->addRule('problemname', 'This field is required', 'required');
        
        $mform->addElement('text', 'category_1_oldtotal', 'Cumulative Total for ' . $sort->category_1);
        $mform->addRule('category_1_oldtotal', 'This field is required', 'required');
        
        $mform->addElement('text', 'category_2_oldtotal', 'Cumulative Total for ' . $sort->category_2);
        $mform->addRule('category_2_oldtotal', 'This field is required', 'required');
        
        $mform->addElement('text', 'category_3_oldtotal', 'Cumulative Total for ' . $sort->category_3);
        $mform->addRule('category_3_oldtotal', 'This field is required', 'required');
        
        $mform->addElement('text', 'category_4_oldtotal', 'Cumulative Total for ' . $sort->category_4);
        $mform->addRule('category_4_oldtotal', 'This field is required', 'required');
        
        $this->add_action_buttons();
    }                           
}                               