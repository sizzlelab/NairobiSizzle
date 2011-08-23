<?php

/**
 * Provides methods for managing self appdata.
 * @author Simon Ndunda
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_Appdata_Self {

    /**
     * updates self data
     * @param $app_id
     * @param $user_id
     * @param array $data
     * @return Application_Model_Appdata_Self
     */
    public function update($app_id, $user_id, array $data) {
        $client = Application_Model_Client::getInstance();
        $data = $client->encodeData($data, "data");
        echo $data . "<br/>";
        $response = $client->sendRequest("/appdata/{$user_id}/@self/{$app_id}", "put", $data);
        if ($response->isSuccessful()) {
            $result = $response->getResponseBody();
            if (is_array($result)) {
                $data = new Application_Model_Appdata_Self($result['entry']);
                return $data;
            } else {
                throw new Application_Model_Mapper_Appdata_Self_Exception("Failed to update self appadata: " . $response->getResponseMessage(), $response->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Appdata_Self_Exception("Failed to update self appadata: " . $response->getResponseMessage(), $response->getResponseCode());
        }
    }

    /**
     * fetch all self data for a particular user
     * @param  $app_id
     * @param  $user_id
     * @return array
     */
    public function fetchAll($app_id, $user_id) {
        $client = Application_Model_Client::getInstance();
        $response = $client->sendRequest("/appdata/{$user_id}/@self/{$app_id}", "get");
        if ($response->isSuccessful()) {
            $result = $response->getResponseBody();
            if (is_array($result)) {
                // var_dump($result['entry']);
                /* $alldata = array();
                  $count = 0;
                  foreach ($result['entry'] as $keyData) {
                  $alldata = new Application_Model_Appdata_Self($keyData);
                  $count++;
                  }
                  return $alldata;
                 * 
                 */
                return $result['entry'];
            } else {
                throw new Application_Model_Mapper_Appdata_Self_Exception("Failed to fetch self appdata: " . $response->getResponseMessage(), $response->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Appdata_Self_Exception("Failed to fetch self appdata: " . $response->getResponseMessage(), $response->getResponseCode());
        }
    }

}

?>
