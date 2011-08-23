<?php

class Forums_Form_Thread extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        $this->createElements();
    }
    public function createElements()
    {
                  
               
                $validator = new Zend_Validate_Alpha(array('allowWhiteSpace' => true));
		
                $title = new Zend_Form_Element_Text('title');
		$title->setLabel('Title')
					->setRequired(true)
					->addFilter('StripTags')
					->addFilter('StringTrim')
                                         ->addValidator($validator)
                                         ->addValidator('StringLength',false,array(2,50))
					->addValidator('NotEmpty');
                $this->addElement($title);
                $thread = new Zend_Form_Element_Textarea('thread',array('cols'=>25,'rows'=>10));
		$thread->setLabel('Type your question here')
					->setRequired(true)
					->addFilter('StripTags')
					->addFilter('StringTrim')
					->addValidator('NotEmpty');
                $options=array('public'=>'Open to everyone(Only check for public messages)');
                $visibility = new Zend_Form_Element_MultiCheckbox('privacy');
                $visibility->addMultiOptions($options)
                            ->setRequired(FALSE);

                $submit= new Zend_Form_Element_Submit('post');
                $submit->setAttrib('label', 'Post it!');
                $this->addElements(array($thread,$visibility,$submit));
    }

}

