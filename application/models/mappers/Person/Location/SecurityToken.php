<?php
/**
 * Handles requests to /people/<user id>/@location/@location_security_token
 * module of the ASI platform.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_Person_Location_SecurityToken extends Application_Model_Mapper_Person_Abstract {
    /**
     * The instance of {@link Application_Model_Person_Location_SecurityToken} to
     * work with.
     *
     * @var Application_Model_Person_Location_SecurityToken|null
     */
    protected $token = null;

    /**
     * Class constructor.
     * 
     * @param Application_Model_Person_Location_SecurityToken $token The location
     * security token to work with.
     */
    public function  __construct(Application_Model_Person_Location_SecurityToken $token = null) {
        if ($token instanceof Application_Model_Person_Location_SecurityToken) {
            $this->setLocationSecurityToken($token);
        }
    }

    /**
     * Sets the instance of {@link Application_Model_Person_Location_SecurityToken}
     * to work with.
     * 
     * @param Application_Model_Person_Location_SecurityToken $token
     *
     * @return Application_Model_Mapper_Person_Location_SecurityToken
     */
    public function setLocationSecurityToken(Application_Model_Person_Location_SecurityToken $token) {
        $this->token = $token;
        return $this;
    }

    /**
     * Sets the instance of {@link Application_Model_Person_Location_SecurityToken}
     * to work with.
     *
     * @see Application_Model_Mapper_Person_Location_SecurityToken::setLocationSecurityToken()
     * 
     * @return Application_Model_Person_Location_SecurityToken
     */
    public function getLocationSecurityToken() {
        if (!$this->token) {
            $this->setLocationSecurityToken(new Application_Model_Person_Location_SecurityToken());
        }
        return $this->token;
    }

    /**
     * Fetches a person's location security token from ASI. Sends a GET request
     * to /people/<user id>/@location/@location_security_token.
     *
     * @param string $id The person's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getId()} to get the person's ID.
     *
     * @return Application_Model_Person_Location_SecurityToken with it's data set.
     *
     * @todo ASI says it returns $data['entry']['location_security_token'],
     * instead this request returns $data['location_security_token'].
     * 
     * @throws Application_Model_Mapper_Person_Location_SecurityToken_Exception
     * If:
     *      - ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person} object).
     *      - The request was not successful.
     *      - The reques was successful but for some unknown reason data was not
     *          received.
     */
    public function fetch($id = null) {
        $id = $id ? (string) $id : $this->getPerson()->getId();
        if ($id) {
            $client = $this->getClient();
            $token  = $this->getLocationSecurityToken();
            if ($client->sendRequest("/people/{$id}/@location/@location_security_token", 'get')->isSuccessful()) {
                $data = $client->getResponseBody();
                if (isset($data['location_security_token'])) {
                    return $token->setData($data);
                } else {
                    throw new Application_Model_Mapper_Person_Location_SecurityToken_Exception('Unexpected error: data not recevied');
                }
            } else {
                throw new Application_Model_Mapper_Person_Location_SecurityToken_Exception('Error fetching location security token: ' . $client->getResponseMessage(), $client->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Person_Location_SecurityToken_Exception('Person ID has not been set');
        }
    }

    /**
     * @throws Application_Model_Mapper_Person_Location_SecurityToken_Exception
     * Unsupported method.
     */
    public function create() {
        throw new Application_Model_Mapper_Person_Location_SecurityToken_Exception('Unsupported method Application_Model_Mapper_Person_Location_SecurityToken::create()');
    }

    /**
     * @throws Application_Model_Mapper_Person_Location_SecurityToken_Exception
     * Unsupported method.
     */
    public function update() {
        throw new Application_Model_Mapper_Person_Location_SecurityToken_Exception('Unsupported method Application_Model_Mapper_Person_Location_SecurityToken::update()');
    }

    /**
     * @throws Application_Model_Mapper_Person_Location_SecurityToken_Exception
     * Unsupported method.
     */
    public function delete() {
        throw new Application_Model_Mapper_Person_Location_SecurityToken_Exception('Unsupported method Application_Model_Mapper_Person_Location_SecurityToken::delete()');
    }
}