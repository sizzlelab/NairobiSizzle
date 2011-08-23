<?php

class Forums_ThreadController extends Zend_Controller_Action
{

    protected $channel = 'aJr-EqWfmr35FWakdrL-mr';

    protected $publicchannel = 'cT1ntEPMLfK4dnUy6RscvZ';
    private static $sessionHandle = null;
    private static $groupHandle = null;
    private static $credentials = null;

    public function init()
    {
        $session = new Application_Model_Session();
        $credentials = new Zend_Session_Namespace('credentials');
        if(!$session->startSession() && empty($credentials->user_id)) {
            $session->setSessionParameter('redirect_to', 1);
            $session->setSessionParameter('get_params', $this->_getAllParams());
            $session->setSessionParameter('params', $this->getRequest()->getParams());
            $this->_helper->redirector('index','login','default');
        }
    }

    public function indexAction()
    {
        // action body
    }

    public function startthreadAction()
    {
        // action body
                /*This creates a form element on which all the requirements of a thread are captured
                                                                 * The thread to be created can be started by any person for their group or for public viewing
                */
                //make sure the user is logged in
                $this->view->groupID = $this->_getParam('id',null);
                $str = new Application_Model_Session();
                $str->startSession('frankenstyn','franko');
                $this->view->title='Pose a question';
                $form = new Forums_Form_Thread();
                $this->view->form = $form;
                if($this->getRequest()->isPost()) {
                    $formData=$this->getRequest()->getPost();
                    if($form->isValid($formData)) {
                        $collect = new Forums_Model_ThreadMapper($form->getValues());
                        if($collect->validate()) {
        
                            $result = new Forums_Model_Thread_Mapper($collect);
                            // $c=$result->create_app_log_in();
                            if($collect->checkPublicity()) {
                                //This ensures that public messages are redirected to the
                                //public channel
                                $this->channel=$this->publicchannel;
        
                            }
                            else
                                {
                                $this->channel =$this->sessionHandle->getSessionParameter('channel');
                                }
                            $res=$result->create($this->channel);
                            if(!$res) {
                                //give the user the appropriate message
                                //save it in session to enable hidden tranfer..
                                $str->setSessionParameter('thread', 1);
        
                                $this->_helper->redirector('get-group-threads','thread','forums');
                            }
                            else {
                                //give the user the message that their request failed
                                $this->view->error ="Sorry,the request could not be completed: ".$res;
        
                            }
        
                            //  var_dump($c);
                        }
                        else {
                            //rerender the form with the errors indicated
                        }
                    }
                    else {
                        $form->populate($formData);
                    }
    }
    }
    public function getActiveThreadsAction()
    {
        // function to return current active threads for this group
                $this->view->title= "Your current threads";
                // $discussions = new Forums_Model_Thread();
                // $discussion=$discussions->get_my_threads();
                $this->view->data=null;
    }

    public function getThreadDetailsAction()
    {
        $shedef= new Application_Model_Session();
                if($shedef->getSessionParameter('ans')==1)
                        {
                    $shedef->unsetSessionParameter('ans');
                    $this->view->ok = "Your answer has been successfully posted";
                        }
                $threadid =$this->_getParam('did');
                if(!$threadid) {
                    $this->view->msg = true;
        
                }
                else {
                    $threader = new Forums_Model_Thread_Mapper();
                    $thread =$threader->fetch($this->channel, $threadid);
                    if(is_null($thread)) {
                        $this->view->msg = true;
                    }
                    else {
                        $this->view->thread=$thread;
                    }
                }
    }

    public function answerThreadAction()
    {
        // action body
                $session = new Application_Model_Session();
                $session->startSession();
                $thread =$this->_getParam('qnid');
                if(!$thread) {
                    $this->view->msg ="Please choose a question to give insight";
                }
                $form = new Forums_Form_Answer();
                $this->view->form= $form;
                if($this->getRequest()->isPost()) {
                    $formData= $this->getRequest()->getPost();
                    if($form->isValid($formData)) {
                        //obtain the answer from the form and post it to server
                        $answer = new Forums_Model_Answer_Mapper();
                        //if($answer->validate()) {
                        //create an answer to this particular thread
                        try {
                            $res =$answer->create($this->channel,$form->getValue('answer'),$thread);
                           
                            if($res==null) {
                                $session->setSessionParameter('ans', 1);
                                
                                $this->_helper->redirector('get-thread-details','thread','forums',array('did'=>$thread));
                            }
                            else {
                                $this->view->error ="Sorry,the request could not be completed.please try again shortly";
                            }
                        }catch (Exception $e) {
                            $this->view->error="Sorry,the request couldnot be completed because an error occurred.";
                        }
                    }else {
                        $form->populate($formData);
                    }
    }
    }
    public function getGroupThreadsAction()
    {
        $groupID = $this->_getParam('id',null);
        
        if(is_null($groupID))
            {
            $this->channel =$this->sessionHandle->getSessionParameter('channel');
            $groupID =$this->sessionHandle->getSessionParameter('group');
           // var_dump($this->sessionHandle->getSessionParameter('channel'));exit;
            $this->view->groupID=$groupID;            
            }else
                {
                $this->view->groupID=$groupID;var_dump($groupID);
                $groupInfo = new Forums_Model_DbTable_GroupInfo();
        $record='';
        try {
            $record = $groupInfo->getRecord($groupID);
                   $this->sessionHandle->setSessionParameter('channel',$record['channelID']);
                   $this->sessionHandle->setSessionParameter('group',$record['groupID']);
           $this->channel=$record['channelID'];
//var_dump($record);exit;
        }catch(Exception $e) {
            //var_dump($e);
            $this->_helper->redirector('get-public-threads','thread','forums');
        }
            //        return $record['channelID'];
                }
        // action body

                $congrats= new Application_Model_Session();
                if($congrats->getSessionParameter('thread')==1) {
                    $congrats->unsetSessionParameter('thread');
                    //The user should be notified that a thread has been successfully created
                    $this->view->msg="Your question has been successfully posted!";
                }
                
                $messages = new Forums_Model_Thread_Mapper();
                if(is_null($this->channel))
                    {
                     $this->_helper->redirector('get-public-threads','thread','forums');
                    }
                $reqvalue =$messages->fetch($this->channel);
                if(is_null($reqvalue)) {
                    $this->view->error="Sorry,the request could not be completed.Please try again shortly";
                }
                elseif (!$reqvalue) {
                    $this->view->error;
                }
                else {
                    if(is_array($reqvalue)) {
                        $this->view->title='Questions posted';
                    $pgntr= new Application_Model_ZendPagination();
                    $this->view->requests= $pgntr->paginate($reqvalue['entry'],$this->_getParam('page'));
        
        
                       // $this->view->requests= $reqvalue;
                    }else {
                        $this->view->error="Sorry,the request could not be completed.Please try again in a while";
                    }
    }
    }
    public function getThreadAnswersAction()
    {
        /**
                 * This function gets a parameter message id and gets all the replies to that message
                 * These replies are then sorted in a descending order by time. The user gets a list of a
                 * all the replies to this thread
                 */
                $thread = $this->_getParam('threadid',0);
                if($thread==0) {
                    //show message indicating that the user has not chosen a thread
                }
                $session = new Application_Model_Session();
                $session->startSession();
                $answer = new Forums_Model_Answer_Mapper();
                try {
                    $answers =$answer->fetch($this->channel,$thread);
        
                    if($answers==null) {
                        //Notify the user that there are no results to show
        
                        $this->view->error="No answer has been posted yet";
                    }
                    if(is_array($answers)) {
                        if(!isset($answers['entry'][0]['reference_to'])) {
                            $this->view->qnid =$thread;
                            $this->view->title="Answer this question";
                        }else {
        
                            $this->view->notEmpty=true;
                            $this->view->answers =$answers;
                            $this->view->title="Answers to this question";
                        }
        
                    }
                    else {
                        $this->view->error="Sorry,an error occurred.We'll look at it shortly.";
                    }
        
                }
                catch (Exception $e) {
                    //notify the user of an error in the request
                    $this->view->error="Sorry,an error occurred.We'll look at it shortly.";
    }
    }
    public function likePostAction()
    {
        // action body
                //Enables a  user to agree with a particular answer or post a better one
                $this->getHelper ( 'viewRenderer' )->setNoRender ();
                $this->_helper->layout()->disableLayout();
                $session = new Application_Model_Session();
                $session->startSession();
                $thread = $this->_getParam('threadlike');
                if($thread) {
                    $liker = new Forums_Model_Misc_Mapper();
                    try {
                        $liked= $liker->agree($this->channel,$thread);
                        if($liked!=null) {
                            $this->view->msg = "The request could not be completed";
                        }
                        $this->_helper->redirector('get-group-threads','thread','forums');
                    }
                    catch(Exception $e) {
                        //show the user that an error has occurred
                        var_dump($e->__toString());
        
                    }
    }
    }
    public function getPublicThreadsAction()
    {
        // action body
           $congrats= new Application_Model_Session();
                if($congrats->getSessionParameter('thread')==1) {
                    $congrats->unsetSessionParameter('thread');
                    //The user should be notified that a thread has been successfully created
                    $this->view->msg="Your question has been successfully posted!";
                }
                //$id= $this->_getParam('chan');
                
                $messages = new Forums_Model_Thread_Mapper();
                $reqvalue =$messages->fetch($this->publicchannel);
                if(is_null($reqvalue)) {
                    $this->view->error="Sorry,the request could not be completed.Please try again shortly";
                }
                elseif (!$reqvalue) {
                    $this->view->error;
                }
                else {
                    if(is_array($reqvalue)) {
                        $this->view->title='Questions posted';
                    $pgntr= new Application_Model_ZendPagination();
                    $this->view->requests= $pgntr->paginate($reqvalue['entry'],$this->_getParam('page'));


                       // $this->view->requests= $reqvalue;
                    }else {
                        $this->view->error="Sorry,the request could not be completed.Please try again in a while";
                    }
    }
}
}