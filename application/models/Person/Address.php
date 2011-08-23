<?php
/**
 * Stores data pertaining to a person's address.
 *
 * You will almost never have to use the setter methods of this class directly,
 * since this is done by {@link Application_Model_Person::setAddress()}.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Person_Address extends Application_Model_Abstract {
    /**
     * The unstructured address e.g. 'Yrj\u00f6-Koskisenkatu 42, 00170 Helsinki'.
     * 
     * @var string
     */
    protected $unstructured  = '';

    /**
     * The address postal code e.g. '00170'.
     * 
     * @var string
     */
    protected $postalCode    = '';

    /**
     * The street address detail of the address e.g. 'Yrj\u00f6-Koskisenkatu 42' .
     *
     * @var string
     */
    protected $streetAddress = '';

    /**
     * The locality detail of the address e.g. 'Helsinki'.
     * 
     * @var string
     */
    protected $locality      = '';

    /**
     * Set the unstructured address.
     * 
     * @param string $unstructured
     *
     * @return Application_Model_Person_Address
     */
    public function setUnstructured($unstructured) {
        $this->unstructured = (string) $unstructured;
        return $this;
    }

    /**
     * Get the unstructured address.
     *
     * @see Application_Model_Person_Address::setUnstructured()
     * 
     * @return string
     */
    public function getUnstructured() {
        return $this->unstructured;
    }

    /**
     * Set the address postal code.
     * 
     * @param string $postalCode
     * 
     * @return Application_Model_Person_Address
     */
    public function setPostalCode($postalCode) {
        $this->postalCode = (string) $postalCode;
        return $this;
    }

    /**
     * Get the address postal code.
     *
     * @see Application_Model_Person_Address::setPostalCode()
     * 
     * @return string
     */
    public function getPostalCode() {
        return $this->postalCode;
    }

    /**
     * Set the street address detail of an address.
     * 
     * @param string $streetAddress
     * 
     * @return Application_Model_Person_Address
     */
    public function setStreetAddress($streetAddress) {
        $this->streetAddress = (string) $streetAddress;
        return $this;
    }

    /**
     * Get the street address detail of an address.
     *
     * @see Application_Model_Person_Address::setStreetAddress()
     * 
     * @return string
     */
    public function getStreetAddress() {
        return $this->streetAddress;
    }

    /**
     * Set the locality detail of an address.
     *
     * @param string $locality
     * 
     * @return Application_Model_Person_Address
     */
    public function setLocality($locality) {
        $this->locality = (string) $locality;
        return $this;
    }

    /**
     * Get the locality detail of an address.
     * 
     * @see Application_Model_Person_Address::setLocality()
     * 
     * @return string
     */
    public function getLocality() {
        return $this->locality;
    }
}