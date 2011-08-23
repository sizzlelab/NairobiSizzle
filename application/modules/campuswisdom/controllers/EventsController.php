<?php

class Campuswisdom_EventsController extends Zend_Controller_Action {


    /**
     *
     * @var int the person_id of the currently logged in user
     */
    protected $person_id=null;

    protected $app_id=null;


    public function init() {
        $session = $this->getFrontController()->getPlugin('Application_Plugin_Util')->getSession();
        $person_id = $session->getUserId();
        if (empty($person_id)) {
            //user is nolonger logged in
            $this->_helper->redirector('index','login');
        }
        else {

            $this->person_id=$person_id;
        }

        $this->app_id=$session->getAppId();
        //echo $this->app_id;
        if(empty($this->app_id)) {

            //not logged in. Redirect to logging in page
            $this->_helper->redirector('index','login');



        }
    }

    public function indexAction() {
        /**
         * This action by default displays the most current events posted by
         * public groups and those private groups that the user is a member.
         * Events are ordered by date by default
         */

        //pass the title to the view
        $this->view->title="Events";
        $session=new Zend_Session_Namespace('message');
        if(isset($session->feedback)) {
            $this->view->message=$session->feedback;
            unset($session->feedback);
            $session->feedback=null;
        }
        /*
         * pass the following variables to the view to be used in testing whether a user has RSVPed to an event
        */
        $this->view->appid=$this->app_id;
        $this->view->personid=$this->person_id;
        $events_array=array();
        //get all current events
        $eventMapper=new Application_Model_Mapper_Appdata_Collection();
        $all_events=$eventMapper->fetchAll($this->app_id,$tags='events');

        //call on the groups mapper
        $group_mapper=new Application_Model_Mapper_Group();
        //create an empty array
        //insert details of each event into the array
        foreach($all_events as $events) {
            $meta=$events->getMetadata();
            //get all events happening today and beyond
            if($meta['event_date']>=date('Y-m-d')) {
                //get the hosting group's name
                //var_dump($meta);
                if(isset($meta['group_id'])) {
                    $group_details=$group_mapper->fetch($meta['group_id']);

                    /*
                     *  check of the user is a member of the group that has the event.
                     *  If the user is not a member then just show the event
                     * else allow the user to RSVP to that event
                    */


                    $gmembership=new Application_Model_Person_GroupMembership();
                    $gmembership->setGroupId($meta['group_id']);

                    $gmembershipmapper=new Application_Model_Mapper_Person_GroupMembership($gmembership);
                    try {
                        $m_ship= $gmembershipmapper->fetch($this->person_id);
                        // var_dump($m_ship);
                        // if admin role is 1 then  push then add the group into $arr_groups
                    }catch(Exception $e) {

                        //do something
                    }

                    if(!empty($m_ship)) {
                        $ismember=true;
                    }
                    else {
                        $ismember=false;
                    }
                    $arr=array('isMember'=>$ismember,'id'=>$events->getId(),'group'=>$group_details->getTitle(),'event_name'=>$meta['event_name'],'description'=>$meta['event_agenda'],'event_charges'=>$meta['event_charges']);
                    array_unshift($events_array, $arr);
                }
                //create array to hold each events metadata.

            }
        }
        //pass the information to the view
        if(empty($events_array)) {
            $this->view->events=null;
        }
        else {
            $paginator=new Application_Model_ZendPagination(5);
            $this->view->events=$paginator->paginate($events_array, $this->_getParam('page'));
        }
    }
    public function vieweventsdetailsAction() {
        /**
         * Action displays the details of the event.
         * Only the creator or the admin can edit the event details.
         */

        //show msg
        $session=new Zend_Session_Namespace('message');
        if(isset($session->feedback)) {
            $this->view->message=$session->feedback;
            unset($session->feedback);
            $session->feedback=null;
        }
        //pass the title of the page
        $this->view->title="Event Details";

        //get the events id
        $id=$this->_getParam('event_id');
        $this->view->event_id=$id;

        //get the details of the event
        $events_mapper=new Application_Model_Mapper_Appdata_Collection();
        $event_object=$events_mapper->fetch($this->app_id, $id);
        /*
         *  get the number of those who have RSVPed the event
        */
        try {
            $rsvp=$events_mapper->fetchAll($this->app_id,$tags="EventRSVP");
            //var_dump($rsvp);
        }catch(Exception $e) {
            //do something
        }
        $attending=0;
        $maybe= 0;
        $notattending=0;
        if(!empty ($rsvp)) {
            foreach($rsvp as $rs) {
                $metadata=$rs->getMetadata();
                /*
              * if event id of the rsvp matches the event id of the event then check whether the user is attending the event,or not decided or not attending
             *
                */
                if($id==$metadata['event_id']) {
                    if($metadata['rsvp']=='rsvp1')  $attending++;
                    else if($metadata['rsvp']=='rsvp2')  $maybe++;
                    else if($metadata['rsvp']=='rsvp3')  $notattending++;

                }
            }
        }

        $this->view->attending=$attending;
        $this->view->maybe=$maybe;
        $this->view->notattending=$notattending;
        //get the logged in person
        $this->view->current_user=$this->person_id;

        //get the owner id
        $this->view->owner_id=$event_object->getOwner();

        //get the metadata for the event
        $event=$event_object->getMetadata();
        $this->view->events=$event;
        //get the hosting group's name
        $group_mapper=new Application_Model_Mapper_Group();
        $group_details=$group_mapper->fetch($event['group_id']);
        $this->view->group_name=$group_details->getTitle();
    }
    public function groupdetailsAction() {
        /**
         * The function displays the details of the event hosting group. its supports
         * 1. A subscribed member can unsubscribe
         * 2. Unsubscribed member can subscribe
         * 3. A non-member can join the group
         */

        $this->view->title="Hosting group";

        //get the group id from the url
        $group_id=$this->_getParam('id');

        //get the group details
        $group_mapper=new Application_Model_Mapper_Group();
        $group_details=$group_mapper->fetch($group_id);
        $group=$group_details->getData();
        $this->view->group_details=$group;

        //format date
        $date_model=new Application_Model_Date();
        $this->view->date=$date_model->toString($group['created_at']);

        /**
         * check if user is a member of the group
         */

        //get list of group members
        $group_members=$group_mapper->getMembers($group_id);
        //loop through members and check if user is a member
        $a_member=false;
        foreach($group_members as $members) {
            if($members->id==$this->person_id) {
                $a_member=true;
                break;
            }

        }
        //if user is a member, check if user is already subscribed
        if($a_member==true) {

        }
        //if the user is not a member, provde a link to be a member.
        else {
            $this->view->join=true;
        }

    }

    public function editeventAction() {
        /**
         * This action by default displays the most current events posted by
         * public groups and those private groups that the user is a member.
         * Events are ordered by date by default
         */

        //pass the title to the view
        $this->view->title="Edit Event";
        //get the events id
        $id=$this->_getParam('event_id');
        $group=$this->_getParam('group_id');
        $this->view->event_id=$id;


        //get the event details

        $events_mapper=new Application_Model_Mapper_Appdata_Collection();
        $event_object=$events_mapper->fetch($this->app_id, $id);
        $events=$event_object->getMetadata();

        //function to display the form
        $form=new campuswisdom_Form_Events_Editevent($events);
        $request = $this->getRequest();
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $formdata=$form->getValues();
                //get the posted details
                $name=$form->getElement('event_name')->getValue();
                $desc=$form->getElement('event_agenda')->getValue();
                $venue=$form->getElement('event_venue')->getValue();
                $date=$form->getElement('event_date')->getValue();
                $time=$form->getElement('event_time')->getValue();
                $charges=$form->getElement('event_charges')->getValue();
                //verify that the date is later than today
                $today=date("Y-m-d");
                if($date<= $today) {
                    $this->view->message="Choose a date greater that today";
                    $form->populate($formdata);
                }
                else//valid details
                {
                    //delete event first
                    $collection_mapper=new Application_Model_Mapper_Appdata_Collection;
                    $result=$collection_mapper->delete($this->app_id, $id);
                    //if($result==$id)//if event is deleted
                    // {
                    //create the  updated event
                    $metadata=array(
                            'group_id'=>$group,
                            'event_name'=>$name,
                            'event_agenda'=>$desc,
                            'event_venue'=> $venue,
                            'event_date'=> $date,
                            'event_time'=> $time,
                            'event_charges'=> $charges,

                    );

                    $data=array(
                            'title'=>'Events',
                            'owner_id'=>$this->person_id,
                            'priv'=>'false',
                            'read_only'=>'true',
                            'tags'=>'Events',
                            'metadata'=>$metadata );
                    /*
                      * pass the data to appData
                    */
                    var_dump($data);
                    $appdata= new Application_Model_Mapper_Appdata_Collection();
                    $event_object=$appdata->create($this->app_id, $data);
                    if(is_object($event_object)) {
                        $arr=array('event_id'=>$event_object->getId());
                        $session=new Zend_Session_Namespace('message');
                        $session->feedback='Event updated successfully';
                        $this->_helper->redirector('vieweventsdetails', 'events', 'campuswisdom',$arr);

                    }
                    //  }
                }
            }
        }
        $this->view->form=$form;
    }







    public function confirmdeleteeventAction() {
        /**
         * The function deletes an Event
         * The event is only be deleted by its owner
         * The function gets the application id and the event_id and call the Appdata functions
         */

        //display the title
        $this->view->title="Delete Event";

        //get the event_id
        $id=$this->_getParam('event_id');
        $this->view->event_id=$id;

        //get the application id
        try {
            $application_id=new Application_Model_Session();
            $application_id->startSession();
            $app_id=$application_id->getAppId();

            //get the event details
            $events_mapper=new Application_Model_Mapper_Appdata_Collection();
            $event_object=$events_mapper->fetch($app_id, $id);
            $events=$event_object->getMetadata();
            $this->view->events=$events;

            //get the hosting group's name
            $group_mapper=new Application_Model_Mapper_Group();
            $group_details=$group_mapper->fetch($events['group_id']);
            $this->view->group_name=$group_details->getTitle();
        }
        catch(Exception $e) {

        }
    }

    public function deleteeventAction() {
        /**
         * The function delete an event that the user has earmarked for deletion
         * It gets the event id
         */

        //get the event id
        $id=$this->_getParam('event_id');
        $this->view->event_id=$id;
        try {
            //delete the event
            $appdata_mapper=new Application_Model_Mapper_Appdata_Collection();
            $result=$appdata_mapper->delete($this->app_id, $id);
            $message="Event deleted successfully";
            $session=new Zend_Session_Namespace('message');
            $session->feedback=$message;
            $this->_helper->redirector('index', 'events', 'campuswisdom');
        }
        catch(Exception $e) {

        }


    }

    public function subscribetogroupAction() {
        /**
         * A user can subscribe to a group inoerder
         * to receive notifications once the group posts an event
         */
        //get the channel id from url
        $channel_id=$this->_getParam('channel_id');
        //get group id
        //$id=$this->_getParam('group_id');
        $arr=array('group_id'=>$this->_getParam('group_id'));

        try {
            //create a subscription to the group
            $channel_mapper=new Application_Model_Mapper_Channel_Subscription();
            $result=$channel_mapper->create($channel_id);
            if($result=="subscription successful") {
                $message="Successful subscription";
                $session=new Zend_Session_Namespace('message');
                $session->feedback=$message;
            }
            $this->_helper->redirector('viewgroup', 'group', 'default',$arr);
        }
        catch(Exception $e) {

        }




    }

    public function unsubscribetogroupAction() {
        /**
         * Function creates user subscription to a given group
         */
        //get the channel id
        try {
            $channel=$this->_getParam('channel_id');
            $arr=array('group_id'=>$this->_getParam('group_id'));
            //unsubscribe the user
            $channel_mapper=new Application_Model_Mapper_Channel_Subscription();
            $result=$channel_mapper->delete($channel);
            //take back to previous page
            //var_dump($result);exit;
            if($result=="unsubscription successful") {
                $message="Successful unsubscription";
                $session=new Zend_Session_Namespace('message');
                $session->feedback=$message;
            }
            $this->_helper->redirector('viewgroup', 'group', 'default',$arr);
        }
        catch(Exception $e) {

        }

    }

    public function addeventAction() {
        /**
         *  This action add an event created by a privileged member of a group
         *  If  a user who either does not belong to a group or is not admin of a group attempts to create an event
         * He/she is redirected to a page that allows him/her to create a group before allowing him/her to create the group
         *
         */

        $this->view->title="Add Event";

        /*
          * Fetch all groups of the user where he/she has adminRole.
          * If no group fetched then alert the user that he/she either does not have a group or has no adminRole
        */

        $person_groups = new Application_Model_Mapper_Person_Groups();


        $groups=$person_groups->fetch($this->person_id);

        if(empty ($groups)) {
            $this->view->message='You currently do not belong to any group! Create a group and invite your friends to before creating an event';

            /*
               * redirect to the action that creates a group.
            */

        }

        /*
          * save the group title in an array
        */
        // var_dump($groups);
        $arr_groups=array();
        foreach($groups as $g):

            /*
             *  check whether the user is admin of that particular group
             *  should we restrict event creation to only members of a group that have admin role in the group or any group member?
            */

            /*
             $gmembership=new Application_Model_Person_GroupMembership();
             $gmembership->setGroupId($g->getId());

             $gmembershipmapper=new Application_Model_Mapper_Person_GroupMembership($gmembership);
             $m_ship= $gmembershipmapper->fetch($this->person_id);

              *  if admin role is 1 then  push then add the group into $arr_groups
            */

            /*
             if($m_ship->getAdminRole()==1) {
                 array_push($arr_groups, array('key'=>$g->getId(),'value'=>$g->getTitle()));
             }
            */

            array_push($arr_groups, array('key'=>$g->getId(),'value'=>$g->getTitle()));

        endforeach;

        //var_dump($arr_groups);
        /*
         * check if $arr_group is empty.
        */

        if(empty ($arr_groups)) {
            $this->view->message='You do not have admin role in any of your groups.To proceed please create a new group and invite members';
        }
        else {

            $form=new campuswisdom_Form_Events_AddEvent($arr_groups);
            //pass the form to the view
            $this->view->form=$form;
        }
        if ($this->getRequest()->isPost()) {
            $formdata=$this->getRequest()->getPost(); {
                if ($form->isValid($formdata)) {
                    $formdata=$form->getValues();

                    //get the posted values
                    $group=$form->getElement('group')->getValue();
                    $name=$form->getElement('name')->getValue();
                    $desc=$form->getElement('description')->getValue();
                    $venue=$form->getElement('venue')->getValue();
                    $date=$form->getElement('date')->getValue();
                    $time=$form->getElement('time')->getValue();
                    $charges=$form->getElement('charges')->getValue();

                    //validate date
                    if(Application_Model_Date::isEarlier($date)) {
                        $error='Date should not be earlier than today';
                        $this->view->error=$error;
                    }
                    /*
                 *  perform the basic validation of input
                    */
                    if(!isset ($error)) {
                        $metadata=array(
                                'group_id'=>$group,
                                'event_name'=>$name,
                                'event_agenda'=>$desc,
                                'event_venue'=> $venue,
                                'event_date'=> $date,
                                'event_time'=> $time,
                                'event_charges'=> $charges,

                        );

                        $data=array(
                                'title'=>'Events',
                                'owner_id'=>$this->person_id,
                                'priv'=>'false',
                                'read_only'=>'true',
                                'tags'=>'Events',
                                'metadata'=>$metadata

                        );
                        /*
                      * pass the data to appData
                        */
                        $appdata= new Application_Model_Mapper_Appdata_Collection();
                        $event_object=$appdata->create($this->app_id, $data);
                        if(is_object($event_object)) {
                            $arr=array('event_id'=>$event_object->getId());
                            $session=new Zend_Session_Namespace('message');
                            $session->feedback='Event created successfully';
                            $this->_helper->redirector('vieweventsdetails', 'events', 'campuswisdom',$arr);

                        }
                    }

                }
                else {

                    $form->populate($formdata);
                }

            }
        }

    }

    public function rsvpAction() {
        $id=$this->_getParam('event_id');
        /*
         * initialise the form
        */
        $form=new campuswisdom_Form_Events_Rsvp();
        $this->view->form=$form;
        if ($this->getRequest()->isPost()) {
            $formdata=$this->getRequest()->getPost(); {
                if ($form->isValid($formdata)) {
                    $formdata=$form->getValues();
                    $rsvp=$form->getElement('rsvp')->getValue();
                    $metadata=array(
                            'event_id'=>$id,
                            'user_id'=>$this->person_id,
                            'rsvp'=>$rsvp
                    );

                    $data=array(
                            'title'=>'EventRSVP',
                            'owner_id'=>$this->person_id,
                            'priv'=>'false',
                            'read_only'=>'true',
                            'tags'=>'EventRSVP',
                            'metadata'=>$metadata

                    );
                    $appdata= new Application_Model_Mapper_Appdata_Collection();
                    $rsvp=$appdata->create($this->app_id, $data);
                    if(is_object($rsvp)) {
                        $arr=array('event_id'=>$id);
                        $session=new Zend_Session_Namespace('message');
                        $session->feedback='RSVP successfully saved';
                        $this->_helper->redirector('vieweventsdetails', 'events', 'campuswisdom',$arr);

                    }
                }
                else {

                    $form->populate($formdata);
                }
            }
        }
    }

    public function rsvpdetailsAction() {

        $event_id=$this->_getParam('event_id');
        $rsvp_mapper=new Application_Model_Mapper_Appdata_Collection();
        try {
            $rsvp=$rsvp_mapper->fetchAll($this->app_id, $tag='EventRSVP');
        }catch(Exception $e) {
            //todo: handle the exception
        }

        if(!empty ($rsvp)) {
            $people=array();
            foreach ($rsvp as $r):
                $meta=$r->getMetadata();
                if($meta['event_id']==$event_id) {
                    array_push($people,$meta['user_id'] );
            }
            endforeach;
        }
        /*
          * check if  array $people  is set and is not empty
        */
        if(isset ($people) and !empty($people)) {
            $person=new Application_Model_Mapper_Person();
            $rsvppeople=array();
            try {
                for($i=0;$i<count($people);$i++) {
                    array_push($rsvppeople, $person->fetch($people[$i]));
                }
            }  catch (Exception $e) {
                //todo: handle the exception
            }
            //echo $rsvppeople[0]->id;
            $paginator=new Application_Model_ZendPagination(5);
            $this->view->people=$paginator->paginate($rsvppeople, $this->_getParam('page'));
        }
    }

    public function editrsvpAction() {
        $event_id=$this->_getParam('event_id');
        $id=$this->_getParam('id');
        /*
         * initialise the form
        */
        $form=new campuswisdom_Form_Events_EditRSVP();
        $this->view->form=$form;
        if ($this->getRequest()->isPost()) {
            $formdata=$this->getRequest()->getPost(); {
                if ($form->isValid($formdata)) {
                    $formdata=$form->getValues();
                    //user has cancelled the action
                    if($this->_request->getPost('cancel')) {
                        $this->_helper->redirector('index','events','campuswisdom');
                    }
                    $rsvp=$form->getElement('rsvp')->getValue();
                    $metadata=array(
                            'event_id'=>$event_id,
                            'user_id'=>$this->person_id,
                            'rsvp'=>$rsvp
                    );

                    $data=array(
                            'title'=>'EventRSVP',
                            'owner_id'=>$this->person_id,
                            'priv'=>'false',
                            'read_only'=>'true',
                            'tags'=>'EventRSVP',
                            'metadata'=>$metadata

                    );
                    $appdata= new Application_Model_Mapper_Appdata_Collection();
                    /*
                     * because updating appdata does not work well with ASI a longer and probably bad method of updating appdata has been
                     * used. Updating is done by first deleting the record then inserting a new one.
                    */
                    if ( $appdata->delete($this->app_id, $id)) {
                        /*
                          * insert a new record
                        */
                        $rsvp=$appdata->create($this->app_id, $data);
                        if(is_object($rsvp)) {
                            $arr=array('event_id'=>$event_id);
                            $session=new Zend_Session_Namespace('message');
                            $session->feedback='RSVP successfully edited';
                            $this->_helper->redirector('vieweventsdetails', 'events', 'campuswisdom',$arr);

                        }
                    }
                }
                else {

                    $form->populate($formdata);
                }
            }
        }
    }


}
