<?php

class Profiles_Form_Validate_DateRange extends Zend_Validate_Abstract {
	
	const NOT_GREATER = 'notGreater';
 
    protected $_messageTemplates = array(
        self::NOT_GREATER => 'Close Date must be Greater than Open Date'
    );
 
    public function isValid($value, $context = null)
    {
    	if(!(Zend_Date::isDate($value,'YYYY-MM-dd '))||!(Zend_Date::isDate($context['open_date'],'YYYY-MM-dd '))){
    		return false;
    	}
        $value = new Zend_Date((string) $value,'YYYY-MM-dd ');
        
        //var_dump($value->toString('YYYY-MM-dd '));
        
        $this->_setValue(($value));
        
        $context = new Zend_Date((string) $context['open_date'],'YYYY-MM-dd ');
 		        
        //var_dump($context->toString('YYYY-MM-dd '));
        
        if($value->isLater($context)||$value->equals($context)){
        	return true;
        }
        $this->_error(self::NOT_GREATER);
        return false;
    }

}