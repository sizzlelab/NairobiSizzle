<?php
class Application_Form_Person_Description extends Zend_Form {
    public function init() {
        $this->setMethod('post')
             ->addElement(Application_Form_Person_Abstract::getDescriptionElement())
             ->addElement(Application_Form_Person_Abstract::getSubmitElement());
    }
}