<?php

class Application_Form_Search extends Zend_Form
{

    public function init()
    {
        $this->setName('User search');
        $id = new Zend_Form_Element_Hidden('id');
        $id->addFilter('Int');

       $name= new Zend_Form_Element_Text('name');
        $name->setLabel('Name')
        ->setRequired(true)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        ->addValidator('NotEmpty');

       $search = new Zend_Form_Element_Submit('search');
        $search->setLabel("Search");
        $search->setAttrib('id', 'submitbutton');

        $this->addElements(array($name, $search));

        
    }


}

