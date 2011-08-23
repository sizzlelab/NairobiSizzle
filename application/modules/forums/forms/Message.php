<?php

class Forums_Form_Message extends Zend_Form
{

    public function init()
    {
        $this->setName('Message Notification Form')
                ->setMethod('post');

        $message = new Zend_Form_Element_Textarea('message');
        $message->setLabel('Message: ')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $send = new Zend_Form_Element_Submit('send');
        $send->setLabel("Send");
        $send->setAttrib('id', 'submitbutton');

        $this->addElements(array($message, $send));
    }


}

