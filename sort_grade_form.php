
<?php

require_once($CFG->libdir . "/formslib.php");

class sort_grade_form extends moodleform {

  function definition() {
    global $CFG;
    $mform = & $this->_form; // Don't forget the underscore! 

    $users = $this->_customdata['users'];
    $problems = $this->_customdata['problems'];
    $studentworks = $this->_customdata['studentworks'];
    $classifications = $this->_customdata['classifications'];

    foreach ($users as $user) {
      $user->classifications = 0;
      $user->comments = 0;
    }


    foreach ($classifications as $classification) {
      if (isset($users[$classification->uid])) {
        $users[$classification->uid]->classifications +=1;
        if (!is_null($classification->commenttext)) {
          $users[$classification->uid]->comments += 1;
        }
      }
    }

    foreach ($users as $user) {
      $mform->addElement('html','<div class="sort-grade-user-box">');
      $mform->addElement('html',"<h3 class='sort-grade-user-box-header'>$user->username</h3>");
      $mform->addElement('html', "# of Classifications: $user->classifications");
      $mform->addElement('html', "<br /># of Comments: $user->comments");
      $mform->addElement('text', 'usergrade_' . $user->id, "Grade:");
      $mform->addElement('html','</div>');
    }
    
    $this->add_action_buttons(false);
  }

}

