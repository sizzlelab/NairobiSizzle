<?php

class campuswisdom_Form_Category extends Zend_Form
{

    public function init()
    {
    	$list = new campuswisdom_Model_DbTable_Categories();
    	$categories = $list->getAll();
    	array_unshift($categories, array('key'=>'0', 'value'=>'All'));
    	$this->setMethod('Post');
    	$decoratorsopen = array('ViewHelper', 'Description','Errors', array(array('data'=>'HtmlTag'), array('tag' => 'p', 'align' => 'center', 'openOnly'=>true)));
    	
    	$category=new Zend_Form_Element_Select('Category');
		$category->addFilter('StripTags')
				 ->addMultiOptions($categories)
				 ->addFilter('StringTrim')
				 ->addValidator('NotEmpty');
		$category->setDecorators($decoratorsopen);
		$this->addElement($category);
			
		$submit=$this->createElement('submit','submit',array(
						'ignore'=>true,
						'label'=>'get',
						'decorators' => array(
                    'ViewHelper',
                    'Description',
                    'Errors',
                    array(array('data'=>'HtmlTag'), array('tag' => 'p', 'align' => 'center', 'closeOnly'=>true))
                )
				));
	   	$this->addElement($submit);
    }


}

