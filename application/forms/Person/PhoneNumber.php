<?php
class Application_Form_Person_PhoneNumber  extends Zend_Form {
    public function init() {
        $this->setMethod('post')
             ->addElement(Application_Form_Person_Abstract::getPhoneNumberElement())
             ->addElement(Application_Form_Person_Abstract::getSubmitElement());
    }
}