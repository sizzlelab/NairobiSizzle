<?php

class Filesharing_Form_Download extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
		
        $disclaimer = new Zend_Form_Element_Checkbox('disclaimer');
        $disclaimer->setlabel('Disclaimer:')
            ->setRequired(true)
            ->addValidator('NotEmpty');

        $textarea = new Zend_Form_Element_Textarea('Read');
        $textarea->setLabel("Please read thrugh nicely")
                 ->setRequired(true)
                 ->setAttrib("disable","true");
        
	$submit = new Zend_Form_Element_Submit('submit');
	$submit->setAttrib('name', 'value');
        
	$this->addElements(array($textarea,$disclaimer));
    }
}

