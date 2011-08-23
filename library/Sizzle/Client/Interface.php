<?php
/**
 * Interface description for Sizzle_Client classes.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
interface Sizzle_Client_Interface {
    /**
     * Set the base URI for all requests made by a client class.
     * 
     * @param string $baseUri
     */
    public function setBaseUri($baseUri);

    /**
     * Get the base URI for all requests made by a client class.
     */
    public function getBaseUri();
    
    /**
     * Send a request to a RESTful platform.
     *
     * @param string $requestUri The request URI that will be appended to the
     * base URI to make a request.
     * 
     * @param string $method The HTTP request method.
     *
     * @param string|array $data The data to send along with the reqeust.
     */
    public function sendRequest($requestUri, $method = 'get', $data = null);
}