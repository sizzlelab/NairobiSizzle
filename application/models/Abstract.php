<?php
abstract class Application_Model_Abstract {

    public function  __construct(array $data = null) {
        if (is_array($data)) {
            $this->setData($data);
        }
    }

    public function  __set($name,  $value) {
        $method = 'set' . ucfirst($this->encodeToProperty($name));
        if (method_exists($this, $method)) {
            $this->$method($value);
        } else {
            $className = get_class($this);
            throw new Application_Model_Exception("Invalid {$className} property '{$name}' passed");
        }
    }

    public function  __get($name) {
        $method = 'get' . ucfirst($this->encodeToProperty($name));
        if (method_exists($this, $method)) {
            return call_user_func(array($this, $method));
        } else {
            $className = get_class($this);
            throw new Application_Model_Exception("Invalid {$className} property '{$name}' passed");
        }
    }

    public function setData(array $data) {
        foreach ($data as $key => $value) {
            $this->__set($key, $value);
        }
        return $this;
    }

    public function getData($asiStyle = true) {
        $methods = get_class_methods($this);
        $data    = array();
        foreach ($methods as $method) {
            if (substr($method, 0, 3) == 'get' && $method != 'getData') {
                $value = call_user_func(array($this, $method));
                if ($value instanceof Application_Model_Abstract) {
                    $value = call_user_func(array($value, 'getData'), $asiStyle);
                }
                $data[$asiStyle ? $this->decodeFromProperty(substr($method, 3)) : substr($method, 3)] = $value;
            }
        }
        return $data;
    }

    protected function encodeToProperty($detail) {
        //look for underscores, remove them and add capital letters
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $detail)));
    }

    protected function decodeFromProperty($property) {
        //put a space before the capital letters and replace the space with an underscore, make all small
        return str_replace(' ', '_', strtolower(preg_replace('/([^\s])([A-Z])/', '\1 \2', $property)));
    }
}