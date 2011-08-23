<?php
/**
 * Stores a person's data.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Person extends Application_Model_Abstract {
    /**
     * The person's ID.
     * 
     * @var string
     */
    protected $id            = '';

    /**
     * The person's username.
     * 
     * @var string
     */
    protected $username      = '';
    
    /**
     * The person's password.
     * 
     * @var string
     */
    protected $password      = '';
    
    /**
     * The person's email.
     * 
     * @var string 
     */
    protected $email         = '';
    
    /**
     * Whether the person is an association or not.
     * 
     * @var bool
     */
    protected $isAssociation = false;
    
    /**
     * The license agreement that a person has agreed to. This would be a string
     * denoting the agreement (language) and version number as served by ASI.
     *
     * @var string
     */
    protected $consent       = '';
    
    /**
     * The person's role.
     * 
     * @var string
     */
    protected $role          = '';
    
    /**
     * The person's name.
     *
     * @var Application_Model_Person_Name|null
     */
    protected $name          = null;

    /**
     * The person's address.
     *
     * @var Application_Model_Person_Address|null
     */
    protected $address       = null;

    /**
     * The person's location.
     *
     * @var Application_Model_Person_Location|null
     */
    protected $location      = null;

    /**
     * The person's birthdate (YYYY-MM-DD).
     * 
     * @var string
     */
    protected $birthdate     = '';

    /**
     * The person's connection e.g. 'you','friend' to another object in ASI.
     *
     * @var string
     */
    protected $connection    = '';

    /**
     * Last update time of a person's profile in UTC time.
     *
     * @var string
     */
    protected $updatedAt     = '';
    
    /**
     * The person's avatar.
     * 
     * @var Application_Model_Person_Avatar|null
     */
    protected $avatar        = null;
    
    /**
     * The person's gender.
     * 
     * @var string
     */
    protected $gender        = '';
    
    /**
     * The person's MSN nickname/ID.
     *
     * @var string
     */
    protected $msnNick       = '';

    /**
     * The person's phone number. Includes the country code e.g. +254 734 734.
     *
     * @var string
     */
    protected $phoneNumber   = '';

    /**
     * The person's homepage/blog/website.
     *
     * @var string
     */
    protected $website       = '';

    /**
     * The person's IRC nickname.
     * 
     * @var string
     */
    protected $ircNick       = '';

    /**
     * The person's description.
     * 
     * @var string
     */
    protected $description   = '';
    
    /**
     * The person's status update.
     * 
     * @var Application_Model_Person_Status|null
     */
    protected $status        = null;

    /**
     * Set a person's ID.
     *
     * @param string $id
     * @return Application_Model_Person
     */
    public function setId($id) {
        $this->id = (string) $id;
        return $this;
    }

    /**
     * Get a person's ID.
     *
     * @see Application_Model_Person::setId()
     * 
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set a person's username.
     *
     * @param string $username
     *
     * @return Application_Model_Person
     */
    public function setUsername($username) {
        $this->username = (string) $username;
        return $this;
    }

    /**
     * Get a person's username.
     *
     * @see Application_Model_Person::setUsername()
     * 
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Set a person's password.
     *
     * @param string $password
     *
     * @return Application_Model_Person
     */
    public function setPassword($password) {
        $this->password = (string) $password;
        return $this;
    }

    /**
     * Get a person's password.
     *
     * @see Application_Model_Person::setPassword()
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set a person's email.
     *
     * @param string $email
     *
     * @return Application_Model_Person
     */
    public function setEmail($email) {
        $this->email = (string) $email;
        return $this;
    }

    /**
     * Get a person's email.
     *
     * @see Application_Model_Person::setEmail()
     *
     * @return Application_Model_Person
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set a flag indicating whether a person is an association or not.
     *
     * @param bool $isAssociation
     *
     * @return Application_Model_Person
     */
    public function setIsAssociation($isAssociation) {
        $this->isAssociation = (boolean) $isAssociation;
        return $this;
    }

    /**
     * Get a flag indicating whether a person is an association or not.
     *
     * @see Application_Model_Person::setIsAssociation()
     * 
     * @return bool
     */
    public function getIsAssociation() {
        return $this->isAssociation;
    }

    /**
     * Set the license agreement that a person has consented to.
     *
     * @param string $consent
     * 
     * @return Application_Model_Person
     */
    public function setConsent($consent) {
        $this->consent = (string) $consent;
        return $this;
    }

    /**
     * Get the license agreement that a person has consented to.
     *
     * @see Application_Model_Person::setConstent()
     * 
     * @return string
     */
    public function getConsent() {
        return $this->consent;
    }

    /**
     * Set a person's role.
     *
     * @param string $role
     * 
     * @return Application_Model_Person
     */
    public function setRole($role) {
        $this->role = (string) $role;
        return $this;
    }

    /**
     * Get a person's role.
     *
     * @see Application_Model_Person::setRole()
     *
     * @return Application_Model_Person
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * Set a person's name.
     *
     * @param array $name Containing values for these keys:
     *      given_name   => string
     *      family_name  => string
     *      unstructured => string
     * 
     * @return Application_Model_Person
     */
    public function setName(array $name = null) {
        if (is_array($name)) {
            $this->name = new Application_Model_Person_Name($name);
        }
        return $this;
    }

    /**
     * Get a person's name.
     *
     * @see Application_Model_Person::setName()
     * 
     * @return Application_Model_Person_Name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set a person's address. Note the difference with {@link Application_Model_Person::setLocation()}.
     * 
     * @param array $address Containing values for these keys:
     *      unstructred    => string
     *      postal_code    => string
     *      street_address => string
     *      locality       => string
     *
     * @return Application_Model_Person
     */
    public function setAddress(array $address = null) {
        if (is_array($address)) {
            $this->address = new Application_Model_Person_Address($address);
        }
        return $this;
    }

    /**
     * Get a person's address.
     *
     * @see Application_Model_Person::setAddress()
     *
     * @return Application_Model_Person_Address
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * Set a person's location. Note the difference with {@link Application_Model_Person::setAddress}
     * 
     * @param array $location Containing values for these keys:
     *      label       => string
     *      latitude    => float
     *      longitude   => float
     *      accuracy    => float
     *
     * @return Application_Model_Person
     */
    public function setLocation(array $location = null) {
        if (is_array($location)) {
            $this->location = new Application_Model_Person_Location($location);
        }
        return $this;
    }

    /**
     * Get a person's location.
     *
     * see Application_Model_Person::setLocation()
     *
     * @return Application_Model_Person_Location
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * Set a person's birthdate.
     *
     * @param string $birthdate In this format: YYYY-MM-DD.
     *
     * @return Application_Model_Person
     */
    public function setBirthdate($birthdate) {
        $this->birthdate = (string) $birthdate;
        return $this;
    }

    /**
     * Get a person's birthdate.
     *
     * @see Application_Model_Person::setBirthdate()
     *
     * @return string
     */
    public function getBirthdate() {
        return $this->birthdate;
    }

    /**
     * Set a person's connection to another object e.g. another person, group etc.
     *
     * @param string $connection
     *
     * @return Application_Model_Person
     */
    public function setConnection($connection) {
        $this->connection = (string) $connection;
        return $this;
    }

    /**
     * Get a person's connection to another object e.g. another person, group etc.
     *
     * @see Application_Model_Person::setConnection()
     *
     * @return string
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Set the time that a person's profile was last updated. Note that this function
     * is not for your (programmer) use, but for the ASI platfrom (when fetching a
     * person's profile).
     *
     * @param string $updatedAt In UTC format.
     *
     * @return Application_Model_Person
     */
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = (string) $updatedAt;
        return $this;
    }

    /**
     * Get the time that a person's profile was last updated.
     *
     * @see Application_Model_Person::setUpdatedAt()
     *
     * @return string
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * Set a person's avatar.
     *
     * @param array $avatar Containing values for these keys:
     *      status  => string
     *      link    => array
     *
     * @return Application_Model_Person
     */
    public function setAvatar(array $avatar = null) {
        if (is_array($avatar)) {
            $this->avatar = new Application_Model_Person_Avatar($avatar);
        }
        return $this;
    }

    /**
     * Get a person's avatar.
     *
     * @see Application_Model_Person::setAvatar()
     * 
     * @return Application_Model_Person_Avatar
     */
    public function getAvatar() {
        return $this->avatar;
    }

    /**
     * Set a person's gender.
     * 
     * @param string $gender
     *
     * @return Application_Model_Person
     */
    public function setGender($gender) {
        $this->gender = (string) $gender;
        return $this;
    }

    /**
     * Get a person's gender.
     *
     * @see Application_Model_Person::setGender()
     * 
     * @return string
     */
    public function getGender() {
        return $this->gender;
    }

    /**
     * Set a person's MSN nickname.
     *
     * @param string $msnNick
     *
     * @return Application_Model_Person
     */
    public function setMsnNick($msnNick) {
        $this->msnNick = (string) $msnNick;
        return $this;
    }

    /**
     * Get a person's MSN nickname.
     *
     * @see Application_Model_Person::setMsnNick()
     *
     * @return string
     */
    public function getMsnNick() {
        return $this->msnNick;
    }

    /**
     * Set a person's phone number.
     *
     * @param string $phoneNumber May also contain country code and spaces e.g.
     * '+254 346 4357 43'
     *
     * @return Application_Model_Person
     */
    public function setPhoneNumber($phoneNumber) {
        $this->phoneNumber = (string) $phoneNumber;
        return $this;
    }

    /**
     * Get a person's phone number.
     *
     * @see Application_Model_Person::setPhoneNumber()
     * 
     * @return string
     */
    public function getPhoneNumber() {
        return $this->phoneNumber;
    }

    /**
     * Set a person's blog/website/other homepage.
     *
     * @param string $website
     *
     * @return Application_Model_Person
     */
    public function setWebsite($website) {
        $this->website = (string) $website;
        return $this;
    }

    /**
     * Get a person's website/blog/other homepage.
     *
     * @see Application_Model_Person::setWebsite()
     * 
     * @return string
     */
    public function getWebsite() {
        return $this->website;
    }

    /**
     * Set a person's IRC nickname.
     * 
     * @param string $ircNick
     * 
     * @return Application_Model_Person
     */
    public function setIrcNick($ircNick) {
        $this->ircNick = (string) $ircNick;
        return $this;
    }

    /**
     * Get a person's IRC nickname.
     *
     * @see Application_Model_Person::setIrcNick()
     *
     * @return string
     */
    public function getIrcNick() {
        return $this->ircNick;
    }

    /**
     * Set a person's description.
     *
     * @param string $description
     *
     * @return Application_Model_Person
     */
    public function setDescription($description) {
        $this->description = (string) $description;
        return $this;
    }

    /**
     * Get a person's description.
     *
     * @see Application_Model_Person::setDescription()
     * 
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set a person's status.
     * 
     * @param array $status Containing values for these keys:
     *      changed => string (When the status was set/changed in UTC format)
     *      message => string (The status message)
     * 
     * @return Application_Model_Person
     */
    public function setStatus(array $status = null) {
        if (is_array($status)) {
            $this->status = new Application_Model_Person_Status($status);
        }
        return $this;
    }

    /**
     * Get a person's status.
     *
     * @see Application_Model_Person::setStatus()
     * 
     * @return Application_Model_Person_Status
     */
    public function getStatus() {
        return $this->status;
    }
}