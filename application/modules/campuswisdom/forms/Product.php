<?php

class campuswisdom_Form_Product extends Zend_Form
{

    public function __construct($bizid=null)
    {
    	parent::__construct($bizid);
    	$this->setMethod('Post');
        $productName = new Zend_Form_Element_Text('name');
		$productName->setLabel('Product/Service name')
					->setRequired(true)
					->addFilter('StripTags')
					->addFilter('StringTrim')
					->addValidator('NotEmpty');
					
		$price = new Zend_Form_Element_Text('price');
		$price->setLabel('Price in Kshs')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim')
				->addValidator(new Zend_Validate_Digits());
		
		$description = new Zend_Form_Element_Textarea('description');
		$description->setLabel('Description')
					->setRequired(true)
					->addFilter('StripTags')
					->addFilter('StringTrim');
					
		$list = new campuswisdom_Model_DbTable_Categories();
    	$categories = $list->getAll();
    	array_unshift($categories, array('key'=>'', 'value'=>''));
    	
    	$category=new Zend_Form_Element_Select('category_id');
		$category->setRequired(true)
				->setLabel('Category')
				->addFilter('StripTags')
				 ->addMultiOptions($categories)
				 ->addFilter('StringTrim')
				 ->addValidator('NotEmpty');
				 
		 $date_added = new Zend_Form_Element_Hidden('date_added');
		 $date_added->removeDecorator('label');
		 $date = new Zend_Date();
		 $date_added->setValue($date->toString('yyyy-MM-dd hh:mm:ss'));
		 
		 $business_id = new Zend_Form_Element_Hidden('business_id');
		 $business_id->removeDecorator('label');
		 $business_id->setValue($bizid)
		 			->setRequired(true);
		 
		 $added_by = new Zend_Form_Element_Hidden('added_by');
		 $added_by->removeDecorator('label');
		 $session = new Application_Model_Session();
		 $session->startSession();
		 $user_id = $session->getUserId();
		 $added_by->setValue($user_id);
		 
    	 /**$product_id = new Zend_Form_Element_Hidden('id');
		 $product_id->removeDecorator('label');
		 $id = $product_id->getValue();
		 if(!empty($id)){
		 	$this->addElement($product_id);
		 }
		 */
		
		 $submit=$this->createElement('submit','submit',array(
						'ignore'=>true,
						'label'=>'OK'
				));
		 
		 $this->addElements(array($productName, $price, $description, $category, $date_added, $added_by, $business_id, $submit));
    }


}

