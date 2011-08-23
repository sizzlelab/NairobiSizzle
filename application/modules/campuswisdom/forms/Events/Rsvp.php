<?php
class  campuswisdom_Form_Events_Rsvp extends Zend_Form
{

    public function __construct($options=NULL)
		{

		parent::__construct($options);
		$this->setName('rsvp')
		         ->setMethod('post');
                $rsvp = new Zend_Form_Element_Select('rsvp');
		$rsvp->setLabel('Your Response')
					->setRequired(true)
                                        ->addMultiOptions(array('rsvp1' =>'I will attend',' rsvp2'=> 'I am not yet decided','rsvp3'=>'I will not attend'))
					->addFilter('StripTags')
					->addFilter('StringTrim')
                                        ->setRegisterInArrayValidator(false)
                                        ->setDescription('Respond to this event invitation')
					->addValidator('NotEmpty');

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('id', 'submitbutton')
			      ->setLabel('RSVP now');
		$this->addElements(array($rsvp,$submit));
		}
}