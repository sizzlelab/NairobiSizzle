<?php

class campuswisdom_Form_Business extends Zend_Form
{

    public function init()
    {
    	$this->setMethod('Post');
        $businessName = new Zend_Form_Element_Text('name');
		$businessName->setLabel('Business name')
					->setRequired(true)
					->addFilter('StripTags')
					->addFilter('StringTrim')
					->addValidator('NotEmpty');
					
		$location = new Zend_Form_Element_Text('location');
		$location->setLabel('Location')
					->setRequired(true)
					->addFilter('StripTags')
					->addFilter('StringTrim')
					->addValidator('NotEmpty');
					
		$email = new Zend_Form_ELement_Text('email');
		$email->setLabel('Email address')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator(new Zend_Validate_EmailAddress());
				
		$mobile = new Zend_Form_Element_Text('mobile');
		$mobile->setLabel('Mobile no')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator(new Zend_Validate_GreaterThan(10))
				->addValidator(new Zend_Validate_Digits());
		
		$landline = new Zend_Form_Element_Text('landline');
		$landline->setLabel('Landline')
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator(new Zend_Validate_GreaterThan(6))
				->addValidator(new Zend_Validate_Digits());
				
		$address = new Zend_Form_Element_Text('address');
		$address->setLabel('Physical address')
				->addFilter('StripTags')
				->addFilter('StringTrim');
		
		//$logo = new Zend_Form_Element_Image('logo');
		//$logo->setLabel('Logo');
		
		$description = new Zend_Form_Element_Textarea('description');
		$description->setLabel('Description')
					->setRequired(true)
					->addFilter('StripTags')
					->addFilter('StringTrim');
					
		$list = new campuswisdom_Model_DbTable_Categories();
    	$categories = $list->getAll();
    	array_unshift($categories, array('key'=>'', 'value'=>''));
    	
    	$category=new Zend_Form_Element_Select('category_id');
		$category->setLabel('Category')
				->setRequired(true)
				->addFilter('StripTags')
				 ->addMultiOptions($categories)
				 ->addFilter('StringTrim')
				 ->addValidator('NotEmpty');
				 
		 $date_added = new Zend_Form_Element_Hidden('date_added');
		 $date_added->removeDecorator('label');
		 $date = new Zend_Date();
		 $date_added->setValue($date->toString('yyyy-MM-dd hh:mm:ss'));
		 
		 $added_by = new Zend_Form_Element_Hidden('added_by_id');
		 $added_by->removeDecorator('label');
		 $session = new Application_Model_Session();
		 $session->startSession();
		 $user_id = $session->getUserId();
		 $added_by->setValue($user_id);
		 
		 /**$business_id = new Zend_Form_Element_Hidden('id');
		 $business_id->removeDecorator('label');
		 
		 $id = $business_id->getValue();
		 if(!empty($id)){
		 	$this->addElement($business_id);
		 }*/
		  
		 $submit=$this->createElement('submit','submit',array(
						'ignore'=>true,
						'label'=>'OK'
				));
		 
		 $this->addElements(array($businessName, $location, $email, $mobile, $landline, $address, $description, $category, $date_added, $added_by, $submit));
		 
		 
    }


}

