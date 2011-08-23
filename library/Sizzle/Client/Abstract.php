<?php
/**
 * Base class providing core functionality to Sizzle_Client classes.
 *
 * All Sizzle_Client classes proxy their requests through {@link Zend_Http_Client}.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Library
 * @subpackage Client
 */
abstract class Sizzle_Client_Abstract implements Sizzle_Client_Interface {
    /**
     * Stores the client object used to make requests.
     *
     * @var Zend_Http_Client|null
     */
    protected $client = null;

    /**
     * Stores the HTTP response from a request.
     *
     * @var Zend_Http_Response|null
     */
    protected $response = null;

    /**
     * Stores an instance of {@link Application_Model_Client}.
     *
     * @var Application_Model_Client|null
     */
    protected static $instance = null;

    /**
     * Class constructor. Access is set to 'private' to enforce singleton.
     */
    private function  __construct() {

    }

    /**
     * Returns an instance of {@link Application_Model_Client}.
     *
     * @return Application_Model_Client
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Set the client object. By default this method sets the 'keepalive' property
     * for {@link Zend_Http_Client::setConfig()} to 'true'. This is to create a
     * session between the client and the ASI platform server.
     *
     * This (and other) property can be changed using {@link Application_Model_Client::setConfig()}.
     *
     * @return Application_Model_Client
     *
     * @throws Application_Model_Client_Exception If this is not successful.
     */
    public function setClientObject() {
        try {
            $client = new Zend_Http_Client();
            //set the 'keepalive' configuration
            //initialize a new cookie jar for maintaining session once logged in
            $client->setConfig(array(
                    'keepalive'   => true
            ));
            $this->client = $client;
        } catch (Zend_Http_Client_Exception $e) {
            throw new Application_Model_Client_Exception('Could not create the client request object', 0, $e);
        }
        return $this;
    }

    /**
     * Get the client object. This allows for advanced usage of {@link
     * Zend_Http_Client}, for example:
     *
     * <code>
     *      $client = Application_Model_Client::getInstance();
     *      $client->getClientObject()->setCookie('cookie', 'value');
     *      //or
     *      $client->getClientObject()->setAdapter(new Zend_Http_Client_Adapter_Proxy());
     *      //etc
     * </code>
     *
     * With this method, you can totally override the request implementation of
     * {@link Application_Model_Client} and fall back to {@link Zend_Http_Client}'s
     * implementation. For example:
     *
     * <code>
     *      $client = Application_Model_Client::getInstance();
     *      $client->getClientObject()->setUri('http://example.com')
     *                                ->setConfig(array('keepalive' => true))
     *                                ->setParameterGet('search', 'search terms')
     *                                ->request();
     * </code>
     *
     * @return Zend_Http_Client
     */
    public function getClientObject() {
        if (!$this->client) {
            $this->setClientObject();
        }
        return $this->client;
    }

    /**
     * Sets the base URI to use for this (and subsequent) requests.
     *
     * @see Application_Model_Client::baseUri
     *
     * @param string $baseUri
     *
     * @return Application_Model_Client
     */
    public function setBaseUri($baseUri) {
        $this->baseUri = $baseUri;
        return $this;
    }

    /**
     * Get the base URI set for making requests.
     *
     * @return string
     */
    public function getBaseUri() {
        return $this->baseUri;
    }

    /**
     * Set the request configuration to use for this (and subsequent) requests.
     *
     * @param array $config This should match the configuration parameter that
     * {@link Zend_Http_Client::setConfig()} expects. For example:
     *
     * <code>
     *      $client = Application_Model_Client::getInstance();
     *      $client->getClientObject()->setConfig(array('keepalive' => true);
     * </code>
     *
     * @return Application_Model_Client
     *
     * @throws Application_Model_Client_Exception If this is not successful.
     */
    public function setConfig(array $config) {
        try {
            $this->getClientObject()
                    ->setConfig($config);
        } catch (Zend_Http_Client_Exception $e) {
            throw new Application_Model_Client_Exception('Could not set the URI for the client request object', 0, $e);
        }
        return $this;
    }

    /**
     * Check whether a request is successful or not. This method interprets successful
     * requests as only those in the 1xx to 2xx range, so be careful when using this to
     * validate your requests. For example:
     *
     * <code>
     *      $client = Application_Model_Client::getInstance();
     *      $client->sendRequest('/session', 'get');
     * </code>
     *
     * will return a 404 if no session exists, but that doesn't mean the request
     * was unsuccessful.
     *
     * @return boolean
     */
    public function isSuccessful() {
        return $this->getResponseObject()->isSuccessful();
    }

    /**
     * Get the raw response object from the request sent. This allows for advanced
     * usage of {@link Zend_Http_Response}, for example:
     *
     * <code>
     *      $client = Application_Model_Client::getInstance();
     *      $statusText = $client->sendRequest('/session', 'get')
     *                           ->getResponseObject()
     *                           ->responseTextAsString($client->getResponseCode());
     * </code>
     *
     * @return Zend_Http_Response
     *
     * @throws Application_Model_Client_Exception If no response exists.
     */
    public function getResponseObject() {
        if (!$this->response) {
            throw new Application_Model_Client_Exception('No response exists. Have you sent a request?');
        }
        return $this->response;
    }

    /**
     * Get the HTTP status code from the request.
     *
     * @return int
     */
    public function getResponseCode() {
        return $this->getResponseObject()
                ->getStatus();
    }

    /**
     * Get the message describing the HTTP response code from the request.
     *
     * @return string
     */
    public function getResponseMessage() {
        return $this->getResponseObject()
                ->getMessage();
    }

    /**
     * Get the response body from the request.
     *
     * @param string $type The format to use to decode the returned response
     * body.
     *
     * Allowed formats are:
     *      - array
     *      - object
     *      - json
     *
     * Specifying any other format will cause the response body to be returned
     * in JSON format by default.
     *
     * Defaults to 'array' if not provided.
     *
     * @return string|array|stdClass
     *
     * @throws Application_Model_Client_Exception If the response cannot be decoded.
     */
    public function getResponseBody($type = 'array') {
        $body = $this->getResponseObject()
                ->getBody();

        switch (strtolower((string) $type)) {
            case 'array':
                try {
                    return Zend_Json::decode($body, Zend_Json::TYPE_ARRAY);
                } catch (Zend_Http_Client_Exception $e) {
                    throw new Application_Model_Client_Exception("Could not decode the response to type 'array'", 0, $e);
                }
                break;

            case 'object':
                try {
                    return Zend_Json::decode($body, Zend_Json::TYPE_OBJECT);
                } catch (Zend_Http_Client_Exception $e) {
                    throw new Application_Model_Client_Exception("Could not decode the response to type 'object'", 0, $e);
                }
                break;

            case 'json':
            default:
                return $body;
                break;
        }
    }
}
