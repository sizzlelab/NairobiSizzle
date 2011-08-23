<?php

class campuswisdom_Form_Classified extends Zend_Form
{

    public function init()
    {
        $this->setMethod('Post');
        $title = new Zend_Form_Element_Text('title');
		$title->setLabel('Title')
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
					
		$price = new Zend_Form_Element_Text('price');
		$price->setLabel('Price')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator('NotEmpty');
		
					
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
		 
		 $added_by = new Zend_Form_Element_Hidden('added_by');
		 $added_by->removeDecorator('label');
		 $session = new Application_Model_Session();
		 $session->startSession();
		 $user_id = $session->getUserId();
		 $added_by->setValue($user_id);
		 
		 $option = array('0'=>'No','1'=>'Yes');
		 
		 $auction=new Zend_Form_Element_Select('to_auction');
		 $auction->setLabel('Place on auction')
				->setRequired(true)
				->addFilter('StripTags')
				 ->addMultiOptions($option)
				 ->addFilter('StringTrim')
				 ->addValidator('NotEmpty');
		  
		 $submit=$this->createElement('submit','submit',array(
						'ignore'=>true,
						'label'=>'OK'
				));
		 
		 $this->addElements(array($title, $location, $price, $description, $category, $date_added, $added_by, $auction, $submit));
		 
		 

    }


}

