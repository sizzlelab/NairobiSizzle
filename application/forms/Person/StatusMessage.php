<?php
class Application_Form_Person_StatusMessage extends Zend_Form {
    public function init() {
        $this->setMethod('post');
        $submit = Application_Form_Person_Abstract::getSubmitElement()->setLabel('Update status');
        $this->addElement(Application_Form_Person_Abstract::getStatusMessageElement())
             ->addElement($submit)
             ->setDecorators(array(
                'FormElements',
                'Form'
        ));
    }
}