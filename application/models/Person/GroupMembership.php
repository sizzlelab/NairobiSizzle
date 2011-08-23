<?php
/**
 * Stores a person's group membership data. This also includes data at the invitation
 * stage i.e after a person has requested to join a group and before the acceptance
 * of the request.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Person_GroupMembership extends Application_Model_Abstract {
    /**
     * When the group membership was created in UTC time format.
     * 
     * @var string
     */
    protected $createdAt  = '';

    /**
     * When the group membership was accepted in UTC time format.
     *
     * @var string
     */
    protected $acceptedAt = '';

    /**
     * When the group membership was last updated in UTC time format.
     *
     * @var string
     */
    protected $updatedAt  = '';

    /**
     * The ID of the group.
     * 
     * @var string
     */
    protected $groupId    = '';

    /**
     * The ID of the person who invited this person (whose membership we're storing)
     * to the group.
     * 
     * @var string
     */
    protected $inviteId   = '';

    /**
     * The ID of the person.
     *
     * @var string
     */
    protected $personId   = '';

    /**
     * Whether the person is an admin in the group or not.
     * 
     * @var bool
     */
    protected $adminRole  = false;

    /**
     * The status of the person's membership to a group e.g 'active'
     * @var <type>
     */
    protected $status     = '';

    /**
     * Set when a person's membership to a group was created.
     *
     * @param string $createdAt Time in UTC format.
     *
     * @return Application_Model_Person_GroupMembership
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = (string) $createdAt;
        return $this;
    }

    /**
     * Get when a person's membership to a group was created.
     *
     * @see Application_Model_Person_GroupMembership::setCreatedAt()
     * 
     * @return string Time in UTC format.
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set when a person's membership to a group was accepted.
     *
     * @param string $acceptedAt Time in UTC format.
     *
     * @return Application_Model_Person_GroupMembership
     */
    public function setAcceptedAt($acceptedAt) {
        $this->acceptedAt = (string) $acceptedAt;
        return $this;
    }

    /**
     * Get when a person's membership to a group was accepted.
     *
     * @see Application_Model_Person_GroupMembership::setAcceptedAt()
     * 
     * @return string Time in UTC format.
     */
    public function getAcceptedAt() {
        return $this->acceptedAt;
    }

    /**
     * Set the last time a person's membership to a group was updated.
     *
     * @param string $updatedAt Time in UTC format.
     *
     * @return Application_Model_Person_GroupMembership
     */
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = (string) $updatedAt;
        return $this;
    }

    /**
     * Get the last time a person's membership to a group was updated.
     *
     * @see Application_Model_Person_GroupMembership::setUpdatedAt()
     *
     * @return string Time in UTC format.
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * Set the group ID of the concerned group.
     *
     * @param string $groupId
     * 
     * @return Application_Model_Person_GroupMembership
     */
    public function setGroupId($groupId) {
        $this->groupId = (string) $groupId;
        return $this;
    }

    /**
     * Get the group ID of the concerned group.
     *
     * @see Application_Model_Person_GroupMembership::setGroupId()
     * 
     * @return string
     */
    public function getGroupId() {
        return $this->groupId;
    }

    /**
     * Set the inviter's ID.
     *
     * @param string $inviterId
     *
     * @return Application_Model_Person_GroupMembership
     */
    public function setInviterId($inviterId) {
        $this->inviterId = (string) $inviterId;
        return $this;
    }

    /**
     * Get the inviter's ID.
     *
     * @see Application_Model_Person_GroupMembership::setInviterId()
     * 
     * @return string
     */
    public function getInviterId() {
        return $this->inviterId;
    }

    /**
     * Set the person's ID.
     * 
     * @param string $personId
     * 
     * @return Application_Model_Person_GroupMembership
     */
    public function setPersonId($personId) {
        $this->personId = (string) $personId;
        return $this;
    }

    /**
     * Get the person's ID.
     *
     * @see Application_Model_Person_GroupMembership::setPersonId()
     *
     * @return string
     */
    public function getPersonId() {
        return $this->personId;
    }

    /**
     * Set a flag indicating whether a person is admin of a group or not.
     *
     * @param bool $adminRole
     * 
     * @return Application_Model_Person_GroupMembership
     */
    public function setAdminRole($adminRole) {
        $this->adminRole = (bool) $adminRole;
        return $this;
    }

    /**
     * Get a flag indicating whether a person is admin in a group or not.
     *
     * @see Application_Model_Person_GroupMembership::setAdminRole()
     *
     * @return bool
     */
    public function getAdminRole() {
        return $this->adminRole;
    }

    /**
     * Set the membership status of a person in a group.
     *
     * @param string $status
     * 
     * @return Application_Model_Person_GroupMembership
     */
    public function setStatus($status) {
        $this->status = (string) $status;
        return $this;
    }

    /**
     * Get the membership status of a person in a group.
     *
     * @see Application_Model_Person_GroupMembership::setStatus()
     *
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

}