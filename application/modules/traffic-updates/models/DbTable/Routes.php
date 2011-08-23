<?php
class TrafficUpdates_Model_DbTable_Routes extends Zend_Db_Table_Abstract {
    /**
     * Stores the name of the database table that stores traffic routes' IDs.
     * 
     * @var string
     */
    protected $_name = 'routes';

    public function setName($name) {
        $this->_name = (string) $name;
        return $this;
    }

    public function getName() {
        return $this->_name;
    }
}
