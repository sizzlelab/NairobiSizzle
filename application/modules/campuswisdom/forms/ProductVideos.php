<?php

class campuswisdom_Form_ProductVideos extends Zend_Form
{

	public function __construct($pdtid){
		$this->setMethod('Post');
		$date_added = new Zend_Form_Element_Hidden();
		$date = new Zend_Date();
		$date_added->setValue($date->getTimestamp());

		$productId = new Zend_Form_Element_Hidden();
		$productId->setValue($pdtid);
		
		$video = new Zend_Form_Element_File('video');
		$video->setLabel('Upload video')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim');
			
		$submit=$this->createElement('submit','submit',array(
						'ignore'=>true,
						'label'=>'ok'
				));
	   	$this->addElement($submit);	

		$this->addElements(array($date_added, $productId, $image));
	}

}

