
<?php

require_once($CFG->libdir . "/formslib.php");

class sort_new_studentwork_form extends moodleform {
 
    function definition() {
        global $CFG;
 
        $mform =& $this->_form; // Don't forget the underscore! 
        
        $studentworks = $this->_customdata['studentworks'];
        $this_studentwork = $this->_customdata['this_studentwork'];
        $sort = $this->_customdata['sort'];
        $problem = $this->_customdata['problem'];
        
        if ($sort->custom_labels) {
	        $mform->addElement('text', 'studentworkname', 'Name');
        } else {
	        $letters = range('A','Z');
	        $options = array_combine($letters,$letters);
	        
	        foreach ($studentworks as $studentwork) {
	          if (isset($options[$studentwork->name]) && (isset($this_studentwork) && $this_studentwork->name != $studentwork->name) || !isset($this_studentwork)) {
	              unset($options[$studentwork->name]);
	          }
	        }
	
	        $mform->addElement('select', 'studentworkname', 'Name', $options);
        }
        $mform->addRule('studentworkname', 'This field is required', 'required');
        
        $mform->addElement('filemanager', 'attachments', 'sample image', null,
                    array('subdirs' => 0, 'maxbytes' => 33554432, 'maxfiles' => 1,
                          ));
        $mform->addRule('attachments', 'This field is required', 'required');
        
        //$DB = new moodle_database();
        global $DB;
        $categories = $DB->get_records('sort_category', array('sid' => $sort->id));
        $options = array(
          '0' => '- None -',
        );
        foreach ($categories as $id => $category) {
          $options[$id] = $category->category;
        }
        
        if ($sort->has_correct) $mform->addElement('select','correct_answer','Correct Answer', $options);
        else $mform->addElement('hidden','correct_answer','0');
        $this->add_action_buttons(false);
    }                           
}                               
