<?php

class Forums_Form_GroupUpdate extends Zend_Form
{
    public function init()
    {
        $this->setName('Update Group');

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title:')
                ->setRequired(true);

        $type = new Zend_Form_Element_Radio('type');
        $options = array("Open", "Closed", "Hidden");
        $type->setLabel('Group Type:') 
                ->addMultiOptions($options)
                ->setRequired(true);

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel('Description')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Update') ->setAttrib('id', 'submitbutton');

        $this->addElements(array($title, $type, $description, $submit));
    }
}