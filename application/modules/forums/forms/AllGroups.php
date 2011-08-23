<?php

class Forums_Form_AllGroups extends Zend_Form
{

    public function init()
    {
        $this->setName('Groups Search');

        $query = new Zend_Form_Element_Text('query');
        $query->setLabel('Title or Description:')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $per_page = new Zend_Form_Element_Text('per_page');
        $per_page->setLabel('Results Per page:')
                ->setValue('5')
                ->addFilter('Int');

        $sort_by = new Zend_Form_Element_Select('sort_by');
        $sort_by->addMultiOptions(array("Title","Date modified","Date created","Description","Creator"));
        $sort_by->setLabel('Sort by:')
                ->setValue('0')
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $sort_order = new Zend_Form_Element_Radio('sort_order');
        $sort_order->setLabel('Sort order:')
                ->addMultiOptions(array("Ascending", "Descending"))
                ->setValue('0');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');

        $this->addElements(array($query, $per_page, $sort_by, $sort_order, $submit));
    }
}

