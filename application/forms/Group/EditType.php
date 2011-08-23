<?php

class Application_Form_Group_EditType extends Zend_Form {

    public function init() {
        $this->setMethod('post')
             
             ->addElement('select', 'type', array(
                 'label'      => 'Group Type',
                 'filters'    => array('StringTrim'),
                 'multiOptions' =>array('open'=>'open','closed'=>'closed'),
                 'required'   => true
             ))
             
             ->addElement('submit', 'submit', array(
                 'label'      => 'Edit!',
                 'required'   => false,
                 'ignore'     => true
             ))
             ->addElement('hash', 'edittype', array(
            	 'ignore'     => true,
                 'salt'       => 'edittype'
             ));
    }
}
