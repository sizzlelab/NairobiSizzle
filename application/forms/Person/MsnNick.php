<?php
class Application_Form_Person_MsnNick extends Zend_Form {
    public function init() {
        $this->setMethod('post')
             ->addElement(Application_Form_Person_Abstract::getMsnNickElement())
             ->addElement(Application_Form_Person_Abstract::getSubmitElement());
    }
}