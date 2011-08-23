<?php
require_once 'Zend/Application.php';

class Sizzle_Application extends Zend_Application {
    protected $_cacheConfig = false;
    protected $_cacheOptions = array(
        'frontendType'    => 'File',
        'backendType'     => 'File',
        'frontendOptions' => array(),
        'backendOptions'  => array()
    );

    public function  __construct($environment, $options = null) {
        if (is_array($options) && isset($options['cacheConfig'])) {
            $this->_cacheConfig = true;
        }
        if (isset($options['cacheOptions'])) {
            $this->_cacheOptions = array_merge($this->_cacheOptions, $options['cacheOptions']);
        }
        $options = $options['configFile'];
        parent::__construct($environment, $options);
    }

    protected function _loadConfig($file) {
        if (!$this->_cacheConfig) {
            return parent::_loadConfig($file);
        }
        require_once 'Zend/Cache.php';
        $cache = Zend_Cache::factory(
                $this->_cacheOptions['frontendType'],
                $this->_cacheOptions['backendType'],
                array_merge(array(
                    'master_file' => $file,
                    'automatic_serialization' => true
                ), $this->_cacheOptions['frontendOptions']),
                array_merge(array(
                    'cache_dir' => APPLICATION_PATH . '/../data/cache'
                ), $this->_cacheOptions['backendOptions'])
            );
        $config = $cache->load('Zend_Application_Config');
        if (!$config) {
            $config = parent::_loadConfig($file);
            $cache->save($config, 'Zend_Application_Config');
        }
        return $config;
    }
}
