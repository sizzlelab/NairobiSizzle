<?php

class campuswisdom_Form_Bid extends Zend_Form
{

	public function __construct($classid, $user_id){
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
		$this->setMethod('Post');
		parent::__construct($classid);
		$date_added = new Zend_Form_Element_Hidden('date_added');
		$date_added->removeDecorator('label');
		$date = new Zend_Date();
		$date_added->setValue($date->toString('yyyy-MM-dd hh:mm:ss'));

		$businessId = new Zend_Form_Element_Hidden('classified_id');
		$businessId->removeDecorator('label');
		$businessId->setValue($classid);
		
		$added_by = new Zend_Form_Element_Hidden('added_by');
		$businessId->removeDecorator('label');
		$businessId->setValue($user_id);
		
		$price = new Zend_Form_Element_Text('amount');
		$price->setLabel('Price in shs')
					->addFilter('StripTags')
					->addFilter('StringTrim')
					->addValidator('NotEmpty');
					
		$comment = new Zend_Form_Element_Textarea('comment');
		$comment->setLabel('Comment')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim');
		
		$submit=$this->createElement('submit','submit',array(
						'ignore'=>true,
						'label'=>'ok'
				));
	   	$this->addElements(array($date_added, $added_by, $businessId, $price, $comment));
	   	$this->addElement($submit);
	}


}

