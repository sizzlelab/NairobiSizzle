<?php

class campuswisdom_Form_BusinessVideos extends Zend_Form
{

	public function __construct($bizid){
		$date_added = new Zend_Form_Element_Hidden();
		$date = new Zend_Date();
		$date_added->setValue($date->getTimestamp());

		$businessId = new Zend_Form_Element_Hidden();
		$businessId->setValue($bizid);
		
		$image = new Zend_Form_Element_Image('image');
		$image->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim');

		$submit=$this->createElement('submit','submit',array(
						'ignore'=>true,
						'label'=>'ok'
				));
	   	$this->addElement($submit);			
				
		$this->addElements(array($date_added, $businessId, $image));
	}
	
    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
    }


}

