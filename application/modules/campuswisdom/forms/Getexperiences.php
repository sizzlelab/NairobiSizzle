<?php

class Campuswisdom_Form_Getexperiences extends Zend_Form {
    public function init() {
        $this->setMethod('post');
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
                    'Insecurity' => 'Insecurer Zones'
                ));      

        $view = new Zend_Form_Element_Submit('view', array(
                    'label' => 'View',
                    'ignore' => true,
                ));
        $this->addElements(array($Category, $view));
        /* Form Elements & Other Definitions Here ... */
    }

}

