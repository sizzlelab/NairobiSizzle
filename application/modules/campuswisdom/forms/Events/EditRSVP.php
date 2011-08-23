<?php
class  campuswisdom_Form_Events_EditRSVP extends Zend_Form
{

    public function __construct($options=NULL)
		{

		parent::__construct($options);
		$this->setName('rsvp')
		         ->setMethod('post');
                $rsvp = new Zend_Form_Element_Select('rsvp');
		$rsvp->setLabel('Your response')
					->setRequired(true)
                                        ->addMultiOptions(array('rsvp1' =>'I will attend',' rsvp2'=> 'I am not yet decided','rsvp3'=>'I will not attend'))
					->addFilter('StripTags')
					->addFilter('StringTrim')
                                        ->setRegisterInArrayValidator(false)
					->addValidator('NotEmpty');

		$confirm = new Zend_Form_Element_Submit('confirm');
		$confirm->setAttrib('id', 'submitbutton')
			   ->setLabel('Edit Now!')
			    ->setDecorators(array(
                   'ViewHelper',

                   'Description',

                   'Errors',

                   array(array('data'=>'HtmlTag'), array('tag' => 'td')),

                   array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
           ));
		$cancel = new Zend_Form_Element_Submit('cancel');
		$cancel->setAttrib('id', 'submitbutton')
			   ->setLabel('Cancel')
               ->setDecorators(array(
                   'ViewHelper',

                   'Description',

                   'Errors',

                   array(array('data'=>'HtmlTag'), array('tag' => 'td')),

                   array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
           ));
		$this->addElements(array($rsvp,$confirm,$cancel));
		}
}