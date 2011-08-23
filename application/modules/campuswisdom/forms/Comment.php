<?php

class Campuswisdom_Form_Comment extends Zend_Form {

    public function init() {
        $this->setMethod('post');
        $comment = new Zend_Form_Element_Textarea('comment', array('cols' => 25, 'rows' => 10));
        $comment->setLabel('Debate on the view')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');
        /* Form Elements & Other Definitions Here ... */
        $submit = new Zend_Form_Element_Submit('Debate');
        $submit->setLabel('Debate')
                ->setIgnore(true);
        $this->addElements(array($comment, $submit));
    }

}

