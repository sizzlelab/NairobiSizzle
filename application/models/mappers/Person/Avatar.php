<?php
/**
 * Handles requests to /people/<user id>/@avatar module of the ASI platform.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_Person_Avatar extends Application_Model_Mapper_Person_Abstract {
    /**
     * Class constructor.
     * 
     * @param Application_Model_File $file The file object to work with.
     */
    public function  __construct(Application_Model_File $file = null) {
        if ($file instanceof Application_Model_File) {
            $this->setFile($file);
        }
    }

    /**
     * Fetches a person's avatar from ASI. Sends a GET request to /people/<user id>/@avatar.
     *
     * @param string $id The person's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getId()} to get the person ID.
     *
     * @return Application_Model_File With the {@link Application_Model_File::fileData} set.
     *
     * @todo Could we get the avatar's mime type from ASI too?
     *
     * @throws Application_Model_Mapper_Person_Avatar_Exception If:
     *      - ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person} object)
     *      - The request was not successful.
     */
    public function fetch($id = null) {
        $id = $id ? $id : $this->getPerson()->getId();
        if ($id) {
            $client  = $this->getClient();
            if ($client->sendRequest("/people/{$id}/@avatar", 'get')->isSuccessful()) {
                //getting response body 'image' is a hack to avoid the client trying to decode response data
                return $this->getFile()->setFileData($client->getResponseBody('image'));
            } else {
                throw new Application_Model_Mapper_Person_Avatar_Exception('Error fetching avatar: ' . $client->getResponseMessage(), $client->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Person_Avatar_Exception('Person ID must be set');
        }
    }

    /**
     * @throws Application_Model_Mapper_Person_Avatar_Exception Unsupported method.
     */
    public function create() {
        throw new Application_Model_Mapper_Person_Avatar_Exception('Unsupported method Application_Model_Person_Avatar_Mapper::create');
    }

    /**
     * Updates a person's avatar. Sends a POST request to /people/<user id>/@avatar.
     * Please refer to the ASI documentation on why a POST and not a PUT.
     *
     * @param string $filename The filename (full path) of the file on disk. If
     * not provided, this method will call {@link Application_Model_Mapper_Person_Abstract::getFile()}
     * and then {@link Application_Model_File::getFilename()} to get the filename.
     * 
     * @param string $id The person's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getId()} to get the person's ID.
     *
     * @return Application_Model_File
     *
     * @throws Application_Model_Mapper_Person_Avatar_Exception If:
     *      - ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person} object).
     *      - Filename has not been provided (and could not be obtained from a
     *          {@link Application_Model_File} object).
     *      - The request was not successful. In this case if any error messages
     *          were returned by ASI, they will be available using
     *          {@link Application_Model_Mapper_Abstract::getErrors()}.
     */
    public function update($filename = null, $id = null) {
        $id = $id ? (string) $id : $this->getPerson()->getId();
        if ($id) {
            $filename = $filename ? $filename : $this->getFile()->getFileName();
            if ($filename) {
            $client = $this->getClient();
            $client->getClientObject()->setFileUpload($filename, 'file');
            if ($client->sendRequest("/people/{$id}/@avatar", 'post')->isSuccessful()) {
                return true;
            } else {
                $response = $client->getResponseBody();
                if (isset($response['messages'])) {
                    $this->setErrors($response['messages']);
                }
                throw new Application_Model_Mapper_Person_Avatar_Exception('Error updating avatar: ' . $client->getResponseMessage(), $client->getResponseCode());
            }
            } else {
                throw new Application_Model_Mapper_Person_Avatar_Exception('A filename has not been passed');
            }
        } else {
            throw new Application_Model_Mapper_Person_Avatar_Exception('Person ID must be set');
        }
    }

    /**
     * Deletes a person's avatar. Sends a DELETE request to /people/<user id>/@avatar.
     * From here on {@link Application_Model_Mapper_Person_Avatar::fetch()} will
     * fetch the default avatar.
     *
     * @param string $id The person's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getId()} to get the person's ID.
     *
     * @return true If the avatar was deleted successfully.
     *
     * @throws Application_Model_Mapper_Person_Avatar_Exception If:
     *      - ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person} object).
     *      - The request was not successful.
     */
    public function delete($id = null) {
        $id = $id ? (string) $id : $this->getPerson()->getId();
        if ($id) {
            $client = $this->getClient();
            if ($client->sendRequest("/people/{$id}/@avatar", 'delete')->isSuccessful()) {
                return true;
            } else {
                throw new Application_Model_Mapper_Person_Avatar_Exception('Error deleting avatar: ' . $client->getResponseMessage(), $client->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Person_Avatar_Exception('Person ID must be set');
        }
    }
}