<?php

class Yearbook_Form_Postjob extends Zend_Form
{

    public function init()
    {
        $this->setName('Postjob');
        $id = new Zend_Form_Element_Hidden('id');
        $id->addFilter('Int');

        $jobfield=new Zend_Form_Element_Select('jobfield');
        $jobfield->setlabel('Select Job field')
        ->addMultiOptions(
array('Administration,business & office work'=>'Administration,business & office work',
		'Building and construction'=>'Building and construction',
		'Tourism ,Catering and hospitality'=>'Tourism ,Catering and hospitality',
		'Computers and IT'=>'Computers and IT',
		'Design, arts and crafts'=>'Design, arts and crafts',
		'Education and training'=>'Education and training',
		'Engineering'=>'Engineering',
		'Financial services'=>'Financial services',
		'Healthcare'=>'Healthcare',
		'Languages, information and culture'=>'Languages, information and culture',
		'Legal and political services'=>'Legal and political services',
		'Leisure, sport and tourism'=>'Leisure, sport and tourism',
		'Manufacturing and production'=>'Manufacturing and production',
		'Marketing and advertising'=>'Marketing and advertising',
		'Media, print and publishing'=>'Media, print and publishing',
		'Performing arts'=>'Performing arts',
		'Personal,hair & beauty'=>'Personal,hair & beauty',
		'Retail sales and customer services'=>'Retail sales and customer services',
		'Science, maths and statistics'=>'Science, maths and statistics',
		'Security and armed forces'=>'Security and armed forces',
		'Social work and counselling services'=>'Social work and counselling services',
		'Transport and logistics'=>'Transport and logistics' ))
        ->setRequired(true)
        ->addFilter('StripTags')
        ->addValidator('NotEmpty');




        $value =date('Y-m-d');

       $Dateadv = new Zend_Form_Element_Text('dateadv',array('validators'=>array('date')));
        $Dateadv->setLabel('Date advertised (yyyy-mm-dd)')
        ->setRequired(true)
        ->setValue($value)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        ->addValidator('NotEmpty');
       // ->addValidator(new Form_Validate_DateGreaterThanToday());

        $Datedue = new Zend_Form_Element_Text('datedue',array('validators'=>array('date')));
        $Datedue->setLabel('Date due (yyyy-mm-dd)')
        ->setRequired(true)
        ->setValue($value)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        ->addValidator('NotEmpty');

        $Company = new Zend_Form_Element_Text('company');
        $Company->setLabel('Company:')
        ->setRequired(true)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        ->addValidator('NotEmpty');

        $JDescription = new Zend_Form_Element_Textarea('description', array ('cols'=>20,'rows'=>5));
        $JDescription->setLabel('Job Description:')

         ->setRequired(true)
         ->addFilter('StripTags')
         ->addFilter('StringTrim')
         ->addValidator('NotEmpty');


        $Qualifica = new Zend_Form_Element_Textarea('qualifica', array ('cols'=>20,'rows'=>5));
        $Qualifica->setLabel('Qualifications:')

        ->setRequired(true)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        ->addValidator('NotEmpty');

        $Howtoapp= new Zend_Form_Element_Textarea('howtoapp', array ('cols'=>20,'rows'=>5));
        $Howtoapp->setLabel('How to apply:')

        ->setRequired(true)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        ->addValidator('NotEmpty');
        
        


$submit = new Zend_Form_Element_Submit('Post');
$submit->setAttrib('id', 'submitbutton');
$this->addElements(array($id, $jobfield,$Dateadv,$Datedue,$Company,$JDescription,$Qualifica,$Howtoapp,$submit));

    }


}

