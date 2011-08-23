<?php

class Application_Form_Group_EditDesc extends Zend_Form {

    public function init() {
        $this->setMethod('post')
              
             ->addElement('textarea', 'description', array(
                 'label'      => 'Group Description',
                 'filters'    => array('StringTrim'),
                 'required'   => false
             ))
            
             ->addElement('submit', 'submit', array(
                 'label'      => 'Edit!',
                 'required'   => false,
                 'ignore'     => true
             ))
             ->addElement('hash', 'editdesc', array(
            	 'ignore'     => true,
                 'salt'       => 'editdesc'
             ));
    }
}