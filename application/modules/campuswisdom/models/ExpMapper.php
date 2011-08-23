<?php

class Campuswisdom_Model_ExpMapper extends Zend_Db_Table_Abstract {

    protected $_dbTable;
    protected $_name = 'Experiences';

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

            $this->setDbTable('Campuswisdom_Model_DbTable_Experiences');
        }

        return $this->_dbTable;
    }

    public function simplesave($_Category, $_Name, $_Views) {

        $data = array(
            'Category' => $_Category,
            'Name' => $_Name,
            'Views' => $_Views,
        );
        try {
            $this->getDbTable()->insert($data);
        } catch (Exception $e) {
           // echo $e->getMessage();
        }
    }

    public function save($_ExpId, $_Comment) {
        $this->_name = 'comments';
        $data = array(
            'ExpId' => $_ExpId,
            'Comment' => $_Comment,
        );



        try {
            $res = $this->getDbTable()->insert($data);
        } catch (Exception $e) {
            $res = null;
        }
        return $res;
    }

    public function checkCategory($value) {
        $select = 'Select ExpId,Name From Experiences where Category="' . $value . '"';

        $result = $this->_db->query($select);
        return $result;
    }

    public function getExps($catego) {
        $select = $this->_db->select();
        $select->from(array('e' => 'Experiences'),
                        array('Name'))
                ->where('e.category =?', $catego);
        try {
            $rows = $this->_db->fetchAll($select);
            // echo $select;
            return $rows;
        } catch (Exception $e) {
            //echo 'something went wrong';
        }
    }

    public function showView($Name) {
        $select = 'Select * from Experiences where Name="' . $Name . '"';
        return $this->_db->fetchAll($select);
    }

    public function ViewComments($id) {

        $select = 'select * from comments  inner join Experiences on  Experiences.ExpId=comments.ExpId and comments.ExpId=' . (int) $id;
        //var_dump($select);exit;
        return $this->_db->fetchAll($select);
    }

    public function showAll() {
        $select = 'select ExpId,category,Name,Views from Experiences order by ExpId desc';
        return $this->_db->fetchAll($select);
    }

    public function Filter($choice) {
        $select = "select * from Experiences where category=" . "'$choice'" . " order by ExpId Desc";
        return $this->_db->fetchAll($select);
        //  echo $select;
    }

}

