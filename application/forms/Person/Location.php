<?php
class Application_Form_Person_Location extends Zend_Form {
    public function init() {
        $this->setMethod('post')
             ->addElement(Application_Form_Person_Abstract::getLocationLabelElement())
             ->addElement(Application_Form_Person_Abstract::getSubmitElement())
             ->addElement(Application_Form_Person_Abstract::getHashElement('person_location'));
    }
}