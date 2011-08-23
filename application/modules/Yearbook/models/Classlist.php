<?php

class Yearbook_Model_Classlist extends Zend_Db_Table
{
     protected $_name='classlist';

    public function insertclasslist($user_id,$course,$year)
    {
        $data=array('user_id'=>$user_id ,'course'=>$course, 'year'=>$year);
        return $this->insert($data);
    }

    public function getUsers($course,$year)
 {

   $select = $this->select()
             ->from(('classlist'),
           array('user_id'))

             ->where('course = ?', $course)
           
             ->where('year = ?', $year);


   return $this->getAdapter()->fetchAll($select);
   //echo $select;exit;


 }
  public function checkuser($user_id){
      $select = $this->_db->select();
      $select->from('classlist')
             ->where('user_id= ?', $user_id);
      $row = $this->getAdapter()->fetchRow($select);
      if(empty ($row)){
          return true;
      }else{
          return false;
      }
  }

  public function getCourse($user_id){
      $select = $this->_db->select();
      $select->from($this->_name)
              ->where('user_id = ?', $user_id);
      $row = $this->getAdapter()->fetchRow($select);
      return $row;
  }


}
?>

