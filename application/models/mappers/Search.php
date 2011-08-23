<?php
/**
 * Handles requests to /search module of the ASI platform.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_Search extends Application_Model_Mapper_Search_Abstract {
    /**
     * Performs a search within people, groups, channels and messages in the ASI platfrom.
     * Sends a GET request to /search with the GET parameter 'search'.
     *
     * The search term must be specified before calling this method using
     * {@link Application_Model_Mapper_Search_Abstract::setSearchTerm()} or this
     * method will throw an exception.
     *
     * The records to be returned can be customized by calling
     * {@link Application_Model_Mapper_Search_Abstract::setPage()} to set the page number to fetch,
     * {@link Application_Model_Mapper_Search_Abstract::setPerPage()} to set the number of records
     * to view per page, {@link Application_Model_Mapper_Search_Abstract::setSortBy()} to set the
     * criteria to use in sorting records and {@link Application_Model_Mapper_Search_Abstract::setSortOrder()}
     * to set the order to use in sorting records before calling this method.
     *
     * If any of these customizations are used, ASI returns pagination data which can
     * be accessed using {@link Application_Model_Mapper_Abstract::getPagination()}.
     *
     * @return array Of {@link Application_Model_Person}s, {@link Application_Model_Group}s
     * and {@link Application_Model_Channel}s with thier data set.
     *
     * @todo Handle search within messages!
     *
     * @throws Application_Model_Mapper_Search_Exception If:
     *      - A search term was not specified.
     *      - The request was not successful.
     *      - The request was successful but for some unknown reason data was not received.
     */
    public function fetch() {
        $searchTerm   = $this->getSearchTerm();
        if (!$searchTerm) {
            throw new Application_Model_Mapper_Search_Exception('Set a search term: Application_Model_Search_Mapper::setSearchTerm()');
        }
        $page         = $this->getPage();
        $perPage      = $this->getPerPage();
        $queryString  = "?search={$searchTerm}";
        $queryString .= $page ? "&page={$page}" : '';
        $queryString .= $perPage ? "&per_page={$perPage}" : '';
        $client       = $this->getClient();
        if ($client->sendRequest("/search{$queryString}", 'get')->isSuccessful()) {
            $response = $client->getResponseBody();
            if (isset($response['pagination'])) {
                $this->setPagination(new Application_Model_Pagination($response['pagination']));
            }
            if (!isset($response['entry'])) {
                throw new Application_Model_Mapper_Search_Exception('Unexpected error: data not received');
            }
            $data  = array();
            foreach ($response['entry'] as $result) {
                $class  = "Application_Model_" . ucfirst($result['type']);
                $data[] = new $class($result['result']);
            }
            return $data;
        } else {
            throw new Application_Model_Mapper_Search_Exception("Error searching for '{$searchTerm}': " . $client->getResponseMessage(), $client->getResponseCode());
        }
    }

    /**
     * @throws Application_Model_Mapper_Search_Exception Unsupported method.
     */
    public function update() {
        throw new Application_Model_Mapper_Search_Exception('Unsupported method Application_Model_Mapper_Search::update()');
    }

    /**
     * @throws Application_Model_Mapper_Search_Exception Unsupported method.
     */
    public function create() {
        throw new Application_Model_Mapper_Search_Exception('Unsupported method Application_Model_Mapper_Search::create()');
    }

    /**
     * @throws Application_Model_Mapper_Search_Exception Unsupported method.
     */
    public function delete() {
        throw new Application_Model_Mapper_Search_Exception('Unsupported method Application_Model_Mapper_Search::delete()');
    }
}