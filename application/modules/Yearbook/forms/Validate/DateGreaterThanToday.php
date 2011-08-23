<?php

class Form_Validate_DateGreaterThanToday extends Zend_Validate_Abstract {
	
	const NOT_GREATER = 'notGreater';
 
    protected $_messageTemplates = array(
        self::NOT_GREATER => 'This date cannot be in the future'
    );
 
    public function isValid($value, $context = null)
    {
    	if(!(Zend_Date::isDate($value,'yyyy-MM-dd '))){
    		return false;
    	}
    	
        $value = new Zend_Date((string) $value,'yyyy-MM-dd ');
        
       // var_dump($value->toString('yyyy-MM-dd '));
        
        $this->_setValue(($value));
        
        $today = new Zend_Date(null,'yyyy-MM-dd ');
        
      //  var_dump($today->toString('yyyy-MM-dd '));
 		        
        if(($value->isLater($today))||$value->isToday()){
        	return true;
        }
        $this->_error(self::NOT_GREATER);
        return false;
    }

}