<?php

class Application_Form_Recover extends Zend_Form
{

    public function init()
    {
        $this->setMethod('Post');
    	$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Email address')
			->setRequired(true)
			->addFilter('StripTags')
			->addFilter('StringTrim')
			->addValidator('NotEmpty')
			->addValidator(new Zend_Validate_EmailAddress());
		$submit=$this->createElement('submit','submit',array(
						'ignore'=>true,
						'label'=>'Submit'
				));
		$this->addElements(array($email,$submit));
    }


}

