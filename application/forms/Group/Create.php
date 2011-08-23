<?php

class Application_Form_Group_Create extends Zend_Form {
    public function init() {
        $this->setMethod('post')
             ->addElement('text', 'title', array(
                 'label'      => 'Title',
                 'filters'    => array('StringTrim'),
                 'required'   => true
             ))
             ->addElement('select', 'type', array(
                 'label'      => 'Group Type',
                 'filters'    => array('StringTrim'),
                 'multiOptions' =>array('open'=>'open','closed'=>'closed'),
                 'required'   => true
             ))
             ->addElement('textarea', 'description', array(
                 'label'      => 'Group Description(optional)',
                 'filters'    => array('StringTrim'),
                 'required'   => false
             ))
            
             ->addElement('submit', 'submit', array(
                 'label'      => 'Create!',
                 'required'   => false,
                 'ignore'     => true
             ))
             ->addElement('hash', 'creategroup', array(
            	 'ignore'     => true,
                 'salt'       => 'creategroup'
             ));
    }
}