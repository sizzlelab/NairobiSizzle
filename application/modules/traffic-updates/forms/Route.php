<?php
class TrafficUpdates_Form_Route extends Zend_Form {
    public function init() {
        $nameValidator = new Zend_Validate_Regex('/[a-zA-Z0-9 ]{2,}/');
        $nameValidator->setMessage('The route\'s name must be two or more letters/numbers');
        $this->setMethod('post')
             ->addElement('text', 'name', array(
                        'label'      => 'Name',
                        'filters'    => array('StringTrim'),
                        'validators' => array($nameValidator),
                        'required'   => true,
                        'description' => 'The name of your route'
             ))
             ->addElement('textarea', 'description', array(
                        'label'      => 'Description',
                        'filters'    => array('StringTrim'),
                        'required'   => true,
                        'description' => 'Brief description of your route'
             ))
             ->addElement('submit', 'submit', array(
                        'label'      => 'Create',
                        'required'   => false,
                        'ignore'     => true,
                        'decorators' => array(
                            'ViewHelper',
                            'Description',
                            'Errors',
                            array(array('data'=>'HtmlTag'), array('tag' => 'p'))
                        )
             ))
             ->addElement('hash', 'routecsrf', array(
                        'salt'       => 'routecsrf',
                        'ignore'     => true
             ));
        $this->getElement('routecsrf')->getValidator('Identical')->setMessage('Please re-submit the form');
    }
}
