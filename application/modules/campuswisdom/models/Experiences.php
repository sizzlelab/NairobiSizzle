<?php

class Campuswisdom_Model_Experiences {

    protected $_gossip;
    protected $_ngzones;
    protected $_id;

    public function __construct(array $options = null) {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    public function __set($name, $value) {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid Experiences property');
        }
        $this->$method($value);
    }

    public function __get($name) {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid experiences property');
        }
        return $this->$method();
    }

    public function setOptions(array $options) {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    public function setgossip($text) {
        $this->_gossip = (string) $text;
        return $this;
    }

    public function getgossip() {
        return $this->_gossip;
    }

    public function setngzones($ngzones) {
        $this->_ngzones = (string) $ngzones;
        return $this;
    }

    public function getngzones() {
        return $this->_ngzones;
    }

    /* public function setCreated($ts)
      {
      $this->_created = $ts;
      return $this;
      }
      public function getCreated()
      {
      return $this->_created;
      } */

    public function setId($id) {
        $this->_id = (int) $id;
        return $this;
    }

    public function getId() {
        return $this->_id;
    }

}

