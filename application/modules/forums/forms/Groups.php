<?php

class Forums_Form_Groups extends Zend_Form
{
    public function init()
    {
        $this->setName('Add Group');

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title:')
                ->setRequired(true);

        $type = new Zend_Form_Element_Radio('type');
        $options = array("Open - Anybody can join without confirmation", "Closed - New members must request for membership", "Hidden - Not viewable to the public");
        $type->setLabel('Group Type:')
                ->addMultiOptions($options)
                ->setRequired(true);
        
        $description = new Zend_Form_Element_Text('description');
        $description->setLabel('Description')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton') ->setLabel('Create');
        $this->addElements(array($title, $type, $description, $submit));
    }
}

