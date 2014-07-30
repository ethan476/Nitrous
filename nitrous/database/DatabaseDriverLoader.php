<?php

/**
 * Description of Database
 *
 * @author developer
 */
class DatabaseDriverLoader {

    public static function loadDriver($config) {
        if (Loader::includeInterface("BaseDriverInterface", DATABASE_DRIVERS_PATH) === FALSE) {
            throw new DatabaseBaseInterfaceNotFoundException();
        }

        $driver_path = DATABASE_DRIVERS_PATH . $config["db_driver"];
        $class_name = $config["db_driver"] . "_DB";
        if (is_dir($driver_path)) {
            if (Loader::includeClass($config["db_driver"] . "_DB.php", $driver_path . DIRECTORY_SEPARATOR)) {
                if (class_exists($class_name) && class_implements($class_name, "BaseDriverInterface")) {
                    return new $class_name($config);
                } else {
                    throw new DatabaseDriverNotFoundException();
                }
            } else {
                throw new DatabaseDriverNotFoundException();
            }
        } else {
            throw new DatabaseDriverNotFoundException();
        }
    }

}

class DatabaseBaseInterfaceNotFoundException extends Exception {

    /**
     * 
     */
    public function __construct($message = "Failed to load database driver interface.", $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}

class DatabaseDriverNotFoundException extends Exception {

    /**
     * 
     */
    public function __construct($message = "Failed to load database driver shim.", $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
