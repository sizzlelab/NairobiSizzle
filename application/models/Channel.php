<?php

/**
 * This is a model for the asi channel
 * @author Simon Ndunda
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 *
 * 
 */
class Application_Model_Channel extends Application_Model_Abstract {

    protected $channel_type = '';
    protected $name = '';
    protected $created_at = '';
    protected $updated_at = '';
    protected $id = '';
    protected $owner_id = '';
    protected $message_count = 0;
    protected $owner_name = '';
    protected $updated_by = null;
    protected $description = '';
    protected $hidden = null;

    /**
     * set channel type
     * @param $channel_type
     * @return Application_Model_Channel
     */
    public function setChannelType($channel_type) {
        $this->channel_type = (string) $channel_type;
        return $this;
    }

    /**
     * get channel type
     * @return String
     */

    public function getChannelType() {
        return $this->channel_type;
    }

    public function setName($name) {
        $this->name = (string) $name;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = (string) $created_at;
        return $this;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setUpdatedAt($updated_at) {
        $this->updated_at = (string) $updated_at;
        return $this;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }

    public function setId($id) {
        $this->id = (string) $id;
        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function setOwnerId($owner_id) {
        $this->owner_id = (string) $owner_id;
        return $this;
    }

    public function getOwnerId() {
        return $this->owner_id;
    }

    public function setMessageCount($message_count) {
        $this->message_count = (string) $message_count;
        return $this;
    }

    public function getMessageCount() {
        return $this->message_count;
    }

    public function setOwnerName($owner_name) {
        $this->owner_name = (string) $owner_name;
        return $this;
    }

    public function getOwnerName() {
        return $this->owner_name;
    }

    public function setUpdatedBy(array $updated_by=null) {
        $this->updated_by = new Application_Model_Channel_UpdatedBy($updated_by);
        return $this;
    }

    public function getUpdatedBy() {
        return $this->updated_by;
    }

    public function setDescription($description) {
        $this->description = (string) $description;
        return $this;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setHidden($hidden) {
        $this->hidden = (string) $hidden;
        return $this;
    }

    public function getHidden() {
        return $this->hidden;
    }

}

