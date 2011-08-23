<?php
/**
 * Handles requests to the /people/availability module of the ASI platform.
 * 
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_Person_Availability extends Application_Model_Mapper_Person_Abstract {
    /**
     * Send the request to check availability of username and/or email. Sends a
     * GET request to /people/availability.
     *
     * @todo Contrary to all other requests, this requests returns data like this
     * $data['entry'][0]['username']=available instead of the expected
     * $data['entry']['username']=available.
     * 
     * @param string $username Username to check for availability. If not provided,
     * this method will call {@link Application_Model_Mapper_Person_Abstract::getPerson()}
     * and then {@link Application_Model_Person::getUsername()} to get the username
     * to check for availability. In order to avoid this i.e. to not check for username
     * availability at all, provide 'null' for this parameter.
     * 
     * @param string $email Email to check for availability. If not provided, this
     * method will call {@link Application_Model_Mapper_Person_Abstract::getPerson()}
     * and then {@link Application_Model_Person::getEmail()} to get the email to check
     * for availability. In order to avoid this i.e. to not check for email
     * availability at all, provide 'null' for this parameter.
     *
     * @return Application_Model_Person_Availability With the
     * {@link Application_Model_Person_Availability::username} and
     * {@link Application_Model_Person_Availability::email} set.
     *
     * @throws Application_Model_Mapper_Person_Availability_Exception If:
     *      - No username or email was provided (and could not be obtained from
     *          a {@link Application_Model_Person} object).
     *      - The request was not successful. 
     *      - The request was successful but for some unknown reason data was not received.
     */
    public function fetch($username = null, $email = null) {
        $person      = $this->getPerson();
        $username    = !is_null($username) ? (string) $username : $person->getUsername();
        $email       = !is_null($email) ? (string) $email : $person->getEmail();
        $queryString = '';
        if ($username) {
            $queryString = $queryString ? $queryString . "&username={$username}" : "?username={$username}";
        }
        if ($email) {
            $queryString = $queryString ? $queryString . "&email={$email}" : "?email={$email}";
        }
        if ($queryString) {
            $client = $this->getClient();
            if ($client->sendRequest("/people/availability{$queryString}", 'get')->isSuccessful()) {
                $response = $client->getResponseBody();
                if (isset($response['entry'][0])) {
                    return new Application_Model_Person_Availability($response['entry'][0]);
                } else {
                    throw new Application_Model_Mapper_Person_Availability_Exception('Unexpected error: data not received');
                }
            } else {
                throw new Application_Model_Mapper_Person_Availability_Exception('Could not check availability: ' . $client->getResponseMessage(), $client->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Person_Availability_Exception('Username and/or email to check for availability must be set');
        }
    }

    /**
     * @throws Application_Model_Mapper_Person_Availability_Exception Unsupported method.
     */
    public function create() {
        throw new Application_Model_Mapper_Person_Availability_Exception('Unsupported method Application_Model_Mapper_Person_Availability::create');
    }

    /**
     * @throws Application_Model_Mapper_Person_Availability_Exception Unsupported method.
     */
    public function update() {
        throw new Application_Model_Mapper_Person_Availability_Exception('Unsupported method Application_Model_Mapper_Person_Availability::update');
    }

    /**
     * @throws Application_Model_Mapper_Person_Availability_Exception Unsupported method.
     */
    public function delete() {
        throw new Application_Model_Mapper_Person_Availability_Exception('Unsupported method Application_Model_Mapper_Person_Availability::delete');
    }
}