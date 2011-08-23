<?php

/**
 * Provides a method for fetching and sorting replies to a particular message.
 * @author Simon Ndunda
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_Channel_Message_Replies extends Application_Model_Mapper_Search_Abstract {

    /**
     * @throws Application_Model_Mapper_Channel_Message_Replies_Exception Unsupported method.
     */
    public function create() {
        throw new Application_Model_Mapper_Channel_Message_Replies_Exception("Unsupported method");
    }

    /**
     * @throws Application_Model_Mapper_Channel_Message_Replies_Exception Unsupported method.
     */
    public function update() {
        throw new Application_Model_Mapper_Channel_Message_Replies_Exception("Unsupported method");
    }

    /**
     * @throws Application_Model_Mapper_Channel_Message_Replies_Exception Unsupported method.
     */
    public function delete() {
        throw new Application_Model_Mapper_Channel_Message_Replies_Exception("Unsupported method");
    }

    /**
     * @throws Application_Model_Mapper_Channel_Message_Replies_Exception Unsupported method.
     */
    public function fetch() {
        throw new Application_Model_Mapper_Channel_Message_Replies_Exception("Unsupported method");
    }

    /**
     *
     * @param  $channel_id the id of the channel the message belongs to
     * @param $message_id the message id to fecth replies for
     * @return Application_Model_Channel_Message
     */
    public function fetchAll($channel_id, $message_id) {

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
        if ($sort_order) {
            $data.="sort_order=" . $sort_order . "&";
        }
        $response = $client->sendRequest("/channels/{$channel_id}/@messages/{$message_id}/@replies?{$data}", "get");
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
                throw new Application_Model_Mapper_Channel_Message_Replies_Exception("Failed to fetch replies: " . $response->getResponseMessage(), $response->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Channel_Message_Replies_Exception("Failed to fetch replies: " . $response->getResponseMessage(), $response->getResponseCode());
        }
    }

}