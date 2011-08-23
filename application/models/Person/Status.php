<?php
/**
 * Stores data pertaining to a person's status update.
 *
 * You will almost never have to use the setter methods of this class directly,
 * since this is done by {@link Application_Model_Person::setStatus()}.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Person_Status extends Application_Model_Abstract {
    /**
     * When the status was last changed.
     * 
     * @var string
     */
    protected $changed = '';

    /**
     * The status message.
     * 
     * @var string
     */
    protected $message = '';

    /**
     * Set when a person's status was last changed.
     *
     * @param string $changed Time in UTC format.
     *
     * @return Application_Model_Person_Status
     */
    public function setChanged($changed) {
        $this->changed = (string) $changed;
        return $this;
    }

    /**
     * Get when a person's status was last changed.
     *
     * @see Application_Model_Person_Status::setChanged()
     * 
     * @return string Time in UTC format.
     */
    public function getChanged() {
        return $this->changed;
    }

    /**
     * Set the status message.
     *
     * @param string $message
     *
     * @return Application_Model_Person_Status
     */
    public function setMessage($message) {
        $this->message = (string) $message;
        return $this;
    }

    /**
     * Get the status message.
     *
     * @see Application_Model_Person_Status::setMessage()
     *
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }
}