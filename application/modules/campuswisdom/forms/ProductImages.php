<?php

class campuswisdom_Form_ProductImages extends Zend_Form
{

	public function __construct($pdtid){
		$this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
		$this->setMethod('Post');
		parent::__construct($pdtid);
		$date_added = new Zend_Form_Element_Hidden('date_added');
		$date_added->removeDecorator('label');
		$date = new Zend_Date();
		$date_added->setValue($date->toString('yyyy-MM-dd hh:mm:ss'));

		$pdt_Id = new Zend_Form_Element_Hidden('business_id');
		$pdt_Id->removeDecorator('label');
		$pdt_Id->setValue($pdtid);
		
		$image = new Zend_Form_Element_File('image');
		$image->setLabel('Upload an image')
		      ->setRequired(true)
		      ->setMaxFileSize(2097152) // limits the filesize on the client side
		      ->setDescription('Click Browse and click on the image file you would like to upload');
		$image->addValidator('Count', false, 1);                // ensure only 1 file
		$image->addValidator('Size', false, 2097152);            // limit to 10 meg
		$image->addValidator('Extension', false, 'jpg,jpeg,png,gif');// only JPEG, PNG, and GIFs
			
		$submit=$this->createElement('submit','submit',array(
						'ignore'=>true,
						'label'=>'ok'
				));
	   	$this->addElements(array($date_added, $pdt_Id, $image));
	   	$this->addElement($submit);
	}
	
  
}

