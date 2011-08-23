<?php
/**
 * Handles requests to /people/<user id>/@groups/<group id> module of the ASI platform.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_Person_GroupMembership extends Application_Model_Mapper_Person_Abstract {
    /**
     * Stores an instance of {@link Application_Model_Person_GroupMembership} to
     * work with.
     *
     * @var Application_Model_Person_GroupMembership|null
     */
    protected $groupMembership = null;

    /**
     * Class constructor.
     * 
     * @param Application_Model_Person_GroupMembership $groupMembership Group membership
     * object to work with.
     */
    public function  __construct(Application_Model_Person_GroupMembership $groupMembership = null) {
        if ($groupMembership instanceof Application_Model_Person_GroupMembership) {
            $this->setGroupMembership($groupMembership);
        }
    }

    /**
     * Set the instance of {@link Application_Model_Person_GroupMembership} to
     * work with.
     * 
     * @param Application_Model_Person_GroupMembership $groupMembership
     * 
     * @return Application_Model_Mapper_Person_GroupMembership
     */
    public function setGroupMembership(Application_Model_Person_GroupMembership $groupMembership) {
        $this->groupMembership = $groupMembership;
        return $this;
    }

    /**
     * Get the instance of {@link Application_Model_Person_GroupMembership} to
     * work with.
     *
     * @see Application_Model_Mapper_Person_GroupMembership::setGroupMembership()
     *
     * @return Application_Model_Person_GroupMembership
     */
    public function getGroupMembership() {
        if (!$this->groupMembership) {
            $this->setGroupMembership(new Application_Model_Person_GroupMembership());
        }
        return $this->groupMembership;
    }

    /**
     * Fetches a person's group membership status from ASI. Sends a GET request
     * to /people/<user id>/@groups/<group id>.
     *
     * @param string $userId The person's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getId()} to get the person's ID.
     *
     * @param string $groupId The group's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_GroupMembership::getGroupMembership()}
     * and then {@link Application_Model_Person_GroupMembership::getGroupId()}
     * to get the group's ID.
     *
     * @return array Of {@link Application_Model_Person_GroupMembership}s with
     * their data set.
     *
     * @throws Application_Model_Mapper_Person_GroupMembership_Exception If:
     *      - Person's ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person} object)
     *      - Group's ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person_GroupMembership} object)
     *      - The request was not successful.
     *      - The reques was successful but for some unknown reason data was not
     *          received.
     */
    public function fetch($userId = null, $groupId = null) {
        $userId  = $userId ? (string) $userId : $this->getPerson()->getId();
        if (!$userId) {
            throw new Application_Model_Exception('Person ID has not been set');
        }
        $groupMembership = $this->getGroupMembership();
        $groupId = $groupId ? (string) $groupId : $groupMembership->getGroupId();
        if (!$groupId) {
            throw new Application_Model_Exception('Group ID has not been set');
        }
        $client = $this->getClient();
        if ($client->sendRequest("/people/{$userId}/@groups/{$groupId}", 'get')->isSuccessful()) {
            $response = $client->getResponseBody();
            if (!isset($response['entry'])) {
                throw new Application_Model_Mapper_Person_GroupMembership_Exception('Unexpected error: data not recevied');
            }
            return $groupMembership->setData($response['entry']);
        } else {
            throw new Application_Model_Mapper_Person_GroupMembership_Exception("Error fetching location security token: " . $client->getResponseMessage(), $client->getResponseCode());
        }
    }

    /**
     * Creates a new group membership for a person. In essence this is sending an
     * invite/request to join a group. Sends a POST request to /people/<user id>/@groups.
     *
     * @param string $userId The person's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getId()} to get the person's ID.
     *
     * @param string $groupId The group's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_GroupMembership::getGroupMembership()}
     * and then {@link Application_Model_Person_GroupMembership::getGroupId()}
     * to get the group's ID.
     *
     * @return true If the request was completed successfully.
     *
     * @todo How to handle the various possibilities of outcomes involved with
     * this request e.g. if request completes successfully, a 201 or 202 response
     * code is returned, meaning different things.
     *
     * @throws Application_Model_Mapper_Person_GroupMembership_Exception If:
     *      - Person's ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person} object)
     *      - Group's ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person_GroupMembership} object)
     *      - The request was not successful.
     *      - The reques was successful but for some unknown reason data was not
     *          received.
     */
    public function create($userId = null, $groupId = null) {
        $userId  = $userId ? (string) $userId : $this->getPerson()->getId();
        if (!$userId) {
            throw new Application_Model_Person_GroupMembership_Mapper_Exception('Person ID has not been set');
        }
        $groupId = $groupId ? (string) $groupId : $this->getGroupMembership()->getGroupId();
        if (!$groupId) {
            throw new Application_Model_Mapper_Person_GroupMembership_Exception('Group ID has not been set');
        }
        $client  = $this->getClient();
        if ($client->sendRequest("/people/{$userId}/@groups", 'post', "group_id={$groupId}")->isSuccessful()) {
            return true;
        } else {
            $response = $client->getResponseBody();
            if (isset($response['messages'])) {
                $this->setErrors($response['messages']);
            }
            throw new Application_Model_Mapper_Person_GroupMembership_Exception('Could not create person\'s group invitation: ' . $client->getResponseMessage(), $client->getResponseCode());
        }
    }

    /**
     * Updates a person's group membership status. Sends a POST request to
     * /people/<user id>/@groups/<group id>.
     *
     * @param bool $acceptRequest Whether to accept a group invitation or not. To
     * force the mapper to ignore this parameter when sending the request, provide
     * a 'null' value.
     *
     * @param bool $adminStatus Whether to make this person a group admin or not.
     * To force the mapper to ignore this parameter when sending the request, provide
     * a 'null' value.
     *
     * @param string $userId The person's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getId()} to get the person's ID.
     *
     * @param string $groupId The group's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_GroupMembership::getGroupMembership()}
     * and then {@link Application_Model_Person_GroupMembership::getGroupId()}
     * to get the group's ID.
     *
     * @return true If the request was completed successfully.
     *
     * @throws Application_Model_Mapper_Person_GroupMembership_Exception If:
     *      - Person's ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person} object)
     *      - Group's ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person_GroupMembership} object)
     *      - The request was not successful. In this case if any error messages
     *          were returned by ASI, they will be available using
     *          {@link Application_Model_Mapper_Abstract::getErrors()}.
     */
    public function update($acceptRequest = null, $adminStatus = null, $userId = null, $groupId = null) {
        $groupMembership = $this->getGroupMembership();
        $userId          = $userId ? (string) $userId : $this->getPerson()->getId();
        if (!$userId) {
            throw new Application_Model_Person_GroupMembership_Mapper_Exception('Person ID has not been set');
        }
        $groupId         = $groupId ? (string) $groupId : $groupMembership->getId();
        if (!$groupId) {
            throw new Application_Model_Person_GroupMembership_Mapper_Exception('Group ID has not been set');
        }
        $acceptRequest   = !is_null($acceptRequest) ? $acceptRequest : null;
        $adminStatus     = !is_null($adminStatus) ? $adminStatus : null;
        $data            = '';
        $data            = $acceptRequest ? "accept_request={$acceptRequest}" : '';
        $data           .= $adminStatus ? $data ? "&admin_status={$adminStatus}" : "admin_status={$adminStatus}" : '';
        $client          = $this->getClient();
        if ($client->sendRequest("/people/{$userId}/@groups/{$groupId}", 'put', $data)->isSuccessful()) {
            return true;
        } else {
            $response = $client->getResponseBody();
            if (isset($response['messages'])) {
                $this->setErrors($response['messages']);
            }
            throw new Application_Model_Mapper_Person_GroupMembership_Exception('Could not update person\'s group membership: ' . $client->getResponseMessage(), $client->getResponseCode());
        }
    }

    /**
     * Removes a person from a group. Sends a DELETE request to
     * /people/<user id>/@groups/<group id>.
     *
     * @param string $userId The person's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getId()} to get the person's ID.
     *
     * @param string $groupId The group's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_GroupMembership::getGroupMembership()}
     * and then {@link Application_Model_Person_GroupMembership::getGroupId()}
     * to get the group's ID.
     *
     * @return true If the request was completed successfully.
     *
     * @throws Application_Model_Mapper_Person_GroupMembership_Exception If:
     *      - Person's ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person} object)
     *      - Group's ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person_GroupMembership} object)
     *      - The request was not successful. In this case if any error messages
     *          were returned by ASI, they will be available using
     *          {@link Application_Model_Mapper_Abstract::getErrors()}.
     */
    public function delete($userId = null, $groupId = null) {
        $userId  = $userId ? (string) $userId : $this->getPerson()->getId();
        if (!$userId) {
            throw new Application_Model_Mapper_Person_GroupMembership_Exception('Person ID has not been set');
        }
        $groupId = $groupId ? (string) $groupId : $this->getGroupMembership()->getId();
        if (!$groupId) {
            throw new Application_Model_Mapper_Person_GroupMembership_Exception('Group ID has not been set');
        }
        $client = $this->getClient();
        if ($client->sendRequest("/people/{$userId}/@groups/{$groupId}", 'delete')->isSuccessful()) {
            return true;
        } else {
            $response = $client->getResponseBody();
            if (isset($response['messages'])) {
                $this->setErrors($response['messages']);
            }
            throw new Application_Model_Mapper_Person_GroupMembership_Exception('Could not delete person\'s group membership: ' . $client->getResponseMessage(), $client->getResponseCode());
        }
    }

    /**
     * Accepts a group invitation. This method passes the request along to
     * {@link Application_Model_Mapper_Person_GroupMembership::update()}. As such,
     * if you wish to accept a request and also set a person as admin, use
     * {@link Application_Model_Mapper_Person_GroupMembership::update()} instead
     * to avoid making two requests.
     *
     * @param string $userId
     *
     * @param string $groupId
     *
     * @return true If the request is successful.
     */
    public function accept($userId = null, $groupId = null) {
        return $this->update(true, null, $userId, $groupId);
    }

    /**
     * Declines a group invitation. This method passes the request along to
     * {@link Application_Model_Mapper_Person_GroupMembership::update()}.
     *
     * @param string $userId
     *
     * @param string $groupId
     *
     * @return true If the request is successful.
     */
    public function decline($userId = null, $groupId = null) {
        return $this->update(false, null, $userId, $groupId);
    }

    /**
     * Sets a person as a group's admin. This method passes the request along to
     * {@link Application_Model_Mapper_Person_GroupMembership::update()}.  As such,
     * if you wish to accept a request and also set a person as admin, use
     * {@link Application_Model_Mapper_Person_GroupMembership::update()} instead
     * to avoid making two requests.
     *
     * @param string $userId
     *
     * @param string $groupId
     *
     * @return true If the request is successful.
     */
    public function setAdmin($userId = null, $groupId = null) {
        return $this->update(null, true, $userId, $groupId);
    }

    /**
     * Unsets a person as a group's admin. This method passes the request along to
     * {@link Application_Model_Mapper_Person_GroupMembership::update()}.
     *
     * @param string $userId
     *
     * @param string $groupId
     *
     * @return true If the request is successful.
     */
    public function unsetAdmin($userId = null, $groupId = null) {
        return $this->update(null, false, $userId, $groupId);
    }
}