<?php

class Forums_Form_SearchPerson extends Zend_Form
{
    public function init()
    {
        $this->setName('User Search Form')
                ->setMethod('post');

        $name = new Zend_Form_Element_Text('criteria');
        $name->setLabel('Name: ')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringToLower')
                ->addFilter('StringTrim');

        $search = new Zend_Form_Element_Submit('search');
        $search->setLabel("Search");
        $search->setAttrib('id', 'submitbutton');

        $this->addElements(array($name, $search));
    }

}

