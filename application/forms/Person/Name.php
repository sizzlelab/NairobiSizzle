<?php
class Application_Form_Person_Name extends Zend_Form {
    public function init() {
        $this->setMethod('post')
             ->addElement(Application_Form_Person_Abstract::getFirstNameElement())
             ->addElement(Application_Form_Person_Abstract::getLastNameElement())
             ->addElement(Application_Form_Person_Abstract::getSubmitElement());
    }
}