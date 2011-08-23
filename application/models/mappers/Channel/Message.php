<?php

/**
 * Provides methods for working with messages in a channel.
 * @author Simon Ndunda
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_Channel_Message extends Application_Model_Mapper_Search_Abstract {

    /**
     * adds a new message to a channel
     * @param  $channel_id
     * @param array $data
     * @return Application_Model_Channel_Message
     */
    public function create($channel_id = null, array $data = null) {
        $client = Application_Model_Client::getInstance();
        $data = $client->encodeData($data, "message");
        //echo $data;
        $response = $client->sendRequest("/channels/{$channel_id}/@messages", "post", $data);
        if ($response->isSuccessful()) {
            $result = $response->getResponseBody();
            if (is_array($result)) {
                $newMessage = new Application_Model_Channel_Message($result['entry']);
                return $newMessage;
            } else {
                throw new Application_Model_Mapper_Channel_Message_Exception("Failed to create message: " . $response->getResponseMessage(), $response->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Channel_Message_Exception("Failed to create message: " . $response->getResponseMessage(), $response->getResponseCode());
        }
    }

    /**
     * updates a message
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
                throw new Application_Model_Mapper_Channel_Message_Exception("Failed to update message: " . $response->getResponseMessage(), $response->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Channel_Message_Exception("Failed to update message: " . $response->getResponseMessage(), $response->getResponseCode());
        }
    }

    /**
     * Deletes a message from a channel
     * @param $channel_id the id of the channel
     * @param $id the id of the message to delete
     */
    public function delete($channel_id = null, $id = null) {
        $client = Application_Model_Client::getInstance();
        $response = $client->sendRequest("/channels/{$channel_id}/@messages/{$id}", "delete");
        if ($response->isSuccessful()) {
            return true;
        } else {
            throw new Application_Model_Mapper_Channel_Message_Exception("Failed to delete message: " . $response->getResponseMessage(), $response->getResponseCode());
        }
    }

    /**
     * Fetches the details of a particular message
     * @param $channel_id the id of the channel
     * @param $id the id of the message to fetch
     * @return Application_Model_Channel_Message
     */
    public function fetch($channel_id = null, $id = null) {
        $client = Application_Model_Client::getInstance();
        $response = $client->sendRequest("/channels/{$channel_id}/@messages/{$id}", "get");
        if ($response->isSuccessful()) {
            $result = $response->getResponseBody();
            if (is_array($result)) {
                $message = new Application_Model_Channel_Message($result['entry']);
                return $message;
            } else {
                throw new Application_Model_Mapper_Channel_Message_Exception("Failed to fetch message: " . $response->getResponseMessage(), $response->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Channel_Message_Exception("Failed to fetch message: " . $response->getResponseMessage(), $response->getResponseCode());
        }
    }

    /**
     * This method fetches all messages belonging to a particular channel
     *
     * @param $channel_id the id of the channel
     * @param $exclude_replies a string that specifies if replies to messages should be included as messages, the default value is true.
     * @return Application_Model_Channel_Message
     *
     */
    public function fetchAll($channel_id, $exclude_replies="true") {
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
        if ($exclude_replies) {
            $data.="exclude_replies=" . $exclude_replies . "&";
        }
        if ($search) {
            $data.="search=" . $search . "&";
        }
        if ($sort_order) {
            $data.="sort_order=" . $sort_order . "&";
        }
        $response = $client->sendRequest("/channels/{$channel_id}/@messages?{$data}", "get");
        if ($response->isSuccessful()) {
            $result = $response->getResponseBody();
            if (is_array($result)) {
                if (isset($result['pagination'])) {
                    $this->setPagination(new Application_Model_Pagination($result['pagination']));
                }
                $allMessages = array();
                $count = 0;
                foreach ($result['entry'] as $messageData) {
                    $allMessages[$count] = new Application_Model_Channel_Message($messageData);
                    $count++;
                }
                return $allMessages;
            } else {
                throw new Application_Model_Mapper_Channel_Message_Exception("Failed to fetch messages: " . $response->getResponseMessage(), $response->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Channel_Message_Exception("Failed to fetch messages: " . $response->getResponseMessage(), $response->getResponseCode());
        }
    }

}