<?php
/**
 * Stores data pertaining to a person's name.
 * 
 * You will almost never have to use the setter methods of this class directly,
 * since this is done by {@link Application_Model_Person::setName()}.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Person_Name extends Application_Model_Abstract {
    /**
     * The person's unstructured name.
     *
     * @var string
     */
    protected $unstructured = '';

    /**
     * The person's family name (surname).
     *
     * @var string
     */
    protected $familyName   = '';

    /**
     * The person's given name (first name and middle name (optional)).
     *
     * @var string
     */
    protected $givenName    = '';

    /**
     * Set the unstructured name.
     *
     * @param string $unstructured
     *
     * @return Application_Model_Person_Name
     */
    public function setUnstructured($unstructured) {
        $this->unstructured = (string) $unstructured;
        return $this;
    }

    /**
     * Get a person's unstructured name.
     *
     * @see Application_Model_Person_Name::setUnstructured()
     * 
     * @return string
     */
    public function getUnstructured() {
        return $this->unstructured;
    }

    /**
     * Set a person's family name.
     *
     * @param string $familyName
     *
     * @return Application_Model_Person_Name
     */
    public function setFamilyName($familyName) {
        $this->familyName = (string) $familyName;
        return $this;
    }

    /**
     * Get a person's family name.
     *
     * @see Application_Model_Person_Name::setFamilyName()
     *
     * @return string
     */
    public function getFamilyName() {
        return $this->familyName;
    }

    /**
     * Set a person's given name.
     *
     * @param string $givenName
     *
     * @return Application_Model_Person_Name
     */
    public function setGivenName($givenName) {
        $this->givenName = (string) $givenName;
        return $this;
    }

    /**
     * Get a person's given name.
     *
     * @see Application_Model_Person_Name::setGivenName()
     * 
     * @return string
     */
    public function getGivenName() {
        return $this->givenName;
    }
}
