<?php

class Filesharing_Model_Saving extends Zend_db_table
{
    protected $_name='upload';
    public function insertShedef($name,$mimetype,$description,$date,$userid)
    {
        $data= array('name'=>$name,'mimetype'=>$mimetype,'description'=>$description,'date'=>$date,'userid'=>$userid);
        try{
            $this->insert($data);
           }
        catch(Exception $e)
        {
            echo $e->getMessage();
        }
    }
    public function getSavedIdiots()
    {
        return $this->fetchAll();
    }
    public function selectMovie()
    {
        $select = $this->_db->select();
        $select->from($this->_name)
                ->where('description = 2');
        $rowset = $this->getAdapter()->fetchAll($select);
        return $rowset;
    }
    public function selectMusic()
    {
        $select = $this->_db->select();
        $select->from($this->_name)
                ->where('description = 3');
        $rowset = $this->getAdapter()->fetchAll($select);
        return $rowset;
    }
    public function selectPictures()
    {
        $select = $this->_db->select();
        $select->from($this->_name)
                ->where('description = 4');
        $rowset = $this->getAdapter()->fetchAll($select);
        return $rowset;
    }

    public function selectDocuments()
    {   
        $select = $this->_db->select();
        $select->from($this->_name)
                ->where('description = 5');
        $rowset = $this->getAdapter()->fetchAll($select);
        return $rowset;
    }

}
