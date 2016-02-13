
<?php

require_once($CFG->libdir . "/formslib.php");

class sort_classify_form extends moodleform {
 
    function definition() {
        global $CFG;
 
        $mform =& $this->_form;
        $studentworks = $this->_customdata['studentworks'];
        $sort = $this->_customdata['sort'];
        $categories = $this->_customdata['categories'];
        
        $swids = "";
        foreach ($studentworks as $studentwork) {
          $mform->addElement('hidden','studentwork_classify_' . $studentwork->id, (isset($studentwork->category)) ? $studentwork->category : 0);
          $mform->addElement('hidden','studentwork_comment_' . $studentwork->id, (isset($studentwork->commenttext)) ? $studentwork->commenttext : "Enter an explanation");
          $swids .= $studentwork->id . ",";
        }
        
        $mform->addElement('hidden','swids',$swids);
        $this->add_action_buttons(false);
    }                           
}                               
