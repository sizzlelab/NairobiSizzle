<?php

class Application_Form_Group_EditTitle extends Zend_Form {

    public function init() {
        $this->setMethod('post')
             ->addElement('text', 'title', array(
                 'label'      => 'Title',
                 'filters'    => array('StringTrim'),
                 'required'   => true
             ))
             
             ->addElement('submit', 'submit', array(
                 'label'      => 'Edit!',
                 'required'   => false,
                 'ignore'     => true
             ))
             ->addElement('hash', 'edittitle', array(
            	 'ignore'     => true,
                 'salt'       => 'edittitle'
             ));
    }
}
