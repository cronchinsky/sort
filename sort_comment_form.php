
<?php

require_once($CFG->libdir . "/formslib.php");

class sort_comment_form extends moodleform {
 
    function definition() {
        global $CFG;
 
        $mform =& $this->_form;
        if (isset($this->_customdata['classification']->commenttext)) $comment = array('text' => $this->_customdata['classification']->commenttext);
        else $comment = array('text' => '');
        
        $mform->addElement('editor', 'commenttext', 'Explanation')->setValue($comment);
        $mform->addRule('commenttext', null, 'required', null, 'client');
        $this->add_action_buttons(FALSE, "Save");
        
    }
}                               
