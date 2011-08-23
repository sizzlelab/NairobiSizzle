<?php

/**
 * Provides a method for managing subscriptions to channels.
 * @author Simon Ndunda
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_Channel_Subscription extends Application_Model_Mapper_Search_Abstract {

    /**
     * @throws Application_Model_Mapper_Channel_Subscription_Exception Unsupported method.
     */
    public function fetch() {
        throw new Application_Model_Mapper_Channel_Subscription_Exception("Unsupported method");
    }

    /**
     * adds a new subscription to a channel, the current user is by default added unless a group_id or person_id is passed
     * @param $channel_id
     * @param $group_id if adding a group, options
     * @param $person_id if adding a person to a private channel, otherwise no needed
     */
    public function create($channel_id = null, $group_id=null, $person_id=null) {
        $client = Application_Model_Client::getInstance();
        $data = '';
        if (isset($group_id)) {
            $data.="group_id=" . $group_id . "&";
        }
        if (isset($person_id)) {
            $data.="person_id=" . $person_id . "&";
        }
        $response = $client->sendRequest("/channels/{$channel_id}/@subscriptions", "post", $data);
        if ($response->isSuccessful()) {
            return true;
        } else {
            throw new Application_Model_Mapper_Channel_Subscription_Exception("Failed to create subscription: " . $response->getResponseMessage(), $response->getResponseCode());
        }
    }

    /**
     *  updates the subscription details
     * @param array $data
     * @param $id
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
                throw new Application_Model_Mapper_Channel_Subscription_Exception("Failed to update subscription: " . $response->getResponseMessage(), $response->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Channel_Subscription_Exception("Failed to update subscription: " . $response->getResponseMessage(), $response->getResponseCode());
        }
    }

    /**
     * removes subscription from a particular channel, the current user is by default removed unless a group_id or person_id is passed
     * @param $channel_id
     * @param  $group_id
     * @param  $person_id
     */
    public function delete($channel_id = null, $group_id=null, $person_id=null) {
        $client = Application_Model_Client::getInstance();
        $data = '';
        if (isset($group_id)) {
            $data.="group_id=" . $group_id . "&";
        }
        if (isset($person_id)) {
            $data.="person_id=" . $person_id . "&";
        }
        $response = $client->sendRequest("/channels/{$channel_id}/@subscriptions", "delete", $data);
        if ($response->isSuccessful()) {
            return true;
        } else {
            throw new Application_Model_Mapper_Channel_Subscription_Exception("Failed to delete subscription: " . $response->getResponseMessage(), $response->getResponseCode());
        }
    }

    /**
     * fetches all the subscribers of a particular channel
     * @param  $channel_id
     * @return Application_Model_Channel_Subscription
     */
    public function fetchAll($channel_id) {
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
        $response = $client->sendRequest("/channels/{$channel_id}/@subscriptions?{$data}", "get");
        if ($response->isSuccessful()) {
            $result = $response->getResponseBody();
            if (is_array($result)) {
                if (isset($result['pagination'])) {
                    $this->setPagination(new Application_Model_Pagination($result['pagination']));
                }
                $subscription = new Application_Model_Channel_Subscription($result['entry']);
                return $subscription;
            } else {
                throw new Application_Model_Mapper_Channel_Subscription_Exception("Failed to delete subscription: " . $response->getResponseMessage(), $response->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Channel_Subscription_Exception("Failed to delete subscription: " . $response->getResponseMessage(), $response->getResponseCode());
        }
    }

}