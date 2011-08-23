<?php

class Yearbook_Form_Postexperience extends Zend_Form
{

   public function init()
{

    $this->setName('PostjobExperience');
        $id = new Zend_Form_Element_Hidden('id');
        $id->addFilter('Int');
        


    $jobfield = new Zend_Form_Element_Select('field');
    $jobfield->setLabel('Job Field:')
             ->addMultiOptions(array('Administration,business & office work'=>'Administration,business & office work',
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

       $jobdescription=new Zend_Form_Element_Textarea('description',array('cols'=>'20', 'rows'=>'3'));
       $jobdescription->setLabel('Job Description')
                       ->setRequired(true)
                       ->addFilter('StripTags')
                       ->addFilter('StringTrim')
                       ->addValidator('NotEmpty');

       $company=new Zend_Form_Element_Text('company');
       $company->setLabel('Company(optional)')
               
               ->addFilter('StripTags')
               ->addFilter('StringTrim');

       

       $experience=new Zend_Form_Element_Textarea('experience',array('cols'=>'20','rows'=>'15'));
       $experience->setLabel('Experience')
                   ->setRequired(true)
                   ->addFilter('StripTags')
                   ->addFilter('StringTrim')
                   ->addValidator('NotEmpty');

        $submit = new Zend_Form_Element_Submit('Share Experience');
        $submit->setAttrib('id', 'submitbutton');
        $this->addElements(array($id, $jobfield,$company,$jobdescription,$experience,$submit));


        
    }

    

}

