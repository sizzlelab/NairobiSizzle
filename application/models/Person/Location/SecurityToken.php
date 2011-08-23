<?php
/**
 * Stores data pertaining to a location security token.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Person_Location_SecurityToken extends Application_Model_Abstract {
    /**
     * The location security token.
     *
     * @var string
     */
    protected $locationSecurityToken = '';

    /**
     * Sets a location security token.
     *
     * @param string $locationSecurityToken
     *
     * @return Application_Model_Person_Location_SecurityToken
     */
    public function setLocationSecurityToken($locationSecurityToken) {
        $this->locationSecurityToken = (string) $locationSecurityToken;
        return $this;
    }

    /**
     * Gets a location security token.
     *
     * @see Application_Model_Person_Location_SecurityToken::setLocationSecurityToken()
     * 
     * @return string
     */
    public function getLocationSecurityToken() {
        return $this->locationSecurityToken;
    }
}