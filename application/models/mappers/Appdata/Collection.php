<?php

/**
 * Provides methods for managing collections and their items for a particluar app_id.
 * @author Simon Ndunda
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 * 
 */
class Application_Model_Mapper_Appdata_Collection {

    /**
     *  creates a new collection
     * @param $app_id
     * @param array $data
     * @return Application_Model_Appdata_Collection
     */
    public function create($app_id, array $data) {
        $client = Application_Model_Client::getInstance();
        $data = $client->encodeData($data, "collection");
        //echo $data;
        $response = $client->sendRequest("/appdata/{$app_id}/@collections", "post", $data);
        if ($response->isSuccessful()) {
            $result = $response->getResponseBody();
            if (is_array($result)) {
                $newCollection = new Application_Model_Appdata_Collection($result['entry']);
                return $newCollection;
            } else {
                throw new Application_Model_Mapper_Appdata_Collection_Exception("Error creating collection: " . $response->getResponseCode(), $response->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Appdata_Collection_Exception("Error creating collection: " . $response->getResponseCode(), $response->getResponseCode());
        }
    }

    /**
     * updates the details of a collection
     * @param $app_id
     * @param $collection_id
     * @param array $data
     * @return Application_Model_Appdata_Collection
     */
    public function update($app_id, $collection_id, array $data) {
        $client = Application_Model_Client::getInstance();
        $data = $client->encodeData($data, "collection");
        echo $data . "<br/>";
        $response = $client->sendRequest("/appdata/{$app_id}/@collections/{$collection_id}", "put", $data);
        if ($response->isSuccessful()) {
            $result = $response->getResponseBody();
            if (is_array($result)) {
                $collection = new Application_Model_Appdata_Collection($result['entry']);
                return $collection;
            } else {
                throw new Application_Model_Mapper_Appdata_Collection_Exception("Error updating collection: " . $response->getResponseCode(), $response->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Appdata_Collection_Exception("Error updating collection: " . $response->getResponseCode(), $response->getResponseCode());
        }
    }

    /**
     * deletes a collection
     * @param $app_id
     * @param $collection_id 
     */
    public function delete($app_id, $collection_id) {
        $client = Application_Model_Client::getInstance();
        $response = $client->sendRequest("/appdata/{$app_id}/@collections/{$collection_id}", "delete");
        if ($response->isSuccessful()) {
            //  echo "deleted collection id {$collection_id}";
        } else {
            throw new Application_Model_Mapper_Appdata_Collection_Exception("Error deleting collection: " . $response->getResponseCode(), $response->getResponseCode());
        }
    }

    /**
     * fetches a collection of the given id with and all the items in it.
     * @param $app_id
     * @param $id
     * @param $count
     * @param $startIndex
     * @return Application_Model_Appdata_Collection
     */
    public function fetch($app_id, $id, $count=null, $startIndex=null) {
        $data = "";
        if ($count) {
            $data .="count=$count&";
        }
        if ($startIndex) {
            $data .="$startIndex=$startIndex&";
        }
        //echo "data=$data";
        $client = Application_Model_Client::getInstance();
        $response = $client->sendRequest("/appdata/{$app_id}/@collections/{$id}?{$data}", "get");
        if ($response->isSuccessful()) {
            $result = $response->getResponseBody();
            if (is_array($result)) {
                $collection = new Application_Model_Appdata_Collection($result['entry']);
                return $collection;
            } else {
                throw new Application_Model_Mapper_Appdata_Collection_Exception("Error fetching collection: " . $response->getResponseCode(), $response->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Appdata_Collection_Exception("Error fetching collection: " . $response->getResponseCode(), $response->getResponseCode());
        }
    }

    /**
     * fetches all the collections for a particular application
     * @param $app_id
     * @param $tags
     * @return Application_Model_Appdata_Collection
     */
    public function fetchAll($app_id, $tags=null) {
        $client = Application_Model_Client::getInstance();
        $data = '';
        if ($tags) {
            $data.="tags=" . $tags . "&";
        }
        $response = $client->sendRequest("/appdata/{$app_id}/@collections?{$data}", "get");
        if ($response->isSuccessful()) {
            $result = $response->getResponseBody();
            if (is_array($result)) {
                // var_dump($result['entry']);
                $allCollections = array();
                $count = 0;
                foreach ($result['entry'] as $collectionData) {
                    $allCollections[$count] = new Application_Model_Appdata_Collection($collectionData);
                    $count++;
                }
                return $allCollections;
            } else {
                throw new Application_Model_Mapper_Appdata_Collection_Exception("Error fetching collections: " . $response->getResponseCode(), $response->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Appdata_Collection_Exception("Error fetching collections: " . $response->getResponseCode(), $response->getResponseCode());
        }
    }

}

?>
