<?php
class TrafficUpdates_Form_Comment extends Zend_Form {
    public function init() {
        $this->setMethod('post')
             ->addElement('textarea', 'body', array(
                        'filters'    => array('StringTrim'),
                        'required'   => true,
                        'decorators' => array(
                            'ViewHelper',
                            'Description',
                            'Errors',
                            array(array('data'=>'HtmlTag'), array('tag' => 'p'))
                        )
             ))
             ->addElement('submit', 'submit', array(
                        'label'      => 'Comment',
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
