<?php

class Filesharing_Form_Register extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
	$input= new Zend_Form_Element_Text('name');
	$input->setLabel('File name')
		->addValidator('alpha');
		
	$description=new Zend_Form_Element_Select('description');
        $description->setlabel('File type')
               ->addMultiOptions(array('Documents','Movie Clips','Music','Pictures'))
               ->setRequired(true)
               ->addFilter('StripTags')
               ->addValidator('NotEmpty');

	
        $filepath = new Zend_Form_Element_File('filePath', array(
                        'required' => true,
                        'validators' => array(
                                array('Size', false, 209715200), //800kb
                                array('Count', false, 1),
                                array('Extension', false, 'png,gif,jpg,txt,pdf,jpeg,mkv,avi,mpeg,docx,ppt,pptx,xlsx,xls')
                        )));
//        $filepath = new Zend_Form_Element_File('filePath');
//        $filepath ->setLabel('Browse for file')
//                ->setRequired(true)
//                ->addValidator(array
//                                array('Size', false, 819200), //800kb
//                                array('Count', false, 1),
//                                array('Extension', false, 'png,gif,jpg'));


	$disclaimer = new Zend_Form_Element_Checkbox('disclaimer');
        $disclaimer->setlabel('I agree to the terms and conditions')
            ->setRequired(true)
            ->addValidator('NotEmpty')
            ->setDecorators(array(
                     'ViewHelper',
                     array('Label', array('placement' => Zend_Form_Decorator_Abstract::APPEND)),
                     'Description',
                     'Errors',
                     array(array('data' => 'HtmlTag'), array('tag' => 'p'))
                 ));
				
	$submit = new Zend_Form_Element_Submit('submit');
	$submit->setAttrib('name', 'value');

	$this->addElements(array($input,$description,$filepath, $disclaimer,$submit));
    }


}

