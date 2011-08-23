<?php
/**
 * Handles requests to /people/<user id>/@friends module of the ASI platform.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_Person_Friends extends Application_Model_Mapper_Person_Abstract {
    /**
     * Stores the instance of {@link Application_Model_Person} that holds the
     * friend's data.
     *
     * @var Application_Model_Person|null
     */
    protected $friend = null;

    /**
     * Class constructor.
     * 
     * @param Application_Model_Person $friend The friend object to work with.
     */
    public function  __construct(Application_Model_Person $friend = null) {
        if ($friend instanceof Application_Model_Person) {
            $this->setFriend($friend);
        }
    }

    /**
     * Set the instance of {@link Application_Model_Person} that holds the
     * friend's data.
     * 
     * @param Application_Model_Person $friend
     * 
     * @return Application_Model_Mapper_Person_Friends
     */
    public function setFriend(Application_Model_Person $friend) {
        $this->friend = $friend;
        return $this;
    }

    /**
     * Get the instance of {@link Application_Model_Person} that holds the
     * friend's data.
     *
     * @see Application_Model_Mapper_Person_Friends::setFriend()
     * 
     * @return Application_Model_Person
     */
    public function getFriend() {
        if (!$this->friend) {
            $this->setFriend(new Application_Model_Person());
        }
        return $this->friend;
    }

    /**
     * Fetches all of a person's friends from ASI. Sends a GET request to /people/<user id>/@friends.
     *
     * The records to be returned can be customized by calling
     * {@link Application_Model_Mapper_Search_Abstract::setPage()} to set the page number to fetch,
     * {@link Application_Model_Mapper_Search_Abstract::setPerPage()} to set the number of records
     * to view per page, {@link Application_Model_Search_Mapper_Abstract::setSortBy()} to set the
     * criteria to use in sorting records and {@link Application_Model_Mapper_Search_Abstract::setSortOrder()}
     * to set the order to use in sorting records before calling this method.
     *
     * If any of these customizations are used, ASI returns pagination data which can
     * be accessed using {@link Application_Model_Mapper_Abstract::getPagination()}.
     *
     * @param string $id The person's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getId()} to get the person's ID.
     *
     * @return array Of {@link Application_Model_Person}s with thier data set.
     * 
     * @throws Application_Model_Mapper_Person_Friends_Exception If:
     *      - ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person} object).
     *      - The request was not successful. In this case if any error messages
     *          were returned by ASI, they will be available using
     *          {@link Application_Model_Mapper_Abstract::getErrors()}.
     *      - The reques was successful but for some unknown reason data was not
     *          received.
     */
    public function fetch($id = null) {
        $id = $id ? (string) $id : $this->getPerson()->getId();
        if ($id) {
            $client      = $this->getClient();
            $page        = $this->getPage();
            $perPage     = $this->getPerPage();
            $sortBy      = $this->getSortBy();
            $sortOrder   = $this->getSortOrder();
            $queryString = '';
            if ($page) {
                $queryString = $queryString ? $queryString . "&page={$page}" : "?page={$page}";
            }
            if ($perPage) {
                $queryString = $queryString ? $queryString . "&per_page={$perPage}" : "?per_page={$perPage}";
            }
            if ($sortBy) {
                $queryString = $queryString ? $queryString . "&sortBy={$sortBy}" : "?sortBy={$sortBy}";
            }
            if ($sortOrder) {
                $queryString = $queryString ? $queryString . "&sortOrder={$sortOrder}" : "?sortOrder={$sortOrder}";
            }
            if ($client->sendRequest("/people/{$id}/@friends" . $queryString, 'get')->isSuccessful()) {
                $data    = $client->getResponseBody();
                $friends = array();
                if (isset($data['pagination'])) {
                    $this->setPagination(new Application_Model_Pagination($data['pagination']));
                }
                if (!isset($data['entry'])) {
                    throw new Application_Model_Mapper_Person_Friends_Exception('Unexpected error: data not received');
                }
                foreach ($data['entry'] as $friend) {
                    $friends[] = new Application_Model_Person($friend);
                }
                return $friends;
            } else {
                $response = $client->getResponseBody();
                if (isset($response['messages'])) {
                    $this->setErrors($response['messages']);
                }
                throw new Application_Model_Mapper_Person_Friends_Exception('Error fetching friends: ' . $client->getResponseMessage(), $client->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Person_Friends_Exception('Person ID must be set');
        }
    }

    /**
     * Creates a new friendship connection. Sends a POST request to /people/<user id>/@friends.
     *
     * @param string $friendId The friend's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Friends::getFriend()} and then
     * {@link Application_Model_Person::getId()} to get the friend's ID.
     *
     * @param string $id The person's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getId()} to get the person's ID.
     * 
     * @return true If the request was successful.
     *
     * @throws Application_Model_Mapper_Person_Friends_Exception If:
     *      - Friend's ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person} object).
     *      - Person's ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person} object).
     *      - The request was not successful. In this case if any error messages
     *          were returned by ASI, they will be available using
     *          {@link Application_Model_Mapper_Abstract::getErrors()}.
     */
    public function create($friendId = null, $id = null) {
        $friendId = $friendId ? (string) $friendId : $this->getFriend()->getId();
        if ($friendId) {
            $id = $id ? $id : $this->getPerson()->getId();
            if ($id) {
                $client = $this->getClient();
                if ($client->sendRequest("/people/{$id}/@friends", 'post', "friend_id={$friendId}")->isSuccessful()) {
                    return true;
                } else {
                    $response = $client->getResponseBody();
                    if (isset($response['messages'])) {
                        $this->setErrors($response['messages']);
                    }
                    throw new Application_Model_Mapper_Person_Friends_Exception('Error creating friend connection: ' . $client->getResponseMessage(), $client->getResponseCode());
                }
            } else {
                throw new Application_Model_Mapper_Person_Friends_Exception('Person ID must be set');
            }
        } else {
            throw new Application_Model_Mapper_Person_Friends_Exception('Friend ID must be set');
        }
    }

    /**
     * @throws Application_Model_Mapper_Person_Friends_Exception Unsupported method.
     */
    public function update() {
        throw new Application_Model_Mapper_Person_Friends_Exception('Unsupported method Application_Model_Mapper_Person_Friends::update()');
    }

    /**
     * Deletes a friendship connection. Sends a DELETE request to /people/<user id>/@friends/<friend id>.
     *
     * @param string $friendId The friend's ID. If not provided, this method will call
     * {@link Application_Model_Model_Person_Friends::getFriend()} and then
     * {@link Application_Model_Person::getId()} to get the friend's ID.
     *
     * @param string $id The person's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getId()} to get the person's ID.
     *
     * @return true If the request was successful.
     *
     * @throws Application_Model_Mapper_Person_Friends_Exception If:
     *      - Friend's ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person} object).
     *      - Person's ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person} object).
     *      - The request was not successful.
     */
    public function delete($friendId = null, $id = null) {
        $friendId = $friendId ? (string) $friendId : $this->getFriend()->getId();
        if ($friendId) {
            $id = $id ? $id : $this->getPerson()->getId();
            if ($id) {
                $client = $this->getClient();
                if ($client->sendRequest("/people/{$id}/@friends/{$friendId}", 'delete')->isSuccessful()) {
                    return true;
                } else {
                    throw new Application_Model_Mapper_Person_Friends_Exception('Error deleting friend connection: ' . $client->getResponseMessage(), $client->getResponseCode());
                }
            } else {
                throw new Application_Model_Mapper_Person_Friends_Exception('Person ID must be set');
            }
        } else {
            throw new Application_Model_Mapper_Person_Friends_Exception('Friend ID must be set');
        }
    }
}