<?php

/**
 * Description of Config
 *
 * @author developer
 */
class Config {

    private static $instance;
    private static $config = array();
    private static $initialized = FALSE;
    private static $default_config_files = array(
    );

    public function __construct() {
        self::$instance = &$this;
        $this->init();
    }

    public static function init() {
        if (self::$initialized === FALSE) {
            self::$default_config_files = array(
                CONFIG_PATH . "Config.ini"
            );

            foreach (self::$default_config_files as $file) {
                self::loadConfigurationFile($file);
            }
            self::$initialized = TRUE;
        }
    }
    
    public static function get($name) {
        return self::getInstance()->__get($name);
    }

    public function __get($name) {
        if (isset(self::$config[$name])) {
            return self::$config[$name];
        } else {
            return NULL;
        }
    }

    public static function loadConfigurationFile($file) {
        if (file_exists($file)) {
            self::$config = array_merge(self::$config, parse_ini_file($file, TRUE));
        }
    }
    public static function &getInstance() {
        return self::$instance;
    }
}
