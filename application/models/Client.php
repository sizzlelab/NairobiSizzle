<?php
/**
 * This class contains core functions that make requests to the ASI platform.
 *
 * This class proxies all requests to {@link Zend_Http_Client} and thus
 * allows for encapsulation and abstraction of the communication layer.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Client {
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
     * The base URI for all requests. This is actually the IP address of the machine
     * where the platform's server resides.
     *
     * This can be changed using {@link Application_Model_Client::setBaseUri()}.
     *
     * @var string
     */
    protected $baseUri;

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
        /*set baseUri using application.ini configs*/
        $configs       = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
        $this->baseUri = $configs['clients']['asi']['baseUri'];
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
            //set timeout to 60 seconds
            $client->setConfig(array(
                    'keepalive'   => true,
                    'timeout'     => 60
            ))
            //allow the client to accept gzipped data
            ->setHeaders('Accept-encoding', 'gzip,deflate');
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
     * Send a request. Note that this method is not used to do file uploads. For
     * that you would have to use {@link Zend_Http_Client::setFileUpload()} before
     * calling this method to send the request. For example:
     *
     * <code>
     *      $client = Application_Model_Client::getInstance();
     *      //this method expects at least two parameters, a filename and the
     *      //name of the form. The name of the form would be what ASI expects
     *      //e.g. for this request, ASI excpects an upload labelled 'file'.
     *      $client->getClientObject()->setFileUpload($filename, 'file');
     *      //send the upload
     *      $client->sendRequest('/people/<user id>/@avatar', 'post);
     * </code>
     *
     * Note that in order to fix some untraceable bug with all PUT requests, this
     * method adds a parameter '_method' with value 'PUT' to the data and instead
     * sends the request as a POST.
     *
     * @param string $requestUri The URI to send the request to *without* the
     * base URI, for example:
     *
     * <code>
     *      $client = Application_Model_Client::getInstance();
     *      $client->sendRequest('/session', 'get');
     *      //or
     *      $client->sendRequest('/people', 'get');
     *      //or
     *      $client->sendRequest('/groups', 'get');
     *      //etc
     * </code>
     *
     * Note the leading forward slash. This parameter must start with a slash.
     *
     * @param string $method The request method. Supported methods are:
     *      - get
     *      - post
     *      - put
     *      - delete
     *
     * @param array|string|null $data  The data to send along with the request.
     * If provided, this should be either an array of key/value pairs, for example:
     *
     * <code>
     *      $client = Application_Model_Client::getInstance();
     *      $client->sendRequest('/session', 'post', array(
     *          'app_name'    => 'test',
     *          'app_password => 'test'
     *      ));
     * </code>
     *
     * Or a properly encoded string, according to the expectations of the ASI
     * platform, for example:
     *
     * <code>
     *      $client = Application_Model_Client::getInstance();
     *      $client->sendRequest('/session', 'post', 'session[app_name]=test&session[app_password]=test');
     * </code>
     *
     * Note that for GET requests data MUST be given as a query string in the URL
     * and NOT using this parameter. For example:
     *
     * <code>
     *      $client = Application_Model_Client::getInstance();
     *      $client->sendRequest('/search?search=test', 'get');
     * </code>
     *
     * As such, for GET (and DELETE) requests this parameter is automatically unset
     * before sending the request.
     *
     * @param string $encodingBase The base to use to encode data. This will be
     * necessary if:
     *
     *      1. You are passing the third parameter $data as an array, and
     *      2. The base to use to encode your data is not equivalent to the first
     *         parameter $requestUri.
     *
     * For example, my request URI is '/people', but to create a new user the data
     * should be encoded like so: 'person[username]=username&person[password]=password'
     * and not 'people[username]=username&people[password]=password'.
     *
     * This is how you would handle such a case:
     *
     * <code>
     *      $client = Application_Model_Client::getInstance();
     *      $client->sendRequest('/people', 'post', array(
     *          'username' => 'username',
     *          'password' => 'password'
     *      ), 'person');
     * </code>
     *
     * Note that unlike the request URI, the encoding base does NOT have a leading
     * forward slash.
     *
     * @return Application_Model_Client
     *
     * @throws Application_Model_Client_Exception If:
     *      - the request URI does not have a leading slash
     *      - a method other than the supported ones is provided
     *      - the request fails.
     */
    public function sendRequest($requestUri, $method, $data = null, $encodingBase =  null) {
        //prepare the request
        //check for a valid request URI
        $requestUri = (string) $requestUri;
        if (substr($requestUri, 0, 1) !== '/') {
            throw new Application_Model_Client_Exception("Invalid request URI '{$requestUri}' provided", 0);
        }
        //prepare method
        $method = strtolower((string) $method);
        
        //prepare data, unset it if request is GET or DELETE
        $data = $method === 'get' || $method === 'delete' ? null : $data;

        //prepare client object
        $client = $this->getClientObject();
        
        //if we have any data to send
        if ($data) {
            //if array, encode it first
            if (is_array($data)) {
                //if we have been provided with an encoding base use it, else use the request URI
                $base = $encodingBase && is_string($encodingBase) ? $encodingBase : substr($requestUri, 1);
                
                //reset any previous parameters and the 'Content-type' and 'Content-length' headers
                $client->resetParameters();
                
                //encode data
                $data = $this->encodeData($data, $base);
                
                //if it's a PUT, spoof the platform using this parameter
                $data .= $method === 'put' ? '&_method=PUT' : '';
            }
            //set request data
            $client->setRawData((string) $data);
        }

        //set the URI to usee for the request
        $client->setUri($this->getBaseUri() . $requestUri);

        //send appropriate request
        switch ($method) {
            case 'get':
                try {
                    $this->response = $client->request(Zend_Http_Client::GET);
                } catch (Zend_Http_Client_Exception $e) {
                    throw new Application_Model_Client_Exception('Could not send the request', 0, $e);
                }
                break;

            case 'post':
                try {
                    $this->response = $client->request(Zend_Http_Client::POST);
                } catch (Zend_Http_Client_Exception $e) {
                    throw new Application_Model_Client_Exception('Could not send the request', 0, $e);
                }
                break;

            case 'put':
                try {
                    $this->response = $client->request(Zend_Http_Client::POST);
                } catch (Zend_Http_Client_Exception $e) {
                    throw new Application_Model_Client_Exception('Could not send the request', 0, $e);
                }
                break;

            case 'delete':
                try {
                    $this->response = $client->request(Zend_Http_Client::DELETE);
                } catch (Zend_Http_Client_Exception $e) {
                    throw new Application_Model_Client_Exception('Could not send the request', 0, $e);
                }
                break;

            default:
                throw new Application_Model_Client_Exception("Unsupported method specified: {$method}");
                break;

        }
        return $this;
    }

    /**
     * Encodes an array of data to an ASI style string. Note that the data is
     * URL-encoded.
     *
     * @param array $data The data to encode, may contain arrays within it i.e. array of arrays.
     *
     * @param string $base The base to use in encoding data. For example if your data
     * should be 'person[name][given_name]=Joel', then the encoding base is 'person'.
     *
     * @return string
     */
    public function encodeData(array $data, $base) {
        $str  = '';
        $base = (string) $base;
        foreach ($data as $key => $value) {
            if(is_array($value)) {
                $str  .= $this->encodeData($value, "{$base}[{$key}]");
            } else {
                $value = urlencode($value);
                $str  .= "{$base}[{$key}]={$value}&";
            }
        }
        //why is this?? why not just <code>return substr($str, 0, -1);</code>
        return substr($str, -1) === '&' ? substr($str, 0, -1) : $str;
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