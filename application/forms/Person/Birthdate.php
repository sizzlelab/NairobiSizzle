<?php
class Application_Form_Person_Birthdate extends Zend_Form {
    public function init() {
        $this->setMethod('post')
             ->addElement(Application_Form_Person_Abstract::getBirthdateDayElement())
             ->addElement(Application_Form_Person_Abstract::getBirthdateMonthElement())
             ->addElement(Application_Form_Person_Abstract::getBirthdateYearElement())
             ->addElement(Application_Form_Person_Abstract::getSubmitElement());
    }
}