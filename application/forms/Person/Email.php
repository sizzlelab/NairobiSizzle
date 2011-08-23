<?php
class Application_Form_Person_Email extends Zend_Form {
    public function init() {
        $this->setMethod('post')
             ->addElement(Application_Form_Person_Abstract::getEmailElement())
             ->addElement(Application_Form_Person_Abstract::getSubmitElement());
    }
}