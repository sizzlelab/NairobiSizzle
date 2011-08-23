<?php
/**
 * Base mapper class for all person mappers e.g. {@link Application_Model_Mapper_Person_Availability},
 * {@link Application_Model_Mapper_Person_Avatar}, {@link Application_Model_Mapper_Person_Friends} etc.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 *
 * @uses Application_Model_Mapper_Search_Abstract
 */
abstract class Application_Model_Mapper_Person_Abstract extends Application_Model_Mapper_Search_Abstract {
    /**
     * Stores an instance of {@link Application_Model_Person} that is manipulated
     * by the person mappers.
     *
     * @var Application_Model_Person|null
     */
    protected $person = null;

    /**
     * Set the {@link Application_Model_Person} instance.
     *
     * @param Application_Model_Person $person
     *
     * @return Application_Model_Person_Mapper_Abstract
     */
    public function setPerson(Application_Model_Person $person) {
        $this->person = $person;
        return $this;
    }

    /**
     * Get the {@link Application_Model_Person} instance. This method will create
     * a new instance of {@link Application_Model_Person} if none had been set
     * at the time of its calling.
     *
     * @see Application_Model_Person_Mapper_Abstract::setPerson()
     *
     * @return Application_Model_Person
     */
    public function getPerson() {
        if (!$this->person) {
            $this->setPerson(new Application_Model_Person());
        }
        return $this->person;
    }
}