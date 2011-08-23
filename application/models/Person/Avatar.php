<?php
/**
 * Stores a person's avatar data.
 * 
 * You will almost never have to use the setter methods of this class directly,
 * since this is done by {@link Application_Model_Person::setAvatar()}.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Person_Avatar extends Application_Model_Abstract {
    /**
     * The avatar's link.
     * 
     * @var Application_Model_Link|null
     */
    protected $link      = null;

    /**
     * The avatar's status.
     * 
     * @var string
     */
    protected $status    = '';

    /**
     * Set an avatar's link.
     * 
     * @param array $link Containing values for these keys:
     *      href => string
     *      rel  => string
     * 
     * @return <type>
     */
    public function setLink(array $link = null) {
        if (is_array($link)) {
            $this->link = new Application_Model_Link($link);
        }
        return $this;
    }

    /**
     * Get an avatar's link.
     *
     * @see Application_Model_Person_Avatar::setLink()
     * 
     * @return Application_Model_Link
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * Set an avatar's status
     *
     * @param string $status
     * 
     * @return Application_Model_Person_Avatar
     */
    public function setStatus($status) {
        $this->status = (string) $status;
        return $this;
    }

    /**
     * Get an avatar's status.
     *
     * @see Application_Model_Person_Avatar::setStatus()
     * 
     * @return string Returns either 'set' or 'not set'.
     */
    public function getStatus() {
        return $this->status;
    }
}