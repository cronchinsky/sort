
<?php

require_once($CFG->libdir . "/formslib.php");

class sort_classify_form extends moodleform {
 
    function definition() {
        global $CFG;
 
        $mform =& $this->_form;
        $studentworks = $this->_customdata['studentworks'];
        $sort = $this->_customdata['sort'];
        $categories = array(
          '0' => 'None',
          '1' => $sort->category_1,
          '2' => $sort->category_2,
          '3' => $sort->category_3,
          '4' => $sort->category_4,
        );
        
        $swids = "";
        foreach ($studentworks as $studentwork) {
          $mform->addElement('hidden','studentwork_classify_' . $studentwork->id, (isset($studentwork->category)) ? $studentwork->category : 0);
          $swids .= $studentwork->id . ",";
        }
        
        $mform->addElement('hidden','swids',$swids);
        $this->add_action_buttons();
    }                           
}                               
