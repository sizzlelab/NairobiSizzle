<?php
class Application_Form_Person_Username extends Zend_Form {
    public function init() {
        $this->setMethod('post');
        $this->addElement(Application_Form_Person_Abstract::getUsernameElement())
             ->addElement(Application_Form_Person_Abstract::getSubmitElement())
             ->setDecorators(array(
                'FormElements',
                'Form'
        ));
    }
}