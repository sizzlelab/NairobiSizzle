<?php

class Forums_Form_Login extends Zend_Form
{

    public function init()
    {
    	$this->setName('Login Form')
		     ->setMethod('post');
        $this->addElement('text', 'username', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'User name',
        ));
        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                'Alnum',
            ),
            'required'   => true,
            'label'      => 'Password ',
        ));

        $this->addElement('submit', 'login', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Login',
        ));

        // We want to display a 'failed authentication' message if necessary;
        // we'll do that with the form 'description', so we need to add that
        // decorator.
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form_error')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
    }


}