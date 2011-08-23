<?php
/**
 * Handles requests to /people/recover_password module of the ASI platform.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_Person_PasswordRecovery extends Application_Model_Mapper_Person_Abstract {
    /**
     * @throws Application_Model_Mapper_Person_PasswordRecovery_Exception
     * Unsupported method.
     */
    public function fetch() {
        throw new Application_Model_Person_PasswordRecovery_Mapper_Exception('Unsupported method Application_Model_PasswordRecovery_Mapper::create');
    }

    /**
     * Sends a password recovery email to a person. Sends a POST request to
     * /people/password_recovery.
     * 
     * @param string $email Email to send password recovery email to. If not
     * provided, this method will call {@link Application_Model_Mapper_Person_Abstract::getPerson()}
     * and then {@link Application_Model_Person::getEmail()} to get the email to
     * use.
     * 
     * @return true If request is successful.
     *
     * @throws Application_Model_Mapper_Person_PasswordRecovery_Exception If:
     *      - Email was not provided (and could not be obtained from
     *          a {@link Application_Model_Person} object).
     *      - The request was not successful. In this case if any error messages
     *          were returned by ASI, they will be available using
     *          {@link Application_Model_Mapper_Abstract::getErrors()}.
     */
    public function create($email = null) {
        $email = $email ? (string) $email : $this->getPerson()->getEmail();
        if ($email) {
            $client = $this->getClient();
            if ($client->sendRequest('/people/recover_password', 'post', "email={$email}")->isSuccessful()) {
                return true;
            } else {
                $response = $client->getResponseBody();
                if (isset($response['messages'])) {
                    $this->setErrors($response['messages']);
                }
                throw new Application_Model_Mapper_Person_PasswordRecovery_Exception('Could not send recovery email: ' . $client->getResponseMessage(), $client->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Person_PasswordRecovery_Exception('Password recovery email must be set');
        }
    }

    /**
     * @throws Application_Model_Mapper_Person_PasswordRecovery_Exception
     * Unsupported method.
     */
    public function update() {
        throw new Application_Model_Mapper_Person_PasswordRecovery_Exception('Unsupported method Application_Model_Mapper_PasswordRecovery::update()');
    }

    /**
     * @throws Application_Model_Mapper_Person_PasswordRecovery_Exception
     * Unsupported method.
     */
    public function delete() {
        throw new Application_Model_Mapper_Person_PasswordRecovery_Exception('Unsupported method Application_Model_Mapper_PasswordRecovery::delete()');
    }
}