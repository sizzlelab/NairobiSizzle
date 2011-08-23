<?php
/**
 * Interface defining methods that must be in all mapper classes.
 *
 * @author Joel Mukuthu <joelmukuthu@gmail.com>
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */
interface Application_Model_Mapper_Interface {
    /**
     * Create a record on the ASI platform under some module e.g. '/people', '/groups'.
     *
     * This method should implement a POST HTTP request on the platform.
     */
    public function create();

    /**
     * Fetch a record(s) from the ASI platform under some module e.g. '/people', '/groups'
     *
     * This method should implement a GET HTTP request on the platform.
     */
    public function fetch();

    /**
     * Update a record on the ASI platform under some module e.g. '/people', '/groups'
     *
     * This method should implement a PUT HTTP request on the platform.
     */
    public function update();
    
    /**
     * Delete a record on the ASI platform under some module e.g. '/people', '/groups'
     *
     * This method should implement a DELETE HTTP request on the platform.
     */
    public function delete();
}