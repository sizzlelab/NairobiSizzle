<?php

/**
 * This is a model of asi/channel/message
 * @author Simon Ndunda
 * @copyright 2010, Nairobi Sizzle
 * @category NairobiSizzle
 * @package Core
 * @subpackage Models
 */

class Application_Model_Channel_Message extends Application_Model_Abstract {

    protected $title = '';
    protected $createdAt = '';
    protected $updatedAt = '';
    protected $referenceTo = '';
    protected $body = '';
    protected $posterId = '';
    protected $replies = '';
    protected $attachment = '';
    protected $contentType = '';
    protected $id = '';
    protected $posterName = '';
    protected $channel = '';

    public function setChannel($channel) {
        $this->channel = $channel;
    }

    public function getChannel() {
        return $this->channel;
    }

    public function setPosterName($posterName) {
        $this->posterName = $posterName;
    }

    public function getPosterName() {
        return $this->posterName;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setContentType($content_type) {
        $this->contentType = $content_type;
    }

    public function getContentType() {
        return $this->contentType;
    }

    public function setAttachment($attachment) {
        $this->attachment = $attachment;
    }

    public function getAttachment() {
        return $this->attachment;
    }

    public function setReplies($replies) {
        $this->replies = $replies;
    }

    public function getReplies() {
        return $this->replies;
    }

    public function setPosterId($poster_id) {
        $this->poster_id = $poster_id;
    }

    public function getPosterId() {
        return $this->poster_id;
    }

    public function setBody($body) {
        $this->body = $body;
    }

    public function getBody() {
        return $this->body;
    }

    public function setReferenceTo($reference_to) {
        $this->referenceTo = $reference_to;
    }

    public function getReferenceTo() {
        return $this->referenceTo;
    }

    public function setUpdatedAt($created_at) {
        $this->updatedAt = $created_at;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setCreatedAt($created_at) {
        $this->createdAt = $created_at;
    }

    public function getCreatedAt() {
        return $this->updatedAt;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getTitle() {
        return $this->title;
    }

}

?>
