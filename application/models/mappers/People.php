<?php
/**
 * Handles requests to the /people (top-level) module of the ASI platform.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Mapper_People extends Application_Model_Mapper_Search_Abstract {

    /**
     * Fetches records of people from the ASI platform. Sends a GET request to /people.
     *
     * The records to be returned can be customized by calling
     * {@link Application_Model_Mapper_Search_Abstract::setSearchTerm()} to set the search term,
     * {@link Application_Model_Mapper_Search_Abstract::setPage()} to set the page number to fetch,
     * {@link Application_Model_Mapper_Search_Abstract::setPerPage()} to set the number of records
     * to view per page, {@link Application_Model_Mapper_Search_Abstract::setSortBy()} to set the
     * criteria to use in sorting records and {@link Application_Model_Mapper_Search_Abstract::setSortOrder()}
     * to set the order to use in sorting records before calling this method.
     *
     * If any of these customizations are used, ASI returns pagination data which can
     * be accessed using {@link Application_Model_Mapper_Abstract::getPagination()}.
     *
     * Note that just like {@link Application_Model_Mapper_People::search()} this
     * method can also be used to search, but unlike {@link Application_Model_Mapper_People::search()},
     * it will not force you to set a search term.
     * 
     * @return array Of {@link Application_Model_Person}s with thier data set.
     *
     * @throws Application_Model_Mapper_People_Exception If:
     *      - The request was not successful. In this case if any error messages were returned
     *          by ASI, they will be available using {@link Application_Model_Mapper_Abstract::getErrors()}.
     *      - The request was successful but for some unknown reason data was not received.
     */
    public function fetch() {
        $client      = $this->getClient();
        $search      = $this->getSearchTerm();
        $page        = $this->getPage();
        $perPage     = $this->getPerPage();
        $sortBy      = $this->getSortBy();
        $sortOrder   = $this->getSortOrder();
        $queryString = '';
        if ($search) {
            $queryString = $queryString ? $queryString . "&search={$search}" : "?search={$search}";
        }
        if ($page) {
            $queryString = $queryString ? $queryString . "&page={$page}" : "?page={$page}";
        }
        if ($perPage) {
            $queryString = $queryString ? $queryString . "&per_page={$perPage}" : "?per_page={$perPage}";
        }
        if ($sortBy) {
            $queryString = $queryString ? $queryString . "&sortBy={$sortBy}" : "?sortBy={$sortBy}";
        }
        if ($sortOrder) {
            $queryString = $queryString ? $queryString . "&sortOrder={$sortOrder}" : "?sortOrder={$sortOrder}";
        }
        if ($client->sendRequest("/people{$queryString}", 'get')->isSuccessful()) {
            $data = $client->getResponseBody();
            if (!isset($data['entry'])) {
                throw new Application_Model_Mapper_People_Exception('Unexpected error: data not received');
            }
            $people = array();
            if (isset($data['pagination'])) {
                $this->setPagination(new Application_Model_Pagination($data['pagination']));
            }
            foreach ($data['entry'] as $person) {
                $people[] = new Application_Model_Person($person);
            }
            return $people;
        } else {
            $response = $client->getResponseBody();
            if (isset($response['messages'])) {
                $this->setErrors($response['messages']);
            }
            throw new Application_Model_Mapper_People_Exception('Error fetching people: ' . $client->getResponseMessage(), $client->getResponseCode());
        }
    }

    /**
     * Performs a search within people in the ASI platfrom. Sends a GET request
     * to /people with the GET parameter 'search'.
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
     * @return array Of {@link Application_Model_Person}s with thier data set.
     *
     * @throws Application_Model_Mapper_People_Exception If:
     *      - A search term was not specified.
     *      - The request was not successful.
     *      - The request was successful but for some unknown reason data was not received.
     */
    public function search() {
        $searchTerm   = $this->getSearchTerm();
        if (!$searchTerm) {
            throw new Application_Model_Mapper_People_Exception('Set a search term(s): Application_Model_People_Mapper::setSearchTerm()');
        }
        $page         = $this->getPage();
        $perPage      = $this->getPerPage();
        $sortBy       = $this->getSortBy();
        $sortOrder    = $this->getSortOrder();
        $queryString  = '?search=' . $searchTerm;
        $queryString .= $page ? '&page=' . $page : '';
        $queryString .= $perPage ? '&per_page=' . $perPage : '';
        $queryString .= $sortBy ? '&sortBy=' . $sortBy : '';
        $queryString .= $sortOrder ? '&sortOrder=' . $sortOrder : '';
        $client       = $this->getClient();
        if ($client->sendRequest("/people/{$queryString}", 'get')->isSuccessful()) {
            $response = $client->getResponseBody();
            if (isset($response['pagination'])) {
                $this->setPagination(new Application_Model_Pagination($response['pagination']));
            }
            if (!isset($response['entry'])) {
                throw new Application_Model_Mapper_People_Exception('Unexpected error: data not received');
            }
            $people = array();
            foreach ($response['entry'] as $person) {
                $people[] = new Application_Model_Person($person);
            }
            return $people;
        } else {
            $response = $client->getResponseBody();
            if (isset($response['messages'])) {
                $this->setErrors($response['messages']);
            }
            throw new Application_Model_Mapper_People_Exception("Error searching people for '{$searchTerm}': " . $client->getResponseMessage(), $client->getResponseCode());
        }
    }

    /**
     * @throws Application_Model_Mapper_People_Exception Unsupported method.
     */
    public function update() {
        throw new Application_Model_Mapper_People_Exception('Unsupported method Application_Model_Mapper_People::update()');
    }

    /**
     * @throws Application_Model_Mapper_People_Exception Unsupported method.
     */
    public function create() {
        throw new Application_Model_Mapper_People_Exception('Unsupported method Application_Model_Mapper_People::create()');
    }

    /**
     * @throws Application_Model_Mapper_People_Exception Unsupported method.
     */
    public function delete() {
        throw new Application_Model_Mapper_People_Exception('Unsupported method Application_Model_Mapper_People::delete()');
    }
}