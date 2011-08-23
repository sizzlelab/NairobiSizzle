<?php
class Application_Form_Person_Website extends Zend_Form {
    public function init() {
        $this->setMethod('post')
             ->addElement(Application_Form_Person_Abstract::getWebsiteElement())
             ->addElement(Application_Form_Person_Abstract::getSubmitElement());
    }
}