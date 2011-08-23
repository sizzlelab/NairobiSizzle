<?php
/**
 * Stores pagination data. Usually this will be the pagination data from a GET
 * request on some ASI module. Hence the data will be in response to a request
 * that included pagination parameters.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
class Application_Model_Pagination extends Application_Model_Abstract {
    /**
     * The page number.
     *
     * @var int
     */
    protected $page    = 0;

    /**
     * Records per page.
     * 
     * @var int
     */
    protected $perPage = 0;

    /**
     * Total number of records.
     *
     * @var int
     */
    protected $size    = 0;

    /**
     * Set the page number.
     * 
     * @param int $page
     * 
     * @return Application_Model_Pagination
     */
    public function setPage($page) {
        $this->page = (int) $page;
        return $this;
    }

    /**
     * Get the page number.
     *
     * @see Application_Model_Pagination::setPage()
     *
     * @return int
     */
    public function getPage() {
        return $this->page;
    }

    /**
     * Set the number of records per page.
     *
     * @param int $perPage
     * 
     * @return Application_Model_Pagination
     */
    public function setPerPage($perPage) {
        $this->perPage = (int) $perPage;
        return $this;
    }

    /**
     * Get the number or records per page.
     *
     * @see Application_Model_Pagination::setPerPage()
     * 
     * @return int
     */
    public function getPerPage() {
        return $this->perPage;
    }

    /**
     * Set the total number of records.
     *
     * @param int $size
     * 
     * @return Application_Model_Pagination
     */
    public function setSize($size) {
        $this->size = (int) $size;
        return $this;
    }

    /**
     * Get the total number of records.
     *
     * @see Application_Model_Pagination::setSize()
     *
     * @return int
     */
    public function getSize() {
        return $this->size;
    }
}