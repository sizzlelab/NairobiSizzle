<?php
/**
 * Base mapper class for all mappers that need search functionality e.g.
 * {@link Application_Model_Mapper_People}, {@link Application_Model_Mapper_Search},
 * {@link Application_Model_Mapper_Person_Friends} etc.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 *
 * @uses Application_Model_Mapper_Abstract
 */
abstract class Application_Model_Mapper_Search_Abstract extends Application_Model_Mapper_Abstract {
    /**
     * The options for sorting search results supported by the ASI platform.
     *
     * This is used for validation of 'GET' requests with pagination parameters to
     * avoid making unnecessary requests if the parameter provided with the request
     * is not supported by the ASI platform anyways.
     *
     * @var array
     */
    protected $sortByOptions  = array(
        'status_changed'
    );

    /**
     * The options for ordering search results supported by the ASI platform.
     *
     * This is used for validation of 'GET' requests with pagination parameters to
     * avoid making unnecessary requests if the parameter provided with the request
     * is not supported by the ASI platform anyways.
     *
     * @var array
     */
    protected $sortOrderOptions = array(
        'ascending',
        'descending'
    );

    /**
     * The search term.
     * 
     * @var string
     */
    protected $searchTerm = '';

    /**
     * The page to start displaying records from.
     * 
     * @var int
     */
    protected $page = 0;

    /**
     * The number of records to return per page.
     *
     * @var int
     */
    protected $perPage = 0;

    /**
     * The 'sortBy' option to use.
     * 
     * @var string
     */
    protected $sortBy = '';

    /**
     * The 'sortOrder' option to use.
     *
     * @var string
     */
    protected $sortOrder = '';

    /**
     * Set the search term.
     * 
     * @param string $searchTerm
     *
     * @return Application_Model_Mapper_Search_Abstract
     */
    public function setSearchTerm($searchTerm) {
        $this->searchTerm = (string) $searchTerm;
        return $this;
    }

    /**
     * Get the search term.
     *
     * @see Application_Model_Mapper_Search_Abstract::setSearchTerm()
     * 
     * @return string
     */
    public function getSearchTerm() {
        return $this->searchTerm;
    }

    /**
     * Set the page number to fetch records from.
     *
     * @param int $page
     *
     * @return Application_Model_Mapper_Search_Abstract
     */
    public function setPage($page) {
        $this->page = (int) $page;
        return $this;
    }

    /**
     * Get the page.
     *
     * @see Application_Model_Mapper_Search_Abstract::setPage()
     *
     * @return int
     */
    public function getPage() {
        return $this->page;
    }

    /**
     * Set the number or records to fetch per page.
     *
     * @param int $perPage
     *
     * @return Application_Model_Mapper_Search_Abstract
     */
    public function setPerPage($perPage) {
        $this->perPage = (int) $perPage;
        return $this;
    }

    /**
     * Get the number or records to fetch per page.
     *
     * @see Application_Model_Mapper_Search_Abstract::setPerPage()
     *
     * @return int
     */
    public function getPerPage() {
        return $this->perPage;
    }

    /**
     * Set the sort by option.
     *
     * @param string $sortBy
     *
     * @return Application_Model_Mapper_Search_Abstract
     * 
     * @throws Application_Model_Mapper_Search_Exception If the provided sort by
     * option is not supported by ASI.
     */
    public function setSortBy($sortBy) {
        if (!in_array($sortBy, $this->sortByOptions)) {
            throw new Application_Model_Mapper_Search_Exception("Unsupported sortBy option: {$sortBy}");
        }
        $this->sortBy = (string) $sortBy;
        return $this;
    }

    /**
     * Get the sort by option.
     *
     * @see Application_Model_Mapper_Search_Abstract::setSortBy()
     *
     * @return string
     */
    public function getSortBy() {
        return $this->sortBy;
    }

    /**
     * Set the sort order option.
     *
     * @param string $sortOrder
     *
     * @return Application_Model_Mapper_Search_Abstract
     *
     * @throws Application_Model_Mapper_Search_Exception If the provided sort order
     * option is not supported by ASI.
     */
    public function setSortOrder($sortOrder) {
        if (!in_array($sortOrder, $this->sortOrderOptions)) {
            throw new Application_Model_Mapper_Search_Exception("Unsupported sortOrder option: {$sortOrder}");
        }
        $this->sortOrder = (string) $sortOrder;
        return $this;
    }

    /**
     * Get the sort order option.
     *
     * @see Application_Model_Mapper_Search_Abstract::setSortOrder()
     *
     * @return string
     */
    public function getSortOrder() {
        return $this->sortOrder;
    }
}