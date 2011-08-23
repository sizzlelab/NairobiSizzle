<?php
class Application_Form_Person_Address extends Zend_Form {
    public function init() {
        $this->setMethod('post')
             ->addElement(Application_Form_Person_Abstract::getAddressStreetAddressElement())
             ->addElement(Application_Form_Person_Abstract::getAddressPostalCodeElement())
             ->addElement(Application_Form_Person_Abstract::getAddressLocalityElement())
             ->addElement(Application_Form_Person_Abstract::getSubmitElement());
    }
}