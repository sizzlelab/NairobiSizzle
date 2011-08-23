<?php

/**
 * Provides a method deleting an item without using a collection id.
 * @author Simon Ndunda
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_Appdata_CollectionItem {

    /**
     * deletes an  item
     * @param $app_id
     * @param $item_id
     */
    public function delete($app_id, $item_id) {
        $client = Application_Model_Client::getInstance();
        $response = $client->sendRequest("/appdata/{$app_id}/@collection_items/{$item_id}", "delete");
        if ($response->isSuccessful()) {
            //   echo "deleted collection item id {$item_id}";
        } else {
            throw new Application_Model_Mapper_Appdata_CollectionItem_Exception("Failed to delete item: " . $response->getResponseMessage(), $response->getResponseCode());
        }
    }

}

?>
