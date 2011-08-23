<?php
class Application_Form_Person_Password extends Zend_Form {
    public function init() {
        $this->setMethod('post');
        $this->addElement(Application_Form_Person_Abstract::getPasswordOldElement())
             ->addElement(Application_Form_Person_Abstract::getPasswordElement('New password'))
             ->addElement(Application_Form_Person_Abstract::getPasswordConfirmElement('Repeat new password'))
             ->addElement(Application_Form_Person_Abstract::getSubmitElement())
             ->setDecorators(array(
                'FormElements',
                'Form'
        ));
    }
}