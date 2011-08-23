<?php
/**
 * Handles requests to the /people/<user_id>/@self module of the ASI platform.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_Person extends Application_Model_Mapper_Person_Abstract {
    /**
     * Class constructor.
     *
     * @param Application_Model_Person $person The person whose data to manipulate i.e.
     * create, fetch, update or delete.
     */
    public function  __construct(Application_Model_Person $person = null) {
        if ($person instanceof Application_Model_Person) {
            $this->setPerson($person);
        }
    }

    /**
     * Fetch a person's data from  the ASI platform. This executes a GET request on
     * /people/<user id>/@self/.
     *
     * @param string $id The person's ID. Either this parameter should be provided
     * or a {@link Application_Model_Person} instance set before calling this method, from
     * which the person's ID can be retrieved.
     *
     * This then means that if this parameter is ommitted, a {@link Application_Model_Person}
     * instance must have been set either with {@link Application_Model_Mapper_Person::__construct()}
     * or {@link Application_Model_Mapper_Person_Abstract::setPerson()}, and that this instance
     * must have an ID such that {@link Application_Model_Person::getId()} returns a value.
     *
     * @see Application_Model_Person_Abstract::setPerson()
     *
     * @return Application_Model_Person With it's data updated with the response from the ASI platform
     * if the request was successful.
     *
     * @throws Application_Model_Mapper_Person_Exception If:
     *      - ID has not been set and cannot be retrieved with {@link Application_Model_Person::getId()}.
     *      - The person specified by ID does not exist (a 404 HTTP response code from the ASI platform).
     *      - Some other error occurred such that the request was unsuccessful.
     *      - The request was successful, but for some unanticipated reason response data was not received.
     */
    public function fetch($id = null) {
        $person = $this->getPerson();
        $id     = $id ? (string) $id : $person->getId();
        if ($id) {
            $client = $this->getClient();
            if ($client->sendRequest("/people/{$id}/@self", 'get')->isSuccessful()) {
                $response = $client->getResponseBody();
                if (isset($response['entry'])) {
                    return $person->setData($response['entry']);
                } else {
                    throw new Application_Model_Mapper_Person_Exception('Unexpected error: data not received');
                }
            } else {
                $code = $client->getResponseCode();
                if ($code == 404) {
                    throw new Application_Model_Mapper_Person_Exception('Person does not exist: ' . $client->getResponseMessage(), 404);
                } else {
                    throw new Application_Model_Mapper_Person_Exception('Could not fetch person: ' . $client->getResponseMessage(), $code);
                }
            }
        } else {
            throw new Application_Model_Mapper_Person_Exception('Person ID must be set');
        }
    }

    /**
     * Create a person record on the ASI platform. This executes a POST request on /people.
     *
     * Note that if successful this method will also automatically log in the new
     * user.
     *
     * @param array $data The data to create a new person. This array must contain values for
     * these keys: 'username', 'password', 'email', 'consent' and an optional 'is_association'.
     *
     * If this parameter is not provided, this method will try to get data from a person object
     * using {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Abstract::getData()}. This approach is not advised as the data
     * fetched will also contain other fields like 'name', 'address', 'connection' etc which will
     * be empty and that are not used for creating a person.
     *
     * @param boolean $welcomeEmail Whether to send a welcome email to the newly created person or not.
     *
     * @return Application_Model_Person With it's data updated with the response from the ASI platform
     * if the request was successful.
     *
     * @throws Application_Model_Mapper_Person_Exception If:
     *      - Data array has not been passed and cannot be retrieved with
     *          {@link Application_Model_Abstract::getData()}.
     *      - The keys 'username', 'password', 'email', 'consent' and their values are not
     *          present in the data array.
     *      - The request was not successful. In this case if the ASI platform
     *          returned any error messages, they can be retrieved using
     *          {@link Application_Model_Mapper_Abstract::getErrors()}.
     *      - The request was successful, but for some unanticipated response reason data was not received.
     */
    public function create(array $data = null, $welcomeEmail = true) {
        $person = $this->getPerson();
        $data   = $data ? $data : $person->getData();
        if (is_array($data)) {
            $keys = array_keys($data);
            if (!isset($keys['username'], $keys['password'], $keys['email'], $keys['consent']) &&
                    !isset($data['username'], $data['password'], $data['email'], $data['consent'])) {
                throw new Application_Model_Mapper_Person_Exception("These keys (and their values) must exist to create a new person: 'username', 'password', 'email', 'consent'");
            }
            $client = $this->getClient();
            $data   = $client->encodeData($data, 'person');
            $data   = $welcomeEmail ? $data : $data . "?welcome_email=false";
            if ($client->sendRequest('/people', 'post', $data)->isSuccessful()) {
                $response = $client->getResponseBody();
                if (!isset($response['entry'])) {
                    throw new Application_Model_Mapper_Person_Exception('Unexpected error: data not received');
                }
                return $person->setData($response['entry']);
            } else {
                $response = $client->getResponseBody();
                if (isset($response['messages'])) {
                    $this->setErrors($response['messages']);
                }
                throw new Application_Model_Mapper_Person_Exception('Could not create person: ' . $client->getResponseMessage(), $client->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Person_Exception('No data to create user');
        }
    }

    /**
     * Updates a person record on the ASI platform. This executes a PUT request on /people.
     *
     * @param array $data The data to create a new person. This array must contain values for
     * these keys: 'username', 'password', 'email', 'consent' and an optional 'is_association'.
     *
     * If this parameter is not provided, this method will try to get data from a person object
     * using {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Abstract::getData()}. This approach is not advised as the data
     * fetched will also contain other fields like 'name', 'address', 'connection' etc which will
     * be empty and that are not used for creating a person.
     *
     * @param string $id The person's ID. Either this parameter should be provided
     * or a {@link Application_Model_Person} instance set before calling this method, from
     * which the person's ID can be retrieved.
     *
     * This then means that if this parameter is ommitted, a {@link Application_Model_Person}
     * instance must have been set either with {@link Application_Model_Mapper_Person::__construct()}
     * or {@link Application_Model_Mapper_Person_Abstract::setPerson()}, and that this instance
     * must have an ID such that {@link Application_Model_Person::getId()} returns a value.
     *
     * @return Application_Model_Person With it's data updated with the response from the ASI platform
     * if the request was successful.
     *
     * @throws Application_Model_Mapper_Person_Exception If:
     *      - Data array has not been passed and cannot be retrieved with
     *          {@link Application_Model_Abstract::getData()}.
     *      - ID has not been set and cannot be retrieved with {@link Application_Model_Person::getId()}.
     *      - The request was not successful. In this case if the ASI platform
     *          returned any error messages, they can be retrieved using
     *          {@link Application_Model_Mapper_Abstract::getErrors()}.
     *      - The request was successful, but for some unanticipated response reason data was not received.
     */
    public function update(array $data = null, $id = null) {
        $person = $this->getPerson();
        $data   = $data ? $data : $person->getData();
        if (is_array($data)) {
            $id = $id ? (string) $id : $person->getId();
            if ($id) {
                $client = $this->getClient();
                if ($client->sendRequest("/people/{$id}/@self", 'put', $data, 'person')->isSuccessful()) {
                    $response = $client->getResponseBody();
                    if (!isset($response['entry'])) {
                        throw new Application_Model_Mapper_Person_Exception('Unexpected error: data was not received');
                    }
                    return $person->setData($response['entry']);
                } else {
                    $response = $client->getResponseBody();
                    if (isset($response['messages'])) {
                        $this->setErrors($response['messages']);
                    }
                    throw new Application_Model_Mapper_Person_Exception('Could not update person: ' . $client->getResponseMessage(), $client->getResponseCode());
                }
            } else {
                throw new Application_Model_Mapper_Person_Exception('Person ID must be set');
            }
        } else {
            throw new Application_Model_Mapper_Person_Exception('No data to update');
        }
    }

    /**
     * Deletes a person record on the ASI platform. This executes a DELETE request on /people.
     *
     * @param string $id The person's ID. Either this parameter should be provided
     * or a {@link Application_Model_Person} instance set before calling this method, from
     * which the person's ID can be retrieved.
     *
     * This then means that if this parameter is ommitted, a {@link Application_Model_Person}
     * instance must have been set either with {@link Application_Model_Mapper_Person::__construct()}
     * or {@link Application_Model_Mapper_Person_Abstract::setPerson()}, and that this instance
     * must have an ID such that {@link Application_Model_Person::getId()} returns a value.
     *
     * @return Application_Model_Person With it's data updated with the response from the ASI platform
     * if the request was successful.
     *
     * @throws Application_Model_Mapper_Person_Exception If:
     *      - ID has not been set and cannot be retrieved with {@link Application_Model_Person::getId()}.
     *      - The request was not successful.
     *      - The request was successful, but for some unanticipated response reason data was not received.
     */
    public function delete($id = null) {
        $id = $id ? (string) $id : $this->getPerson()->getId();
        if ($id) {
            $client = $this->getClient();
            if ($client->sendRequest("/people/{$id}/@self", 'delete')->isSuccessful()) {
                return true;
            } else {
                throw new Application_Model_Mapper_Person_Exception('Error deleting user: ' . $client->getResponseMessage(), $client->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Person_Exception('Person ID must be set');
        }
    }

    /**
     * Checks whether a person's username and/or email are avaiable on the ASI
     * platform. This method passes the request along to
     * {@link Application_Model_Mapper_Person_Availability::fetch()}.
     * 
     * @param string $username The person's username. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getUsername()} to get the person's username. To
     * avoid checking for username availability i.e. check for email availability
     * alone, pass 'null' value.
     *
     * @param srting $email The person's email. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getUsername()} to get the person's email. To
     * avoid checking for email availability i.e. check for username availability
     * alone, pass 'null' value.
     * 
     * @return true|false True if available, false if not.
     */
    public function isAvailable($username = null, $email = null) {
        $availabilityMapper = new Application_Model_Mapper_Person_Availability();
        $availability       = $availabilityMapper->setPerson($this->getPerson())
                                                 ->fetch($username, $email);
        $usernameAvailability = $availability->getUsername();
        $emailAvailability    = $availability->getEmail();
        if (($usernameAvailability && $usernameAvailability === 'available') && 
                ($emailAvailability && $emailAvailability === 'available')) {
            return true;
        } elseif (($usernameAvailability && $usernameAvailability === 'unavailable') &&
                ($emailAvailability && $emailAvailability === 'unavailable')) {
            return false;
        }
    }

    /**
     * Sends a password recovery email for a person. This method passes the request along to
     * {@link Application_Model_Mapper_Person_PasswordRecovery::create()}.
     *
     * @param srting $email The person's email. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getUsername()} to get the person's email.
     *
     * @return true If the request is successful.
     */
    public function recoverPassword($email = null) {
        $passwordRecoveryMapper = new Application_Model_Mapper_Person_PasswordRecovery();
        return $passwordRecoveryMapper->setPerson($this->getPerson())
                                                       ->create($email);
    }
}