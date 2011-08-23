<?php
class TrafficUpdates_Form_Update extends Zend_Form {
    public function init() {
        $this->setMethod('post')
             ->addElement('text', 'title', array(
                        'label'      => 'Title',
                        'filters'    => array('StringTrim'),
                        'required'   => true,
                        'decorators' => array(
                            'ViewHelper',
                            'Description',
                            'Errors',
                            array(array('data'=>'HtmlTag'), array('tag' => 'p')),
                            'Label'
                        )
             ))
             ->addElement('textarea', 'body', array(
                        'label'      => 'Body (optional)',
                        'filters'    => array('StringTrim'),
                        'required'   => false,
                        'decorators' => array(
                            'ViewHelper',
                            'Description',
                            'Errors',
                            array(array('data'=>'HtmlTag'), array('tag' => 'p')),
                            'Label'
                        )
             ))
             ->addElement('submit', 'submit', array(
                        'label'      => 'Post update',
                        'required'   => false,
                        'ignore'     => true,
                        'decorators' => array(
                            'ViewHelper',
                            'Description',
                            'Errors',
                            array(array('data'=>'HtmlTag'), array('tag' => 'p'))
                        )
             ));
        $this->setDecorators(array(
                'FormElements',
                'Form'
        ));
    }
}
