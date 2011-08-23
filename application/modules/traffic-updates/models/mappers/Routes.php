<?php
class TrafficUpdates_Model_Mapper_Routes {
    /**
     * Stores an instance of {@link TrafficUpdates_Model_DbTable_Routes} to work with.
     *
     * @var TrafficUpdates_Model_DbTable_Routes
     */
    protected $dbTable = null;

    /**
     * Stores an instance of {@link Application_Model_Mapper_Channel} to work with.
     *
     * @var Application_Model_Mapper_Channel
     */
    protected $channelMapper = null;

    /**
     * Stores an instance of {@link Application_Model_Mapper_Channel_Message} to work with.
     *
     * @var Application_Model_Mapper_Channel_Message
     */
    protected $channelMessageMapper = null;

    /**
     * Stores an instance of {@link Application_Model_Mapper_Channel_Message_Replies} to work with.
     *
     * @var Application_Model_Mapper_Channel_Message_Replies
     */
    protected $channelMessageRepliesMapper = null;

    /**
     * Stores an instance of {@link Application_Model_Mapper_Channel_Subscription} to work with.
     *
     * @var Application_Model_Mapper_Channel_Subscription
     */
    protected $channelSubscriptionMapper = null;

    /**
     * Sets an instance of {@link TrafficUpdates_Model_DbTable_Routes} to work with.
     *
     * @return TrafficUpdates_Model_Mapper_Routes
     */
    public function setDbTable(TrafficUpdates_Model_DbTable_Routes $dbTable) {
        $this->dbTable = $dbTable;
        return $this;
    }

    /**
     * Gets the instance of {@link TrafficUpdates_Model_DbTable_Routes} to work with.
     *
     * @see TrafficUpdates_Model_Mapper_Routes::setDbTable()
     *
     * @return TrafficUpdates_Model_DbTable_Routes
     */
    public function getDbTable() {
        if (!$this->dbTable) {
            $this->setDbTable(new TrafficUpdates_Model_DbTable_Routes());
        }
        return $this->dbTable;
    }

    /**
     * Sets an instance of {@link Application_Model_Mapper_Channel} to work with.
     *
     * @return TrafficUpdates_Model_Mapper_Routes
     */
    public function setChannelMapper(Application_Model_Mapper_Channel $mapper) {
        $this->channelMapper = $mapper;
        return $this;
    }

    /**
     * Gets the instance of {@link Application_Model_Mapper_Channel} to work with.
     *
     * @see TrafficUpdates_Model_Mapper_Routes::setChannelMapper()
     *
     * @return Application_Model_Mapper_Channel
     */
    public function getChannelMapper() {
        if (!$this->channelMapper) {
            $this->setChannelMapper(new Application_Model_Mapper_Channel());
        }
        return $this->channelMapper;
    }

    /**
     * Sets an instance of {@link Application_Model_Mapper_Channel_Message} to work with.
     *
     * @return TrafficUpdates_Model_Mapper_Routes
     */
    public function setChannelMessageMapper(Application_Model_Mapper_Channel_Message $mapper) {
        $this->channelMessageMapper = $mapper;
        return $this;
    }

    /**
     * Gets the instance of {@link Application_Model_Mapper_Channel_Message} to work with.
     *
     * @see TrafficUpdates_Model_Mapper_Routes::setChannelMessageMapper()
     *
     * @return Application_Model_Mapper_Channel_Message
     */
    public function getChannelMessageMapper() {
        if (!$this->channelMessageMapper) {
            $this->setChannelMessageMapper(new Application_Model_Mapper_Channel_Message());
        }
        return $this->channelMessageMapper;
    }

    /**
     * Sets an instance of {@link Application_Model_Mapper_Channel_Message_Replies} to work with.
     *
     * @return TrafficUpdates_Model_Mapper_Routes
     */
    public function setChannelMessageRepliesMapper(Application_Model_Mapper_Channel_Message_Replies $mapper) {
        $this->channelMessageRepliesMapper = $mapper;
        return $this;
    }

    /**
     * Gets the instance of {@link Application_Model_Mapper_Channel_Message_Replies} to work with.
     *
     * @see TrafficUpdates_Model_Mapper_Routes::setChannelMessageRepliesMapper()
     *
     * @return Application_Model_Mapper_Channel_Message_Replies
     */
    public function getChannelMessageRepliesMapper() {
        if (!$this->channelMessageRepliesMapper) {
            $this->setChannelMessageRepliesMapper(new Application_Model_Mapper_Channel_Message_Replies());
        }
        return $this->channelMessageRepliesMapper;
    }

    /**
     * Sets an instance of {@link Application_Model_Mapper_Channel_Subscription} to work with.
     *
     * @return TrafficUpdates_Model_Mapper_Routes
     */
    public function setChannelSubscriptionMapper(Application_Model_Mapper_Channel_Subscription $mapper) {
        $this->channelSubscriptionMapper = $mapper;
        return $this;
    }

    /**
     * Gets the instance of {@link Application_Model_Mapper_Channel_Subscription} to work with.
     *
     * @see TrafficUpdates_Model_Mapper_Routes::setChannelMessageMapper()
     *
     * @return Application_Model_Mapper_Channel_Subscription
     */
    public function getChannelSubscriptionMapper() {
        if (!$this->channelSubscriptionMapper) {
            $this->setChannelSubscriptionMapper(new Application_Model_Mapper_Channel_Subscription());
        }
        return $this->channelSubscriptionMapper;
    }

    /**
     * Creates a new route.
     *
     * @uses Application_Model_Mapper_Channel::create() To create a new channel in ASI.
     *
     * @uses TrafficUpdates_Model_DbTable_Routes::insert() To commit the ID of the ASI
     * channel to the routes database.
     *
     * @param array $data The routes's data:
     *      name        => string
     *      descrption  => string
     */
    public function createRoute(array $data) {
        $channel = $this->getChannelMapper()->create($data);
        $table = $this->getDbTable();
        $table->insert(array(
            'id'      => $channel->getId(),
            'creator' => $channel->getOwnerId()
        ));
        $table->setName('routes_subscribers')->insert(array(
            'route'      => $channel->getId(),
            'subscriber' => $channel->getOwnerId()
        ));
        $table->setName('routes');
    }

    /**
     * Updates a route's information.
     * 
     * @param string $id Route ID.
     *
     * @param array $data Data to update.
     *
     * @return Application_Model_Channel
     */
    public function updateRoute($id, array $data) {
        return $this->getChannelMapper()->update($data, $id);
    }

    /**
     * Deletes a route.
     *
     * @uses Application_Model_Mapper_Channel::delete() To delete a new channel from ASI.
     *
     * @uses TrafficUpdates_Model_DbTable_Routes::insert() To delete the ID of the ASI
     * channel in the routes database.
     *
     * @param string $routeId
     *
     * @return true If successful.
     */
    public function deleteRoute($routeId) {
        $this->getChannelMapper()->delete($routeId);
        $table = $this->getDbTable();
        $table->delete($routeId);
        $table->setName('routes_subscribers')->delete(array('route = ?' => $routeId));
        $table->setName('routes');
        return true;
    }

    /**
     * Fetches all routes accessible to the currently logged in user.
     *
     * @uses Application_Model_Mapper_Channel::fetchAll() To fetch channels from ASI.
     *
     * @uses TrafficUpdates_Model_DbTable_Routes::insert() To fetch routes ID from
     * the database.
     *
     * @return array|false Of {@link Application_Model_Channel}s or false if none.
     */
    public function fetchAllRoutes() {
        $channels = $this->getChannelMapper()->fetchAll('false', 'public');
        if (!$channels) {
            return false;
        }
        $table = $this->getDbTable();
        $rows  = $table->fetchAll();
        if (count($rows) == 0)  {
            return false;
        }
        $ids = array();
        foreach ($rows as $row) {
            $ids[] = $row->id;
        }
        $ret = array();
        $table->setName('routes_subscribers');
        foreach ($channels as $channel) {
            $id = $channel->getId();
            if (in_array($id, $ids)) {
                $select      = $table->select()->from($table, array('count' => 'COUNT(*)'))->where('route = ?', $id);
                $subscribers = $table->fetchRow($select);
                $ret[] = array(
                    'route'       => $channel,
                    'subscribers' => $subscribers ? $subscribers->count : 0
                );
            }
        }
        $table->setName('routes');
        return $ret;
    }

    /**
     * Fetches routes subscribed to by a user.
     *
     * @uses Application_Model_Mapper_Channel::fetchAll() To fetch channels from ASI.
     *
     * @uses TrafficUpdates_Model_DbTable_Routes::insert() To fetch routes ID from
     * the database.
     *
     * @param string $personId
     *
     * @return array|false Of {@link Application_Model_Channel}s or false if none.
     */
    public function fetchRoutesSubscribed($personId) {
        $channels = $this->getChannelMapper()->fetchAll('false', 'public');
        if (!$channels) {
            return false;
        }
        $table  = $this->getDbTable();
        $resultSet = $table->setName('routes_subscribers')->fetchAll(array('subscriber = ?' => $personId));
        $table->setName('routes');
        if (count($resultSet) == 0)  {
            return false;
        }
        $ids = array();
        foreach ($resultSet as $row) {
            $ids[] = $row->route;
        }
        $ret = array();
        for ($i = 0; $i < count($channels); $i++) {
            $channel = $channels[$i];
            $id      = $channel->getId();
            if (in_array($id, $ids)) {
                $ret[] = array(
                    'update' => $this->fetchMostRecentUpdate($id),
                    'route'  => $channel
                );
            }
        }
        return $ret;
    }

    /**
     * Fetches all routes created by a person.
     *
     * @return array|false Of {@link Application_Model_Channel}s or false if none.
     */
    public function fetchRoutesByOwner($personId) {
        $channels = $this->getChannelMapper()->fetchAll('false', 'public');
        if (!$channels) {
            return false;
        }
        $table = $this->getDbTable();
        $rows = $table->fetchAll(array('creator = ?' => $personId));
        if (count($rows) == 0)  {
            return false;
        }
        $ids = array();
        foreach ($rows as $row) {
            $ids[] = $row->id;
        }
        $ret = array();
        $table->setName('routes_subscribers');
        foreach ($channels as $channel) {
            $id = $channel->getId();
            if (in_array($id, $ids)) {
                $select = $table->select()->from($table, array('count' => 'COUNT(*)'));
                $subscribers = $table->fetchRow($select->where('route = ?', $id));
                $ret[] = array(
                    'route'      => $channel,
                    'subscribers' => $subscribers ? $subscribers->count : 0
                );
            }
        }
        $table->setName('routes');
        return $ret;
    }


    /**
     * Fetches a route.
     * 
     * @param string $routeId
     * 
     * @return Application_Model_Channel
     */
    public function fetchRoute($routeId) {
        return $this->getChannelMapper()->fetch($routeId);
    }

    /**
     * Fetches a route's updates. By default will exclude the update's comments i.e.
     * message replies in ASI.
     *
     * @uses Application_Model_Mapper_Channel_Message::fetchAll()
     *
     * @param string $routeId
     * @param int $page
     * @param int $per_page
     * @param bool $exclude_replies
     * @param string $search
     * @param string $sort_order
     *
     * @return array|false Of {@link Application_Model_Mapper_Channel_Message}s or false if none.
     */
    public function fetchUpdates($routeId) {
        return $this->getChannelMessageMapper()->fetchAll($routeId);
    }

    /**
     * Fetches the most recent update in a route.
     *
     * @uses Application_Model_Mapper_Channel_Message::fetchAll()
     *
     * @param string $routeId
     *
     * @return Application_Model_Mapper_Channel_Message|false
     */
    public function fetchMostRecentUpdate($routeId) {
        $updates = $this->getChannelMessageMapper()->setPage(1)
                                                   ->setPerPage(1)
                                                   ->setSortOrder('descending')
                                                   ->fetchAll($routeId);
        return isset($updates[0]) ? $updates[0] : false;
    }

    /**
     * Fetches an update.
     *
     * @param string $routeId
     * @param string $updateId
     * 
     * @return Application_Model_Channel_Message
     */
    public function fetchUpdate($routeId, $updateId) {
        return $this->getChannelMessageMapper()->fetch($routeId, $updateId);
    }

    /**
     * Creates an update (ASI message).
     * 
     * @param string $routeId
     * @param array $data
     * 
     * @return Application_Model_Channel_Message If successful.
     */
    public function postUpdate($routeId, array $data) {
        return $this->getChannelMessageMapper()->create($routeId, $data);
    }

    /**
     * Deletes an update.
     *
     * @param string $routeId
     * @param string $updateId
     *
     * @return true If successful
     */
    public function deleteUpdate($routeId, $updateId) {
        return $this->getChannelMessageMapper()->delete($routeId, $updateId);
    }

    /**
     * Fetches an update's comments (ASI comments).
     *
     * @param string $routeId
     * @param string $updateId
     * @param int $page
     * @param int $per_page
     * @param int $sort_order
     *
     * @return array Of {@link Application_Model_Mapper_Channel_Message_Replies}.
     */
    public function fetchUpdateComments($routeId, $updateId, $page = null, $per_page = null, $sort_order = null) {
        return $this->getChannelMessageRepliesMapper()->fetchAll($routeId, $updateId);
    }

    /**
     * Creates an update's comment (ASI message reply).
     *
     * @param string $routeId
     * @param string $updateId
     * @param array $data
     *
     * @return Application_Model_Channel_Message If successful.
     */
    public function postUpdateComment($routeId, $updateId, $data) {
        $data['reference_to'] = $updateId;
        return $this->getChannelMessageMapper()->create($routeId, $data);
    }

    /**
     * Fetches a route's subscribers.
     * 
     * @param string $routeId
     * 
     * @return Application_Model_Channel_Subscription
     */
    public function fetchSubscribers($routeId) {
        return $this->getChannelSubscriptionMapper()->fetchAll($routeId)->getUserSubscribers();
    }

    /**
     * Subscribes the currently logged in user to a route.
     *
     * @param string $routeId
     *
     * @return true If successful
     *
     * @throws Zend_Db_Statement_Exception If the user is already
     * subscribed to the route.
     */
    public function subscribe($routeId, $personId) {
        $table = $this->getDbTable();
        $table->setName('routes_subscribers')->insert(array(
            'route'      => $routeId,
            'subscriber' => $personId
        ));
        $this->getChannelSubscriptionMapper()->create($routeId);
        $table->setName('routes');
        return true;
    }

    /**
     * Unsubscribes the currently logged in user from a route.
     *
     * @param string $routeId
     *
     * @return true If successful
     */
    public function unsubscribe($routeId, $personId) {
        $this->getChannelSubscriptionMapper()->delete($routeId);
        $this->getDbTable()->setName('routes_subscribers')->delete(array(
            'route = ?' => $routeId,
            'subscriber = ?' => $personId
        ));
        $this->getDbTable()->setName('routes');
        return true;
    }
}