<?php
class Application_Form_Person_IrcNick extends Zend_Form {
    public function init() {
        $this->setMethod('post')
             ->addElement(Application_Form_Person_Abstract::getIrcNickElement())
             ->addElement(Application_Form_Person_Abstract::getSubmitElement());
    }
}