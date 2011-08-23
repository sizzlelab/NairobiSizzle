<?php

class Forums_Model_DbTable_IgnoredInvites extends Zend_Db_Table
{

    protected $_name = 'IgnoredInvites';

    public function getIgnored($userID) {
        $select = $this->_db->select();

        $select->from($this->_name, 'groupID')
                ->where("userID = '{$userID}'");

        $groupIDs = $this->_db->fetchAll($select);
        return $groupIDs;
    }

    public function setIgnored($userID, $groupID) {
        $data = array('userID' => $userID,'groupID' => $groupID);

        $inserted = $this->insert($data);

        if(empty ($inserted)) {
            throw new Exception("Could not insert data");
        }
    }
}

