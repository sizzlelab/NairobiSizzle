<?php
/**
 * Stores a person's availability data.
 *
 * The setter methods of this class are not meant for your use, once you use the
 * availablity mapper ({@link Application_Model_Person_Availability}) in order to
 * check for availability, this data is automatically populated by the mapper thus
 * you only need to call the getter methods.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Person_Availability extends Application_Model_Abstract {
    /**
     * The availability of the person's username.
     * 
     * @var string
     */
    protected $username = '';

    /**
     * The availability of the person's email.
     *
     * @var string
     */
    protected $email    = '';

    /**
     * Set the person's username availability.
     *
     * @param string $username
     *
     * @return Application_Model_Person_Availability
     */
    public function setUsername($username) {
        $this->username = (string) $username;
        return $this;
    }

    /**
     * Get the person's username availability.
     *
     * @see Application_Model_Person_Availability::setUsername()
     * 
     * @return string Returns either 'available' or 'unavailable'
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Set the person's email availability.
     *
     * @param string $email
     *
     * @return Application_Model_Person_Availability
     */
    public function setEmail($email) {
        $this->email = (string) $email;
        return $this;
    }

    /**
     * Get the person's email availability.
     *
     * @see Application_Model_Person_Availability::setEmail()
     *
     * @return string Returns either 'available' or 'unavailable'
     */
    public function getEmail() {
        return $this->email;
    }
}
