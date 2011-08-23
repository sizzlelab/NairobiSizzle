<?php
/**
 * This class can be used to create any function that should not be included within the controllers of campus wisdom
 * @author Fred<mconyango> & Jux
 * @copyright 2010 Nairobi Sizzle
 * @category NairobiSizzle
 * @package <campuswisdom> Events
 * @subpackage Model
 */
 class campuswisdom_Model_DbTable_Event extends Application_Model_Abstract{
     
      /**
       *@param string $app_id the app_id in session
       * @param string $event_id the event to check whether has been RSVPed by a user
       * @param string $person_id the id of the currently logged in user
       * @return boolen TRUE if the person has RSVPed else FALSE
       */
      public function hasRSVPed($app_id,$event_id,$person_id){
        $Mapper=new Application_Model_Mapper_Appdata_Collection();
        $allrsvp=$Mapper->fetchAll($app_id, $tags='EventRSVP');
        $hasRSVPed=false;
        foreach ($allrsvp as $rsvp){
             $meta=$rsvp->getMetadata();
             $rsvp_event_id=$meta['event_id'];
             $rsvp_user_id=$meta['user_id'];
             if($rsvp_event_id==$event_id and $rsvp_user_id==$person_id){
                  $hasRSVPed=array('id'=>$rsvp->getId(),'rsvp'=>$meta['rsvp']);
                  break;
             }
             
        }
        if($hasRSVPed!=false){
              if($hasRSVPed['rsvp']=='rsvp1') {
                   return array('id'=>$hasRSVPed['id'],'msg'=>'Your RSVP: Attending');

                   }
              else if($hasRSVPed['rsvp']=='rsvp2'){
                   return array('id'=>$hasRSVPed['id'],'msg'=>'Your RSVP: May be attending');

                   }
              else{ 
                   return array('id'=>$hasRSVPed['id'],'msg'=>'Your RSVP: Not attending');
                   }
        }
        else{
             return false;
        }

      }
    /**
     *This function returns all events that are associated to a group
     * @param string $app_id the app_id in session
     * @param string  $group_id
     * @return array  Array containing all the group's events
     */
      public function getGroupEvents($app_id,$group_id){
           $events=new Application_Model_Mapper_Appdata_Collection();
           $result=$events->fetchAll($app_id, $tags='events');
            $arr=array();
            foreach($result as $r){
                 $meta=$r->getMetadata();
                 $date=new Zend_Date(date('Y-m-d'),Zend_Date::ISO_8601);
                 $event_date=new Zend_Date($meta['event_date'],Zend_Date::ISO_8601);
                  if($date->isEarlier($event_date) and $meta['group_id']==$group_id){
                       array_push($arr, $r);
                  }
            }
                 return $arr;
          
      }

      
 }

