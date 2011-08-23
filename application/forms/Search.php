<?php
class Application_Form_Search extends Zend_Form {
    public function init() {
        $this->setMethod('post');
        $this->addElement('text', 'search', array(
                'filters'  => array('StringTrim', 'StringToLower'),
                'required' => true,
                'decorators' => array(
                    'ViewHelper',
                    'Description',
                    'Errors',
                    array(array('data'=>'HtmlTag'), array('tag' => 'p', 'align' => 'center'))
                )
            ))
            ->addElement('submit', 'submit', array(
                 'label'    => 'Search',
                 'required' => false,
                 'ignore'   => true,
                 'decorators' => array(
                    'ViewHelper',
                    'Description',
                    'Errors',
                    array(array('data'=>'HtmlTag'), array('tag' => 'p', 'align' => 'center'))
                )
            ))
             ->setDecorators(array(
                'FormElements',
                'Form'
           ));
    }
}