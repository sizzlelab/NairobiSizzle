<?php

class Yearbook_Model_Jobexperiences extends Zend_Db_Table
{

    protected $_name='jobexperiences';


public function savejobexperience($field,$company,$jobdescription,$experience,$dateposted,$username,$userid)
 {

    $data=array('field'=>$field ,'company'=>$company,'jobdescription'=>$jobdescription,'experience'=>$experience,'dateposted'=>$dateposted,'postedby'=>$username,'userid'=>$userid);

	try{

    $this->insert($data);
    }catch(Exception $e)
    {
    echo $e->getMessage();
    }

 }

 public function getjobexperiences($jobfield)
 {
    $select = $this->select()
             ->from('jobexperiences')

             ->where('field = ?', $jobfield)
             ->order('company');


   return $this->getAdapter()->fetchAll($select);

 }
 
public function jobexperiencedetails($id)
{

    $select = $this->select()
             ->from('jobexperiences')

             ->where('experienceid= ?',$id);

   return $this->getAdapter()->fetchAll($select);
       

}

}

?>