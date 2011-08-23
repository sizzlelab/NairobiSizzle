<?php

/**
 * Provides methods for managing channels for a particular app
 * @author Simon Ndunda
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 *
 */
class Application_Model_Mapper_Channel extends Application_Model_Mapper_Search_Abstract {

    /**
     * creates a new channel
     * @param array $data
     * @return Application_Model_Channel
     */
    public function create(array $data = null) {
        $client = Application_Model_Client::getInstance();
        $data = $client->encodeData($data, "channel");
        //echo $data;
        $response = $client->sendRequest("/channels", "post", $data);
        if ($response->isSuccessful()) {
            $result = $response->getResponseBody();
            if (is_array($result)) {
                $newChannel = new Application_Model_Channel($result['entry']);
                return $newChannel;
            } else {
                throw new Application_Model_Mapper_Channel_Exception("Error creating channel: " . $response->getResponseMessage(), $response->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Channel_Exception("Error creating channel: " . $response->getResponseMessage(), $response->getResponseCode());
        }
    }

    /**
     * updates the details of a channel
     * @param array $data
     * @param  $id
     * @return Application_Model_Channel
     */
    public function update(array $data = null, $id = null) {
        $client = Application_Model_Client::getInstance();
        $data = $client->encodeData($data, "channel");
        //echo $data;
        $response = $client->sendRequest("/channels/{$id}", "put", $data);
        if ($response->isSuccessful()) {
            $result = $response->getResponseBody();
            if (is_array($result)) {
                $updatedChannel = new Application_Model_Channel($result['entry']);
                return $updatedChannel;
            } else {
                throw new Application_Model_Mapper_Channel_Exception("Error updating channel: " . $response->getResponseMessage(), $response->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Channel_Exception("Error updating channel: " . $response->getResponseMessage(), $response->getResponseCode());
        }
    }

    /**
     * deletes a channel
     * @param $id
     */
    public function delete($id = null) {
        $client = Application_Model_Client::getInstance();
        $response = $client->sendRequest("/channels/{$id}", "delete");
        if ($response->isSuccessful()) {
            //     echo "deleted channel id {$id}";
            return true;
        } else {
            throw new Application_Model_Mapper_Channel_Exception("Error deleting channel: " . $response->getResponseMessage(), $response->getResponseCode());
        }
    }

    /**
     * fetches the details of a particular channel
     * @param $id
     * @return Application_Model_Channel
     */
    public function fetch($id = null) {
        $client = Application_Model_Client::getInstance();
        $response = $client->sendRequest("/channels/{$id}", "get");
        if ($response->isSuccessful()) {
            $result = $response->getResponseBody();
            if (is_array($result)) {
                $channel = new Application_Model_Channel($result['entry']);
                return $channel;
            } else {
                throw new Application_Model_Mapper_Channel_Exception("Error fetching channel: " . $response->getResponseMessage(), $response->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Channel_Exception("Error fetching channel: " . $response->getResponseMessage(), $response->getResponseCode());
        }
    }

    /**
     * fetches all the channels belonging to the current application
     * @param  $include_private
     * @param  $type_filter
     * @return Application_Model_Channel
     */
    public function fetchAll($include_private=null, $type_filter=null) {
        $search = $this->getSearchTerm();
        $page = $this->getPage();
        $per_page = $this->getPerPage();
        $sort_order = $this->getSortOrder();
        $client = Application_Model_Client::getInstance();
        $data = '';
        if ($page) {
            $data.="page=" . $page . "&";
        }
        if ($per_page) {
            $data.="per_page=" . $per_page . "&";
        }
        if ($search) {
            $data.="search=" . $search . "&";
        }
        if ($sort_order) {
            $data.="sort_order=" . $sort_order . "&";
        }
        if ($include_private) {
            $data.="include_private=" . $include_private . "&";
        }
        if ($type_filter) {
            $data.="type_filter=" . $type_filter . "&";
        }
        $response = $client->sendRequest("/channels?{$data}", "get");
        if ($response->isSuccessful()) {
            $result = $response->getResponseBody();
            if (is_array($result)) {
                if (isset($result['pagination'])) {
                    $this->setPagination(new Application_Model_Pagination($result['pagination']));
                }
                $allChannels = array();
                $count = 0;
                foreach ($result['entry'] as $channelData) {
                    $allChannels[$count] = new Application_Model_Channel($channelData);
                    $count++;
                }
                return $allChannels;
            } else {
                throw new Application_Model_Mapper_Channel_Exception("Error fetching channels: " . $response->getResponseMessage(), $response->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Channel_Exception("Error fetching channels: " . $response->getResponseMessage(), $response->getResponseCode());
        }
    }

}