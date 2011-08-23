<?php
/**
 * Handles requests to /people/<user id>/@location module of the ASI platform.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_Person_Location extends Application_Model_Mapper_Person_Abstract {
    /**
     * Stores the instance of {@link Application_Model_Person_Location} to work
     * with.
     *
     * @var Application_Model_Person_Location|null
     */
    protected $location = null;

    /**
     * Class constructor.
     *
     * @param Application_Model_Person_Location $location The location object to
     * work with.
     */
    public function  __construct(Application_Model_Person_Location $location = null) {
        if ($location instanceof Application_Model_Person_Location) {
            $this->setLocation($location);
        }
    }

    /**
     * Sets the instance of {@link Application_Model_Person_Location} to work
     * with.
     * 
     * @param Application_Model_Person_Location $location
     * 
     * @return Application_Model_Mapper_Person_Location
     */
    public function setLocation(Application_Model_Person_Location $location) {
        $this->location = $location;
        return $this;
    }

    /**
     * Gets the instance of {@link Application_Model_Person_Location} to work
     * with.
     *
     * @see Application_Model_Mapper_Person_Location::setLocation()
     * 
     * @return Application_Model_Person_Location
     */
    public function getLocation() {
        if (!$this->location) {
            $this->setLocation(new Application_Model_Person_Location());
        }
        return $this->location;
    }

    /**
     * Fetches a person's location from ASI. Sends a GET request to /people/<user id>/@location.
     *
     * @param string $id The person's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getId()} to get the person's ID.
     *
     * @return Application_Model_Person_Location with it's data set.
     *
     * @throws Application_Model_Mapper_Person_Location_Exception If:
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
            $client   = $this->getClient();
            $location = $this->getLocation();
            if ($client->sendRequest("/people/{$id}/@location/", 'get')->isSuccessful()) {
                $response = $client->getResponseBody();
                if (!isset($response['entry'])) {
                    throw new Application_Model_Mapper_Person_Location_Exception('Unexpected error: data not received');
                }
                return $location->setData($response['entry']);
            } else {
                throw new Application_Model_Mapper_Person_Location_Exception('Error fetching location', $client->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Person_Location_Exception('Person ID has not been set');
        }
    }

    /**
     * Sets a person's location. Sends a POST request to /people/<user id>/@location.
     * 
     * @param array $data The new location's data. Should contain values for the
     * following keys:
     *      label       => string
     *      latitude    => float
     *      longitude   => float
     *      accuracy    => float
     * If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Location::getLocation()} and then
     * {@link Application_Model_Location::getData()} to get the location's data.
     * 
     * @param string $id The person's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getId()} to get the person's ID.
     *
     * @param string $locationSecurityToken If this is provided, then the user
     * doesn't have to be logged in in order to set their location.
     *
     * @return true If the request is successful.
     *
     * @throws Application_Model_Mapper_Person_Location_Exception If:
     *      - ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person} object).
     *      - Location data has not been provided (and could not be obtained from
     *          a {@link Application_Model_Person_Location} object).
     *      - The request was not successful. In this case if any error messages
     *          were returned by ASI, they will be available using
     *          {@link Application_Model_Mapper_Abstract::getErrors()}.
     */
    public function create(array $data = null, $id = null, $locationSecurityToken = null) {
        $location = $this->getLocation();
        $data     = $data ? $data : $location->getData();
        if (is_array($data)) {
            $id   = $id ? (string) $id : $this->getPerson()->getId();
            if ($id) {
                $client = $this->getClient();
                $str    = '';
                foreach ($data as $key => $value) {
                    $str .= "location[{$key}]={$value}&";
                }
                $data = $locationSecurityToken ? $str . "location_security_token=" . $locationSecurityToken : substr($str, 0, -1);
                if ($client->sendRequest("/people/{$id}/@location", 'post', $data)->isSuccessful()) {
                    return true;
                } else {
                    $response = $client->getResponseBody();
                    if (isset($response['messages'])) {
                        $this->setErrors($response['messages']);
                    }
                    throw new Application_Model_Mapper_Person_Location_Exception('Could not create location: ' . $client->getResponseMessage(), $client->getResponseCode());
                }
            } else {
                throw new Application_Model_Mapper_Person_Location_Exception('Person ID has not been set');
            }
        } else {
            throw new Application_Model_Mapper_Person_Location_Exception('No data to create location');
        }
    }

    /**
     * Updates a person's location. Sends a PUT request to /people/<user id>/@location.
     *
     * @param array $data The new location's data. Should contain values for the
     * following keys:
     *      label       => string
     *      latitude    => float
     *      longitude   => float
     *      accuracy    => float
     * If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Location::getLocation()} and then
     * {@link Application_Model_Location::getData()} to get the location's data.
     *
     * @param string $id The person's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getId()} to get the person's ID.
     *
     * @param string $locationSecurityToken If this is provided, then the user
     * doesn't have to be logged in in order to update their location.
     *
     * @return true If the request is successful.
     *
     * @throws Application_Model_Mapper_Person_Location_Exception If:
     *      - ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person} object).
     *      - Location data has not been provided (and could not be obtained from
     *          a {@link Application_Model_Person_Location} object).
     *      - The request was not successful. In this case if any error messages
     *          were returned by ASI, they will be available using
     *          {@link Application_Model_Mapper_Abstract::getErrors()}.
     */
    public function update(array $data = null, $id = null, $locationSecurityToken = null) {
        $location = $this->getLocation();
        $data     = $data ? $data : $location->getData();
        if (is_array($data)) {
            $id   = $id ? (string) $id : $this->getPerson()->getId();
            if ($id) {
                $client = $this->getClient();
                $str    = '';
                foreach ($data as $key => $value) {
                    $str .= "location[{$key}]={$value}&";
                }
                $data   = $locationSecurityToken ? $str . "location_security_token=" . $locationSecurityToken : substr($str, 0, -1);
                if ($client->sendRequest("/people/{$id}/@location", 'put', $data)->isSuccessful()) {
                    return true;
                } else {
                    $response = $client->getResponseBody();
                    if (isset($response['messages'])) {
                        $this->setErrors($response['messages']);
                    }
                    throw new Application_Model_Mapper_Person_Location_Exception('Could not update location: ' . $client->getResponseMessage(), $client->getResponseCode());
                }
            } else {
                throw new Application_Model_Mapper_Person_Location_Exception('Person ID has not been set');
            }
        } else {
            throw new Application_Model_Mapper_Person_Location_Exception('No data to update location');
        }
    }

    /**
     * Clears a person's location. Sends a DELETE request to /people/<user id>/@location.
     *
     * @param string $id The person's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getId()} to get the person's ID.
     * 
     * @return true If the request is successful.
     *
     * @throws Application_Model_Mapper_Person_Location_Exception If:
     *      - ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person} object).
     *      - The request was not successful.
     */
    public function delete($id = null) {
        $id = $id ? (string) $id : $this->getPerson()->getId();
        if ($id) {
            $client = $this->getClient();
            if ($client->sendRequest("/people/{$id}/@location", 'delete')->isSuccessful()) {
                return true;
            } else {
                throw new Application_Model_Mapper_Person_Location_Exception('Error deleting user: ' . $client->getResponseMessage(), $client->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Person_Location_Exception('Person ID must be set');
        }
    }
}