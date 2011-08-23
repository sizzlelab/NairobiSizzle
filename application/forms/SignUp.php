<?php
class Application_Form_SignUp extends Zend_Form {
    public function init() {
        $this->setMethod('post')
             ->addElement(Application_Form_Person_Abstract::getUsernameElement())
             ->addElement(Application_Form_Person_Abstract::getEmailElement())
             ->addElement(Application_Form_Person_Abstract::getPasswordElement())
             ->addElement(Application_Form_Person_Abstract::getPasswordConfirmElement())
             ->addElement('checkbox', 'consent', array(
                 'label'      => 'I agree to the terms of use',
                 'required'   => true,
                 'decorators' => array(
                     'ViewHelper',
                     array('Label', array('placement' => Zend_Form_Decorator_Abstract::APPEND)),
                     'Description',
                     'Errors',
                     array(array('data' => 'HtmlTag'), array('tag' => 'p'))
                 )
             ))
             ->addElement('submit', 'submit', array(
                 'label'      => 'Sign Up!',
                 'required'   => false,
                 'ignore'     => true
             ))
             ->addElement('hash', 'signup', array(
            	 'ignore'     => true,
                 'salt'       => 'signup'
             ));
        $this->getElement('signup')->getValidator('Identical')->setMessage('Please re-submit the form');
    }
}