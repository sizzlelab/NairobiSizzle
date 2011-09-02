<?php
class IndexController extends Zend_Controller_Action {

    public function indexAction() {
        if ($this->view->isUserLogged) {
            $plugin  = $this->getFrontController()->getPlugin('Application_Plugin_Util');
            $id      = $plugin->getSession()->getUserId();
            $request = $this->getRequest();
            //get friend requests
            $requestsMapper = new Application_Model_Mapper_Person_PendingFriendRequests();
            $this->view->friendRequests = $requestsMapper->fetch($id);
            //get friends feed
            $feedMapper = new Application_Model_Mapper_Person_Friends();
            $friends    = $feedMapper->setSortBy('status_changed')
				     ->setSortOrder('descending')
				     ->fetch($id);
            $feed       = array();
            $count      = 0;
            //filter friends with an update
            foreach ($friends as $friend) {
                if ($friend->status && $friend->status->message && $count++ < 10) {
                    $feed[] = $friend;
                }
            }
            $this->view->friendsFeed = $feed;
            //status update
            $form = new Application_Form_Person_StatusMessage();
            $form->getElement('submit')->setLabel('Update status');
            $this->view->updateForm = $form;
            if ($request->isPost()) {
                if (!$form->isValid($request->getPost())) {
                    $this->view->form = $form;
                    return $this->render();
                }
                $status = $form->getValue('status_message');
                try {
                    $mapper = new Application_Model_Mapper_Person();
                    $mapper->update(array('status_message' => $status), $id);
                    $form->reset();
                    $this->view->statusUpdated = true;
                } catch (Application_Model_Mapper_Person_Exception $e) {
                    $this->view->errors = $this->mapper->getErrors();
                }
            }
            //$plugin->addPostDispatchCallback(array($feedMapper, 'fetch'), $id);
        }
        /**
         * Facebook access
         *
        $client = Application_Model_Client::getInstance();
        $client->getClientObject()->setCookieJar(true);
        $client->setBaseUri('https://graph.facebook.com')->sendRequest('/19292868552', 'get');
        if ($client->isSuccessful()) {
            var_dump($client->getResponseBody());
        } else {
            echo $client->getResponseMessage();
        }
        */
    }
}
