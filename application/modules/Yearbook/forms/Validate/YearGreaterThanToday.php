<?php

class Profiles_Form_Validate_YearGreaterThanToday extends Zend_Validate_Abstract {

	const NOT_GREATER = 'notGreater';

    protected $_messageTemplates = array(
        self::NOT_GREATER => 'This year can only be in the past'
    );

    public function isValid($value, $context = null)
    {
    	if(!(Zend_Date::isDate($value,'yyyy'))){
    		return false;
    	}

        $value = new Zend_Date((string) $value,'yyyy');

       // var_dump($value->toString('yyyy'));

        $this->_setValue(($value));

        $today = new Zend_Date(null,'yyyy');

        //var_dump($today->toString('YYYY'));

        if(($value->isEarlier($today))||!($value->isLater($today))){
        	return true;
        }
        $this->_error(self::NOT_GREATER);
        return false;
    }

}