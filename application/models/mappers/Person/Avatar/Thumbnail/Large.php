<?php
/**
 * Handles requests to /people/<user id>/@avatar/large_thumbnail module of the
 * ASI platform.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_Person_Avatar_Thumbnail_Large extends Application_Model_Mapper_Person_Abstract {
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
     * Fetches a person's large avatar thumbnail from ASI. Sends a GET request to
     * /people/<user id>/@avatar/large_thumbnail.
     *
     * @param string $id The person's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getId()} to get the person's ID.
     *
     * @return Application_Model_File With the {@link Application_Model_File::fileData} set.
     *
     * @todo Could we get the thumbnail's mime type from ASI too?
     *
     * @throws Application_Model_Mapper_Person_Avatar_Thumbnail_Large_Exception If:
     *      - ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person} object).
     *      - The request was not successful.
     */
    public function fetch($id = null) {
        $id = $id ? (string) $id : $this->getPerson()->getId();
        if ($id) {
            $client  = $this->getClient();
            if ($client->sendRequest("/people/{$id}/@avatar/large_thumbnail", 'get')->isSuccessful()) {
                //getting response body image is a hack to avoid the client trying to decode the image to array
                return $this->getFile()->setFileData($client->getResponseBody('image'));
            } else {
                throw new Application_Model_Mapper_Person_Avatar_Thumbnail_Large_Exception('Error fetching large avatar thumbnail: ' . $client->getResponseMessage(), $client->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Person_Avatar_Thumbnail_Large_Exception('Person ID must be set');
        }
    }

    /**
     * @throws Application_Model_Mapper_Person_Avatar_Thumbnail_Large_Exception Unsupported method.
     */
    public function create() {
        throw new Application_Model_Mapper_Person_Avatar_Thumbnail_Large_Exception('Unsupported method Application_Model_Mapper_Person_Avatar_Thumbnail_Large::create');
    }

    /**
     * @throws Application_Model_Mapper_Person_Avatar_Thumbnail_Large_Exception Unsupported method.
     */
    public function update() {
        throw new Application_Model_Mapper_Person_Avatar_Thumbnail_Large_Exception('Unsupported method Application_Model_Mapper_Person_Avatar_Thumbnail_Large::update');
    }

    /**
     * @throws Application_Model_Mapper_Person_Avatar_Thumbnail_Large_Exception Unsupported method.
     */
    public function delete() {
        throw new Application_Model_Mapper_Person_Avatar_Thumbnail_Large_Exception('Unsupported method Application_Model_Mapper_Person_Avatar_Thumbnail_Large::delete');
    }
}