<?php
/**
 * Base class containing code core to all mapper classes.
 *
 * This class implements {@link Application_Model_Mapper_Interface}, thus ensuring
 * a code standard that all mapper classes implement the methods declared in
 * the interface.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
abstract class Application_Model_Mapper_Abstract implements Application_Model_Mapper_Interface {
    /**
     * Stores an instance of {@link Application_Model_Client}.
     *
     * @var Application_Model_Client|null
     */
    protected $client     = null;

    /**
     * Stores an instance of {@link Application_Model_Pagination}. This is used
     * where pagination is involved.
     * 
     * @var Application_Model_Pagination|null
     */
    protected $pagination = null;
    
    /**
     * Stores an instance of {@link Application_Model_File}. This is used where
     * file uploads and downloads to and from ASI are involved.
     * 
     * @var Application_Model_File|null
     */
    protected $file = null;

    /**
     * Stores the errors resulting from a failed request. Normally this will be
     * something like this:
     *
     * <code>
     *      $client = $this->getClient();
     *      if ($client->sendRequest('/people', 'post', 'errroneous data')->isSuccessful()) {
     *          $data = $client->->getResponseBody();
     *          if (isset($data['entry'])) {
     *              return new Application_Model_Person($data['entry']);
     *          } else {
     *              throw new Application_Model_Exception("Unexpected error: key 'entry' not set in response data");
     *          }
     *      } else {
     *          $response = $client->getResponseBody();
     *          $this->setErrors($data['meessages']);
     *      }
     * </code>
     * @var array
     */
    protected $errors = array();

    /**
     * Set the client instance for perfoming mapping requests. This will by default
     * get a client instance using {@link Application_Model_Client::getInstance()}.
     *
     * Thus any configuration to the client object for all subsequent requests e.g.
     * setting a {@link Zend_Http_Client_Adapter_Proxy} adapter should be done here.
     *
     * @return Application_Model_Mapper_Abstract
     */
    public function setClient() {
        $client = Application_Model_Client::getInstance();
        /*
        $adapter = new Zend_Http_Client_Adapter_Proxy();
        $adapter->setConfig(array(
                'proxy_user' => 'swa',
                'proxy_pass' => 'swa',
                'proxy_host' => '10.2.21.7',
                'proxy_port' => 80,
                'persistent' => true,
        ));
        $client->setBaseUri('http://41.204.186.41')
                ->getClientObject()
                ->setAdapter($adapter);
         * 
         */
        $this->client = $client;
        return $this;
    }

    /**
     * Get the client instance for performing mapping requests.
     *
     * @see Application_Model_Mapper_Abstract::setClient()
     *
     * @return Application_Model_Client
     */
    public function getClient() {
        if (!$this->client) {
            $this->setClient();
        }
        return $this->client;
    }

    /**
     * Set the errors resulting from a request.
     *
     * @see Application_Model_Mapper_Abstract::errors
     *
     * @return Application_Model_Mapper_Abstract
     */
    public function setErrors(array $errors) {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Get the errors resulting from a request, set using
     * {@link Application_Model_Mapper_Abstract::setErrors()}.
     *
     * @see Application_Model_Mapper_Abstract::setErrors()
     *
     * @return Application_Model_Mapper_Abstract
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Set the pagination object. This is useful when the ASI platform returns any
     * pagination data, for example:
     *
     * <code>
     *      //get client
     *      $client = $this->getClient();
     *      //do a GET
     *      if ($client->sendRequest('/people?page=2&per_page=5', 'get')->isSuccessful()) {
     *          $response = $client->getResponseBody();
     *          if (isset($response['pagination']) {
     *              $this->setPagination(new Application_Model_Pagination($response['pagination']));
     *          }
     *          //continue processing response data
     *      }
     * </code>
     * 
     * @return Application_Model_Mapper_Abstract
     */
    public function setPagination(Application_Model_Pagination $pagination) {
        $this->pagination = $pagination;
        return $this;
    }

    /**
     * Get the pagination object.
     *
     * @see Application_Model_Mapper_Abstract::setPagination()
     *
     * @return Application_Model_Pagination
     */
    public function getPagination() {
        return $this->pagination;
    }

    /**
     * Set the file object. This is useful when you want to do a file upload or
     * download to/from ASI.
     *
     * @param Application_Model_File $file The file object.
     *
     * @return Application_Model_Mapper_Abstract
     */
    public function setFile(Application_Model_File $file) {
        $this->file = $file;
        return $this;
    }

    /**
     * Get the file object.
     *
     * @see Application_Model_Mapper_Abstract::setFile()
     *
     * @return Application_Model_File
     */
    public function getFile() {
        if (!$this->file) {
            $this->setFile(new Application_Model_File());
        }
        return $this->file;
    }
}