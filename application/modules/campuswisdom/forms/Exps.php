<?php

class Campuswisdom_Form_Exps extends Zend_Form {
    public function init() {
        $this->setMethod('post');
        $validator = new Zend_Validate_Alpha(array('allowWhiteSpace' => true));
        $Category = new Zend_Form_Element_Select('Category');
        $Category->setLabel('Select Category')
                ->setMultioptions(array(
                    'Course' => 'Course',
                    'Residentials' => 'Residentials',
                    'Cafeteria' => 'Cafeteria',
                    'Security' => 'General Security',
                    'College' => 'College/Campus',
                    'Unit' => 'Course Unit',
                    'Lecturer' => 'Lecturer',
                    'Insecurity' => 'Insecurer Zones'  ));        
        $this->addElement($Category);
        $Name = new Zend_Form_Element_Text('Name');
        $Name->setLabel('Title')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');
        $Views = new Zend_Form_Element_Textarea('Views', array('cols' => 25, 'rows' => 10));
        $Views->setLabel('Details')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');        
        $submit = new Zend_Form_Element_Submit('Share');
        $submit->setAttrib('label', 'Share!');

        $this->addElements(array($Name, $Views, $submit));

        /* Form Elements & Other Definitions Here ... */
    }

}

