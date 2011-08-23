<?php

class Campuswisdom_Model_ExperiencesMapper {

    protected $_dbTable;

    public function setDbTable($dbTable) {

        if (is_string($dbTable)) {

            $dbTable = new $dbTable();
        }

        if (!$dbTable instanceof Zend_Db_Table_Abstract) {

            throw new Exception('Invalid table data gateway provided');
        }

        $this->_dbTable = $dbTable;

        return $this;
    }

    public function getDbTable() {
        if (null === $this->_dbTable) {

            $this->setDbTable('Application_Model_DbTable_Experiences');
        }

        return $this->_dbTable;
    }

    public function simplesave($gossip, $ngzones) {
        $data = array(
            'gossip' => $gossip,
            'ngzones' => $ngzones,
        );

        $this->getDbTable()->insert($data);
    }

    public function save(Application_Model_Experiences $University) {
        $data = array(
            'gossip' => $University->getgossip(),
            'ngzones' => $University->getngzones(),
        );



        if (null == ($id = $University->getId())) {

            unset($data['id']);

            $this->getDbTable()->insert($data);
            // var_dump($data);
        } else {

            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    public function find($id, Application_Model_Experiences $University) {

        $result = $this->getDbTable()->find($id);

        if (0 == count($result)) {

            return;
        }

        $row = $result->current();

        $University->setId($row->id)
                ->setgossip($row->gossip)
                ->setngzones($row->ngzones);
    }

    public function fetchAll() {

        $resultSet = $this->getDbTable();

        $entries = array();

        foreach ($resultSet as $row) {

            $entry = new Application_Model_Experiences();

            $entry->setId($row->id)
                    ->setgossip($row->gossip)
                    ->setngzones($row->ngzones);

            $entries[] = $entry;
        }

        return $entries;
    }

}

