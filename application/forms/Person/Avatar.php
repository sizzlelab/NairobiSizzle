<?php
class Application_Form_Person_Avatar extends Zend_Form {
    public function init() {
        $this->setMethod('post')
             ->setAttrib('enctype', 'multipart/form-data')
             ->addElement(Application_Form_Person_Abstract::getAvatarElement())
             ->addElement(Application_Form_Person_Abstract::getSubmitElement())
             ->addElement(Application_Form_Person_Abstract::getHashElement('avatar_upload'));
    }
}