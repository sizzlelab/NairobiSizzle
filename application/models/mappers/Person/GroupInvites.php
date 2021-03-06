<?php
/**
 * Handles requests to /people/<user id>/@groups/@invites module of the ASI platform.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_Person_GroupInvites extends Application_Model_Mapper_Person_Abstract {
    /**
     * Fetches all of a person's group invites from ASI. Sends a GET request to /people/<user id>/@groups/@invites.
     *
     * The records to be returned can be customized by calling
     * {@link Application_Model_Mapper_Search_Abstract::setPage()} to set the page number to fetch,
     * {@link Application_Model_Mapper_Search_Abstract::setPerPage()} to set the number of records
     * to view per page and {@link Application_Model_Mapper_Search_Abstract::setSortOrder()}
     * to set the order to use in sorting records before calling this method.
     *
     * If any of these customizations are used, ASI returns pagination data which can
     * be accessed using {@link Application_Model_Mapper_Abstract::getPagination()}.
     *
     * @param string $id The person's ID. If not provided, this method will call
     * {@link Application_Model_Mapper_Person_Abstract::getPerson()} and then
     * {@link Application_Model_Person::getId()} to get the person's ID.
     *
     * @return array Of {@link Application_Model_Group}s with thier data set.
     *
     * @throws Application_Model_Mapper_Person_GroupInvites_Exception If:
     *      - ID has not been provided (and could not be obtained from a
     *          {@link Application_Model_Person} object)
     *      - The request was not successful. In this case if any error messages
     *          were returned by ASI, they will be available using
     *          {@link Application_Model_Mapper_Abstract::getErrors()}.
     *      - The reques was successful but for some unknown reason data was not
     *          received.
     */
    public function fetch($id = null) {
        $id = $id ? (string) $id : $this->getPerson()->getId();
        if ($id) {
            $client      = $this->getClient();
            $page        = $this->getPage();
            $perPage     = $this->getPerPage();
            $sortOrder   = $this->getSortOrder();
            $queryString = '';
            if ($page) {
                $queryString = $queryString ? $queryString . "&page={$page}" : "?page={$page}";
            }
            if ($perPage) {
                $queryString = $queryString ? $queryString . "&per_page={$perPage}" : "?per_page={$perPage}";
            }
            if ($sortOrder) {
                $queryString = $queryString ? $queryString . "&sortOrder={$sortOrder}" : "?sortOrder={$sortOrder}";
            }
            if ($client->sendRequest("/people/{$id}/@groups/@invites{$queryString}", 'get')->isSuccessful()) {
                $response = $client->getResponseBody();
                if (isset($data['pagination'])) {
                    $this->setPagination(new Application_Model_Pagination($data['pagination']));
                }
                if (!isset($response['entry'])) {
                    throw new Application_Model_Mapper_Person_GroupInvites_Exception('Unexpected error: data not received');
                }
                $groupInvites = array();
                foreach ($response['entry'] as $group) {
                    $groupInvites[] = new Application_Model_Group($group);
                }
                return $groupInvites;
            } else {
                $response = $client->getResponseBody();
                if (isset($reponse['messages'])) {
                    $this->setErrors($reponse['messages']);
                }
                throw new Application_Model_Mapper_Person_GroupInvites_Exception("Error fetching groups: " . $client->getResponseMessage(), $client->getResponseCode());
            }
        } else {
            throw new Application_Model_Mapper_Person_GroupInvites_Exception('Person ID has not been set');
        }
    }

    /**
     * @throws Application_Model_Mapper_Person_GroupInvites_Exception Unsupported method.
     */
    public function create() {
        throw new Application_Model_Mapper_Person_GroupInvites_Exception('Unsupported method Application_Model_Mapper_Person_GroupInvites::create()');
    }

    /**
     * @throws Application_Model_Mapper_Person_GroupInvites_Exception Unsupported method.
     */
    public function update() {
        throw new Application_Model_Mapper_Person_GroupInvites_Exception('Unsupported method Application_Model_Mapper_Person_GroupInvites::update()');
    }

    /**
     * @throws AApplication_Model_Mapper_Person_GroupInvites_Exception Unsupported method.
     */
    public function delete() {
        throw new Application_Model_Mapper_Person_GroupInvites_Exception('Unsupported method Application_Model_Mapper_Person_GroupInvites::delete()');
    }
}