<?php
abstract class Application_Form_Person_Abstract {

    public static function getElement($elementName) {
        $method = 'get' . ucfirst($elementName) . 'Element';
        return call_user_func(array($this, $method));
    }

    public static function getAvatarElement() {
        $avatar = new Zend_Form_Element_File('avatar', array(
                        'required' => true,
                        'validators' => array(
                                array('Size', false, 819200), //800kb
                                array('Count', false, 1),
                                array('Extension', false, 'png,gif,jpg')
                        )
        ));
        return $avatar->setDescription('Only .gif, .jpg and .png files allowed, maximum 800kb.')
                      ->removeDecorator('label');
    }

    public static function getStatusMessageElement() {
        return new Zend_Form_Element_Textarea('status_message', array(
                        'label'      => 'Status Update',
                        'filters'    => array('StringTrim'),
                        'required'   => true,
                        'decorators' => array(
                            'ViewHelper',
                            'Description',
                            'Errors',
                            array(array('data'=>'HtmlTag'), array('tag' => 'p'))
                        )
        ));
    }

    public static function getUsernameElement() {
        $lengthValidator = new Zend_Validate_StringLength(array('min' => 4, 'max' => 20));
        $alnumValidator  = new Zend_Validate_Alnum();
        $alnumValidator->setMessage('Username may only contain letters and numbers');
        $lengthValidator->setMessage('Username should be between 4 and 20 characters');
        $username = new Zend_Form_Element_Text('username', array(
                        'label'      => 'Username',
                        'filters'    => array('StringTrim'),
                        'validators' => array(
                            $alnumValidator,
                            $lengthValidator
                        ),
                        'required'   => true
        ));
        $username->setDescription('Between 4 and 20 letters and/or numbers.');
        return $username;
    }

    public static function getPasswordElement($label = 'Password') {
        $validator = new Zend_Validate_StringLength(array(5));
        $validator->setMessage('Password should not be less than 5 characters');
        $password = new Zend_Form_Element_Password('password', array(
                        'label'      => $label,
                        'filters'    => array('StringTrim'),
                        'validators' => array(
                            $validator
                        ),
                        'required'   => true
        ));
        $password->setDescription('Not less than 5 characters.');
        return $password;
    }

    public static function getPasswordConfirmElement($label = 'Repeat Password') {
        $validator = new Zend_Validate_StringLength(array(5));
        $validator->setMessage('Password should not be less than 5 characters');
        $password = new Zend_Form_Element_Password('confirm_password', array(
                        'label'      => $label,
                        'filters'    => array('StringTrim'),
                        'validators' => array(
                            $validator
                        ),
                        'required'   => true
        ));
        $password->setDescription('Should match the password above.');
        return $password;
    }

    public static function getPasswordOldElement() {
        return new Zend_Form_Element_Password('old_password', array(
                        'label'      => 'Your old password',
                        'filters'    => array('StringTrim'),
                        'required'   => true
        ));
    }

    public static function getFirstNameElement() {
        return new Zend_Form_Element_Text('given_name', array(
                        'label'      => 'First name',
                        'filters'    => array('StringTrim'),
                        'validators' => array('Alnum'),
                        'required'   => false
        ));
    }

    public static function getLastNameElement() {
        return new Zend_Form_Element_Text('family_name', array(
                        'label'      => 'Last name',
                        'filters'    => array('StringTrim'),
                        'validators' => array('Alnum'),
                        'required'   => false
        ));
    }

    public static function getGenderElement() {
        return new Zend_Form_Element_Select('gender', array(
                        'label'        => 'Gender',
                        'required'     => false,
                        'multiOptions' => array(
                                'MALE' => 'Male',
                                'FEMALE' => 'Female',
                        )
        ));
    }

    public static function getBirthdateDayElement() {
        for ($i = 1; $i <= 31; $i++) {
            $days[$i] = $i;
        }
        return new Zend_Form_Element_Select('day', array(
                        'label'        => 'Day',
                        'required'     => false,
                        'multiOptions' => $days
        ));
    }

    public static function getBirthdateMonthElement() {
        return new Zend_Form_Element_Select('month', array(
                        'label'        => 'Month',
                        'required'     => false,
                        'multiOptions' => array(
                                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                                5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
                                9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
                        )
        ));
    }

    public static function getBirthdateYearElement() {
        $year = date('Y');
        for ($i = $year; $i >= 1900; $i--) {
            $years[$i] = $i;
        }
        return new Zend_Form_Element_Select('year', array(
                        'label'        => 'Year',
                        'required'     => false,
                        'multiOptions' => $years
        ));
    }

    public static function getDescriptionElement() {
        return new Zend_Form_Element_Textarea('description', array(
                        'label'      => 'About me',
                        'filters'    => array('StringTrim'),
                        'required'   => false
        ));
    }

    public static function getPhoneNumberElement() {
        $validator = new Zend_Validate_Regex('/^\+{0,1}[ 0-9]{10,24}$/');
        $validator->setMessage('The phone number you entered is not valid or contains illegal characters');
        return new Zend_Form_Element_Text('phone_number', array(
                        'label'      => 'Phone number',
                        'filters'    => array('StringTrim'),
                        'validators' => array($validator),
                        'required'   => false
        ));
    }

    public static function getEmailElement() {
        $email = new Zend_Form_Element_Text('email', array(
                        'label'      => 'Email',
                        'filters'    => array('StringTrim'),
                        'validators' => array('EmailAddress'),
                        'required'   => false
        ));
        return $email->setDescription('Should be a valid email address');
    }

    public static function getMsnNickElement() {
        $validator = new Zend_Validate_Alnum();
        $validator->setMessage('Your profile ID may only contain numbers and letters');
        return new Zend_Form_Element_Text('msn_nick', array(
                        'label'      => 'Facebook Profile ID',
                        'filters'    => array('StringTrim'),
                        'validators' => array($validator),
                        'required'   => false,
                        'description' => "No need to enter your full profile link, enter your profile ID only. Your profile ID is something like this: 'http://www.facebook.com/profile_id'"
        ));
    }

    public static function getIrcNickElement() {
        $validator = new Zend_Validate_Alnum();
        $validator->setMessage('Your profile ID may only contain numbers and letters');
        return new Zend_Form_Element_Text('irc_nick', array(
                        'label'      => 'Twitter Profile ID',
                        'filters'    => array('StringTrim'),
                        'validators' => array($validator),
                        'required'   => false,
                        'description' => "No need to enter your full profile link, enter your profile ID only. Your profile ID is something like this: 'http://twitter.com/profile_id'"
        ));
    }

    public static function getWebsiteElement() {
        return new Zend_Form_Element_Text('website', array(
                        'label'      => 'Website',
                        'filters'    => array('StringTrim'),
                        'required'   => false
        ));
    }

    public static function getLocationLabelElement() {
        return new Zend_Form_Element_Text('label', array(
                        'label'      => 'Location',
                        'filters'    => array('StringTrim'),
                        'required'   => false
        ));
    }

    public static function getAddressStreetAddressElement() {
        return new Zend_Form_Element_Text('street_address', array(
                        'label'      => 'Address (P.O. Box)',
                        'filters'    => array('StringTrim'),
                        'required'   => false
        ));
    }

    public static function getAddressLocalityElement() {
        return new Zend_Form_Element_Text('locality', array(
                        'label'      => 'Town/City',
                        'filters'    => array('StringTrim'),
                        'required'   => false
        ));
    }

    public static function getAddressPostalCodeElement() {
        return new Zend_Form_Element_Text('postal_code', array(
                        'label'      => 'Postal Code',
                        'filters'    => array('StringTrim'),
                        'validators' => array('Digits'),
                        'required'   => false
        ));
    }

    public static function getSubmitElement() {
        return new Zend_Form_Element_Submit('submit', array(
                 'label'    => 'Update',
                 'required' => false,
                 'ignore'   => true,
                 'decorators' => array(
                    'ViewHelper',
                    'Description',
                    'Errors',
                    array(array('data'=>'HtmlTag'), array('tag' => 'p'))
                )
        ));
    }

    public static function getHashElement($name) {
        $hash = new Zend_Form_Element_Hash($name, array(
             	 'ignore'     => true,
                 'salt'       => $name
        ));
        $hash->getValidator('Identical')->setMessage('Please re-submit the form');
        return $hash;
    }
}