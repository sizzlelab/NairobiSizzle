<?php

class GroupController extends Zend_Controller_Action {

    /**
     * NOTE: Open and closed groups are the groups implemented within this controller. Personal and Hidden
     * groups are not necessary within the scope of nairobi sizzle but may be implemented later
     * @var string stores the user id of the currently logged in user
     *
     */
    protected $user_id = null;
    protected $app_id=null;

    public function init() {
        /*
                  * get the details of the currently logged in user
        */
        $session = $this->getFrontController()->getPlugin('Application_Plugin_Util')->getSession();

        $this->user_id = $session->getUserId();
        if (empty ($this->user_id)) {
            //no session
            $this->_helper->redirector('index','login');
        }
        $this->app_id=$session->getAppId();
        //echo $this->app_id;
        if(empty($this->app_id)) {

            //not logged in. Redirect to logging in page
            $this->_helper->redirector('index','login');

        }
    }

    public function indexAction() {
        /*
         * show all the group that the user belongs to
         * if none, then display other groups.
        */
        try {

            $groups=new Application_Model_Mapper_Person_Groups();
            $mygroups=$groups->fetch($this->user_id);
            $formatted_groups=array();
            foreach($mygroups as $g) {

                $events=new campuswisdom_Model_DbTable_Event();
                $num_events=count($events->getGroupEvents($this->app_id, $g->getId()));
                $arr=array($g,$num_events);
                array_push($formatted_groups,$arr);
            }
            //var_dump($formatted_groups);

            /*
          * pagination
            */
            $paginator = new Application_Model_ZendPagination();
            $paginator->setItemCount(5);
            $this->view->groups = $paginator->paginate($formatted_groups, $this->_getParam('page'));
            //$this->view->groups=$mygroups;
        }
        catch(Application_Model_Mapper_Person_Groups_Exception $e) {
            //do something with the exception
        }

    }

    public function creategroupAction() {
        //initialise form
        $form   = new Application_Form_Group_Create();
        $this->view->form=$form;

        if ($this->getRequest()->isPost()) {
            $formdata=$this->getRequest()->getPost(); {
                if ($form->isValid($formdata)) {
                    $formdata=$form->getValues();

                    //get the specific posted values
                    $title=$form->getElement('title')->getValue();
                    $type=$form->getElement('type')->getValue();
                    $desc=$form->getElement('description')->getValue();

                    /*
                      * pass the data to ASI
                    */
                    $group=new  Application_Model_Mapper_Group();

                    try {

                        if(empty($desc)) {

                            $data=array(
                                    'title' =>$title,
                                    'type' =>$type,
                            );

                            $groupobj=$group->create($data, $create_channel='false');
                        }

                        else {

                            $data=array(
                                    'title' =>$title,
                                    'type' =>$type,
                                    'description' =>$desc
                            );

                            $groupobj=$group->create($data, $create_channel='false');

                        }

                        if($groupobj instanceof Application_Model_Group) {

                            $id=$groupobj->getId();
                            /*
                      * create a channel for the group. let the channel type =public and the channel name be the group_id of the created group
                            */
                            $data=array(
                                    'name'=>$id,
                                    'description'=>$groupobj->getDescription(),
                                    'channel_type'=>'public'
                            );
                            $channel=new Application_Model_Mapper_Channel();
                            try {
                                $channelobj=$channel->create($data);
                                // var_dump($channelobj);
                            }  catch (Exception $e) {

                                //@todo  do someting with the exception

                            }

                            /*
                      * redirect to viewgroup action and show success message
                            */
                            if($channelobj instanceof Application_Model_Channel) {
                                $this->_helper->redirector('viewgroup','group',NULL,array('group_id'=>$id,'created'=>'ok'));
                            }
                        }

                    }
                    catch (Exception $e) {
                        //do something
                    }
                }
                else {

                    $form->populate($formdata);
                }

            }
        }
    }

    public function viewgroupAction() {

        $session=new Zend_Session_Namespace('message');
        if(isset($session->feedback)) {
            $this->view->message=$session->feedback;
            unset($session->feedback);
            $session->feedback=null;
        }
        /*
         * clear the param
        */

        $id=(string)$this->_getParam('group_id');

        /*
         * get the group whose id=$id details 
        */

        $group=new Application_Model_Mapper_Group(new Application_Model_Group());
        try {

            $groupdetails=$group->fetch($id);
            //var_dump($groupdetails);
            $this->view->group=$groupdetails;

            //get the groups channel
            if($groupdetails->getIsMember()==true) {
                $user_groups=new Application_Model_Mapper_Channel();
                $user_channels=$user_groups->fetchAll();
                foreach($user_channels as $channels) {

                    if($channels->getName()==$groupdetails->getId()) {
                        $channel_id=$channels->getId();
                        //   var_dump( $channel_id);
                        break;
                    }
                }
                if(!empty($channel_id)) {
                    //get user subscription
                    $subscription_mapper=new Application_Model_Mapper_Channel_Subscription();
                    $user_subscriptions=$subscription_mapper->fetchAll($channel_id);
                    $is_subscribed=false;
                    $data=$user_subscriptions->getUserSubscribers();

                    foreach( $data as $subscriptions ) {

                        if( $subscriptions->getId()==$this->user_id) {
                            $is_subscribed=true;
                            $channel_id=$channels->getId();
                            break;
                        }
                    }



                    if($is_subscribed==true) {
                        //   echo $is_subscribed;
                        $this->view->is_subscribed=true;
                        $this->view->channel_id=$channel_id;
                    }
                    else if($is_subscribed==FALSE) {
                        //  echo $is_subscribed;
                        $this->view->not_subscribed=true;
                        $this->view->channel_id=$channel_id;
                    }
                }

            }
        }
        catch(Exception $e) {
            //do something with the exception
        }

    }

    public function allgroupsAction() {
        /*
         * show all the group that the user belongs to
         * if none, then display other groups.
        */

        /*
          * we can search for a group/groups too
          * @todo create a search form and pass the search term to asi/search/
        */

        $groups=new Application_Model_Mapper_Publicgroups_Mapper();
        try {
            $allgroups=$groups->fetch();
            // var_dump($mygroups);
            /*
           * filter all groups such that we only display groups that the current user is not a member
            */
            $othergroups=array();
            foreach ($allgroups as $g):
                if($g->getIsMember()==FALSE) {
                    //add the object to an array
                    array_push($othergroups, $g);
            }
            endforeach;

            //var_dump($othergroups);
            /*
           * No other groups available  
            */
            if(empty($othergroups)) {

                $this->view->message='No other groups available';

            }

            /*
          * pagination
            */
            else {
                $paginator = new Application_Model_ZendPagination();
                $paginator->setItemCount(5);
                $this->view->groups = $paginator->paginate($othergroups, $this->_getParam('page'));
                //$this->view->groups=$mygroups;
            }
        }
        catch(Application_Model_Mapper_Person_Groups_Exception $e) {
            //do something with the exception
        }

    }

    public function joingroupAction() {
        /**
         *  enables a user to join an open group
         */

        //disable view
        $this->_helper->ViewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();

        $group_id=$this->_getParam('group_id');
        $membership=new Application_Model_Mapper_Person_GroupMembership();
        try {
            if($membership->create($this->user_id, $group_id)) {
                $this->_helper->redirector('viewgroup','group',NULL,array('group_id'=>$group_id ,'message'=>'ok'));
            }
        }
        catch(Exception $e) {
            $this->_helper->redirector('viewgroup','group',NULL,array('group_id'=>$group_id,'message'=>'error'));
        }
    }

    public function membershiprequestAction() {


    }

    public function editgrouptitleAction() {
        //initialise form
        $form   = new Application_Form_Group_EditTitle();
        /*
              * get the group details
        */
        $id=$this->_getParam('group_id');
        $group=new Application_Model_Mapper_Group();
        try {
            $group=$group->fetch($id);
            //var_dump($group->title);
            $default=array('title'=>$group->title);
            $form->populate($default);
        }
        catch(Exception $e) {
            //do something with $e
        }
        $this->view->form=$form;

        if ($this->getRequest()->isPost()) {
            $formdata=$this->getRequest()->getPost(); {
                if ($form->isValid($formdata)) {
                    $formdata=$form->getValues();

                    //get the specific posted values
                    $title=$form->getElement('title')->getValue();

                    /*
                      * pass the data to ASI
                    */
                    $group=new  Application_Model_Mapper_Group();

                    try {
                        $data=array(
                                'title' =>$title
                        );
                        if( $group->update($id, $data)) {
                            /*
                      * redirect to viewgroup action and show success message
                            */
                            $this->_helper->redirector('viewgroup','group',NULL,array('group_id'=>$id,'edited'=>'ok'));
                        }

                    }
                    catch (Exception $e) {
                        //do something
                    }
                }
                else {

                    $form->populate($formdata);
                }

            }
        }
    }

    public function editgrouptypeAction() {
        //initialise form
        $form   = new Application_Form_Group_EditType();
        /*
              * get the group details
        */
        $id=$this->_getParam('group_id');
        $group=new Application_Model_Mapper_Group();
        try {
            $group=$group->fetch($id);
            //var_dump($group->title);
            $default=array('type'=>$group->groupType);
            $form->populate($default);
        }
        catch(Exception $e) {
            //do something with $e
        }
        $this->view->form=$form;

        if ($this->getRequest()->isPost()) {
            $formdata=$this->getRequest()->getPost(); {
                if ($form->isValid($formdata)) {
                    $formdata=$form->getValues();

                    //get the specific posted values
                    $type=$form->getElement('type')->getValue();

                    /*
                      * pass the data to ASI
                    */
                    $group=new  Application_Model_Mapper_Group();

                    try {
                        $data=array(
                                'type' =>$type
                        );
                        if( $group->update($id, $data)) {
                            /*
                      * redirect to viewgroup action and show success message
                            */
                            $this->_helper->redirector('viewgroup','group',NULL,array('group_id'=>$id,'edited'=>'ok'));
                        }

                    }
                    catch (Application_Model_Exception $e) {
                        $this->view->error=$e;
                    }
                }
                else {

                    $form->populate($formdata);
                }

            }
        }
    }

    public function editgroupdescAction() {
        //initialise form
        $form   = new Application_Form_Group_EditDesc();
        /*
              * get the group details
        */
        $id=$this->_getParam('group_id');
        $group=new Application_Model_Mapper_Group();
        try {
            $group=$group->fetch($id);
            //var_dump($group->title);
            $default=array('description'=>$group->description);
            $form->populate($default);
        }
        catch(Exception $e) {
            //do something with $e
        }
        $this->view->form=$form;

        if ($this->getRequest()->isPost()) {
            $formdata=$this->getRequest()->getPost(); {
                if ($form->isValid($formdata)) {
                    $formdata=$form->getValues();

                    //get the specific posted values
                    $desc=$form->getElement('description')->getValue();

                    /*
                      * pass the data to ASI
                    */
                    $group=new  Application_Model_Mapper_Group();

                    try {
                        $data=array(
                                'description' =>$desc
                        );
                        if( $group->update($id, $data)) {
                            /*
                      * redirect to viewgroup action and show success message
                            */
                            $this->_helper->redirector('viewgroup','group',NULL,array('group_id'=>$id,'edited'=>'ok'));
                        }

                    }
                    catch (Exception $e) {
                        //do something
                    }
                }
                else {

                    $form->populate($formdata);
                }

            }
        }
    }

    public function viewgroupeventsAction() {

        try {

            $this->view->personid=$this->user_id;
            $this->view->appid=$this->app_id;
            $group_id=$this->_getParam('group_id');

            $appdata=new Application_Model_Mapper_Appdata_Collection();
            $events=$appdata->fetchAll($this->app_id, $tags='events');
            $group_events=array();
            foreach($events as $e) {
                $meta=$e->getMetadata();
                if($meta['group_id']==$group_id) {
                    array_push($group_events, $e);
                }
            }
            //Zend_Debug::dump($group_events);
            //pagination
            $paginator = new Application_Model_ZendPagination();
            $paginator->setItemCount(5);
            $this->view->events = $paginator->paginate($group_events, $this->_getParam('page'));

        }
        catch(Exception $e) {
            //todo:handle the caught exception
        }

    }
}
