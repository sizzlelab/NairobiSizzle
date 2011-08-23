<?php
class Sizzle_Client_Asi extends Sizzle_Client_Abstract {
    /**
     * The default base URI for all requests to the ASI platform.
     *
     * This can be changed using {@link Sizzle_Client_Asi::setBaseUri()}.
     *
     * @var string
     */
    protected $baseUri = 'http://127.0.0.1:3000';

    /**
     * Encodes an array of data to an ASI platform style string.
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
                $str .= $this->encodeData($value, "{$base}[{$key}]");
            } else {
                $str .= "{$base}[{$key}]={$value}&";
            }
        }
        //why is this?? why not just <code>return substr($str, 0, -1);</code>
        return substr($str, -1) === '&' ? substr($str, 0, -1) : $str;
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
}
