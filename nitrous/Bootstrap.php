<?php

require CLASS_PATH . 'Loader.php';

/**
 * Initializer class, for loading base classes and setting run-time mode.
 */
class Bootstrap {

    /**
     * List of default classes to load.
     */
    private static $default_classes = array(
        "Router",
        "Nitro_Controller",
        "Nitro_Model",
        "Output",
        "Config"
    );

    /**
     * Shim for Mode::setMode()
     */
    public static function setMode($mode) {
        Mode::setMode($mode);
    }

    /**
     * Set Paths
     */
    public static function setPaths() {
        define("APP_PATH", BASE_PATH . 'public' . DIRECTORY_SEPARATOR);
        define("VIEWS_PATH", BASE_PATH . 'public' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR);
        define("MODELS_PATH", BASE_PATH . 'public' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR);
        define("CONTROLLERS_PATH", APP_PATH . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR);
        define("CACHE_PATH", APP_PATH . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR);
        define("ERROR_PATH", APP_PATH . DIRECTORY_SEPARATOR . "errors" . DIRECTORY_SEPARATOR);
        define("DATABASE_PATH", NITRO_PATH . 'database' . DIRECTORY_SEPARATOR);
        define("DATABASE_DRIVERS_PATH", DATABASE_PATH . 'drivers' . DIRECTORY_SEPARATOR);
        define("CONFIG_PATH", NITRO_PATH . 'config' . DIRECTORY_SEPARATOR);
        define("LIBRARY_PATH", NITRO_PATH . 'libraries' . DIRECTORY_SEPARATOR);
    }

    /**
     * Load all default classes.
     */
    public static function load() {
        $loader = new Loader();
        $loader->includeClasses(Bootstrap::$default_classes);
        
       $config = new Config();
        
        $output = new Output();
    }

    public static function run() {
        Bootstrap::setPaths();
        Bootstrap::load();
        try {
            Router::route();
        } catch (Exception $e) {
            Router::route(array(
                "__ERROR_404__"
            ));
        }
        Output::getInstance()->display();
    }

}

/**
 * Predefined run-time modes
 */
abstract class Mode {

    /**
     * List of run-time modes.
     */
    const DEVELOPMENT = "DEVELOPMENT";
    const STAGING = "STAGING";
    const PRODUCTION = "PRODUCTION";

    /**
     * Set the run-time mode.
     */
    public static function setMode($mode) {
        switch($mode) {
            case Mode::DEVELOPMENT:
                error_reporting(E_ALL);
                ini_set("display_errors", 1);
                break;
            case Mode::STAGING:
                error_reporting(E_ALL & ~E_NOTICE);
                ini_set("display_errors", 1);
                break;
            default:
                error_reporting(0);
                ini_set("display_errors", 0);
        }
    }
}

/**
 * Return an instance of the currently loaded controller.
 */
function &getInstance() {
    return Nitro_Controller::getInstance();
}

?>
