<?php

class Forums_Form_Answer extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        
         $thread = new Zend_Form_Element_Textarea('answer',array('cols'=>25,'rows'=>10));
		$thread->setLabel('Post answer')
					->setRequired(true)
					->addFilter('StripTags')
					->addFilter('StringTrim')
					->addValidator('NotEmpty');
                $this->addElement($thread);
                $submit= new Zend_Form_Element_Submit('post');
                $submit->setAttrib('label', 'Answer it!');
                $this->addElement($submit);
    }


}

