<?php

class Application_Form_Login extends Zend_Form
{

    public function __construct($captcha)
    {
    	parent::__construct($captcha);
    	$this->setName('Login Form')
		     ->setMethod('post');
		     
    	$this->addElement('text', 'username', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Username',
        ));
        $this->addElement('password', 'password', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Password ',
        ));
        
        /**
         * add a captcha field if retries greater than three
         */
        if($captcha){        	
        	$captcha = new Zend_Form_Element_Captcha(  
         		'captcha',   
         		array('label' => 'Please confirm you are human',  
         			'captcha' => array(
         				'captcha' => 'figlet',
         				'wordLen' => 4,
         				'timeout' => 300,  
 				)));
 			$this->addElement($captcha);
        }
        
        

        $this->addElement('checkbox', 'remember', array(
                 'label'      => 'Remember me',
                 'decorators' => array(
                     'ViewHelper',
                     array('Label', array('placement' => Zend_Form_Decorator_Abstract::APPEND)),
                     'Description',
                     'Errors',
                     array(array('data' => 'HtmlTag'), array('tag' => 'p'))
                 )
             ));
             
        $this->addElement('submit', 'login', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Login',
        ));

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form_error')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
    }


}
