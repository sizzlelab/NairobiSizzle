<?php

class Forums_Model_DbTable_GroupInfo extends Zend_Db_Table
{

    protected $_name = 'GroupInfo';

    public function getRecord($groupID) {
        $record = $this->fetchRow("groupID = '{$groupID}'");

        if(!$record) {            
            throw new Exception('Could not find group');
        } 
        return $record->toArray();
    }

    public function setRecord($groupID, $channelID) {
        $data = array('groupID' => $groupID, 'channelID' => $channelID);

        $inserted = $this->insert($data);

        if(empty ($inserted))
            throw new Exception ("Could not store course and year information. Please try again later");
    }

//    public function updateRecord($groupID, $courseID, $course, $yearOfStudy) {
//        $data = array('courseID' => $courseID, 'Course' => $course, 'YearOfStudy' => $yearOfStudy);
//
//        $response = $this->update($data, "groupID = '{$groupID}'");
//        if(!$response)
//            throw new Exception ("Could not updated course and year information. Please try again later");
//    }

    public function deleteRecord($groupID) {
        $response = $this->delete("groupID = '{$groupID}'");

        if(!$response)
            throw new Exception ("Could not delete group information. Please try again later");
    }
}

