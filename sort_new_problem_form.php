<?php

require_once($CFG->libdir . "/formslib.php");
class sort_new_problem_form extends moodleform {
 
    function definition() {
        global $CFG;
        global $DB;
        
        
        $mform =& $this->_form; // Don't forget the underscore!
        $sort = $this->_customdata['sort'];
        $categories = sort_get_categories($sort->id, $this->_customdata['context']);

        $mform->addElement('text', 'problemname', get_string('problemname', 'sort'));
        $mform->addRule('problemname', 'This field is required', 'required');
        foreach ($categories as $key => $category) {
          $mform->addElement('text',"category_oldtotal_$key", "Previous Total for $category->category");
          $mform->addRule("category_oldtotal_$key", 'This field is required', 'required');
        }
        
        
        $this->add_action_buttons();
    }                           
}                               