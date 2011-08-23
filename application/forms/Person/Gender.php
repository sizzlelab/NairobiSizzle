<?php
class Application_Form_Person_Gender extends Zend_Form {
    public function init() {
        $this->setMethod('post')
             ->addElement(Application_Form_Person_Abstract::getGenderElement())
             ->addElement(Application_Form_Person_Abstract::getSubmitElement());
    }
}