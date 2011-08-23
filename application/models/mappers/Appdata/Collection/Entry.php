<?php

/**
 * Provides methods for managing entries in a collection.
 * @author Simon Ndunda
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 *
 */
class Application_Model_Mapper_Appdata_Collection_Entry {

    /**
     * adds an item to a collection
     * @param $app_id
     * @param $collection_id
     * @param array $data
     * @return Application_Model_Appdata_Collection_Entry
     */
    public function create($app_id, $collection_id, array $data) {
        $client = Application_Model_Client::getInstance();
        $data = $client->encodeData($data, "item");
        //echo $data;
        $response = $client->sendRequest("/appdata/{$app_id}/@collections/{$collection_id}", "post", $data);
        if ($response->isSuccessful()) {
            $result = $response->getResponseBody();
            if (is_array($result)) {
                $newItem = new Application_Model_Appdata_Collection_Entry($result['entry']);
                return $newItem;
            } else {
                throw new Application_Model_Mapper_Appdata_Collection_Exception("Error adding item: " . $response->getResponseCode(), $response->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Appdata_Collection_Exception("Error adding item: " . $response->getResponseCode(), $response->getResponseCode());
        }
    }

    /**
     * deletes an item from a collection
     * @param  $app_id
     * @param  $collection_id
     * @param  $item_id
     */

    public function delete($app_id, $collection_id, $item_id) {
        $client = Application_Model_Client::getInstance();
        $response = $client->sendRequest("/appdata/{$app_id}/@collections/{$collection_id}/@items/{$item_id}", "delete");
        if ($response->isSuccessful()) {
            echo "deleted item id {$item_id}";
        } else {
            echo "faild code " . $response->getResponseCode() . " " . $response->getResponseMessage();
        }
    }

}

?>
