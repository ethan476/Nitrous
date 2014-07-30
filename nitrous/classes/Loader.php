<?php

/**
 * 
 */
class Loader {

    /**
     * 
     */
    private static $interfaces = array();
    
    /**
     * 
     */
    private static $files = array();
    
    /**
     * 
     */
    private static $classes = array();

    /**
     * 
     */
    private static $libraries = array();

    /**
     * 
     */
    private static $vars = array();

    /**
     * 
     */
    private static $instance;

    /**
     * 
     */
    private static $ob_level;

    /**
     * 
     */
    public function __construct() {
        self::$instance = &$this;
        self::$ob_level = ob_get_level();
    }

    /**
     * 
     */
    public function __destruct() {
        
    }

    /**
     * 
     */
    public static function error($error_code, $data = array()) {
        http_response_code($error_code);
        Loader::__view(ERROR_PATH . $error_code . ".php", $data);
    }

    /**
     * 
     */
    public static function database($database = "default", $return = FALSE, $overwrite = TRUE) {
        if (file_exists(CONFIG_PATH . "Database.php") && self::includeClass("DatabaseDriverLoader", DATABASE_PATH)) { 
            require_once CONFIG_PATH . "Database.php";

            if (isset($db)) {
                if (isset($db[$database])) {

                    $tmp_db = DatabaseDriverLoader::loadDriver($db[$database]);
                    
                    /* add overwrite check */ 
                    getInstance()->database = &$tmp_db;
                    
                    if ($return == TRUE) {
                        return $tmp_db;
                    } else {
                        return TRUE;
                    }
                } else {
                    throw new DatabaseNotFoundException();
                }
            } else {
                throw new DatabaseNotFoundException();
            }
        } else {
            throw new DatabaseNotFoundException();
        }
    }

    /**
     * 
     */
    public static function view($view, $data = array(), $return = FALSE, $path_override = FALSE) {
        if ($path_override === FALSE) {
            foreach (Paths::getViewPaths() as $path) {
                $out = Loader::__view($path . $view . ".php", $data, $return);
                if ($return === TRUE) {
                    return $out;
                } else {
                    return TRUE;
                }
            }
        } else {
            $out = Loader::__view($path_override . $view . ".php", $data, $return);
            if ($return === TRUE) {
                return $out;
            } else {
                return TRUE;
            }
        }
        throw new ViewNotFoundException();
    }

    /**
     * 
     */
    private static function __view($path, $data = array(), $return = FALSE) {
        if (file_exists($path)) {
            Loader::$vars = array_merge($data, Loader::$vars);
            extract(Loader::$vars);

            ob_start();

            if ((bool) @ini_get('short_open_tag') === FALSE) {
                echo eval('?>' . preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', file_get_contents($path))));
            } else {
                include_once $path;
            }

            if ($return === TRUE) {
                $buffer = ob_get_contents();
                @ob_end_clean();
                return $buffer;
            }

            if (ob_get_level() > self::$ob_level + 1) {
                ob_end_flush();
            } else {
                Output::getInstance()->append(ob_get_contents());
                @ob_end_clean();
            }

            return TRUE;
        }
        throw new ViewNotFoundException();
    }

    /**
     * 
     */
    public static function model($model, $name = "", $overwrite = FALSE) {
        foreach (Paths::getModelPaths() as $path) {
            if (Loader::includeClass($path . $model, FALSE)) {
                if (!in_array($model, Loader::$libraries)) {
                    if ($name == "") {
                        $split_name = explode("/", $model);
                        $name = end($split_name);
                    }
                    if (!isset(getInstance()->{$name}) || $overwrite = TRUE) {
                        getInstance()->{$name} = new $name();
                        return getInstance()->{$name};
                    } else {
                        throw new ObjectNameTakenException();
                    }
                } else {
                    if ($overwrite === TRUE) {
                        getInstance()->{$name} = new $name();
                    }
                    return getInstance()->{$name};
                }
            }
        }
        throw new ModelNotFoundException();
    }

    /**
     * 
     */
    public static function library($library, $config = array(), $name = "", $overwrite = FALSE) {
        foreach (Paths::getLibraryPaths() as $path) {
            if (Loader::includeClass($path . $library, FALSE)) {
                if (!in_array($library, Loader::$libraries)) {
                    if ($name == "") {
                        $name = end(explode("/", $library));
                    }
                    if (!isset(getInstance()->{$name}) || $overwrite = TRUE) {
                        getInstance()->{$name} = new $name($config);
                        return getInstance()->{$name};
                    } else {
                        throw new ObjectNameTakenException();
                    }
                } else {
                    if ($overwrite === TRUE) {
                        getInstance()->{$name} = new $name($config);
                    }
                    return getInstance()->{$name};
                }
            }
        }
        throw new LibraryNotFoundException();
    }

    /**
     * 
     */
    public static function includeClasses($classes, $use_default_path = TRUE) {
        $returned_data = array();

        foreach ($classes as $class) {
            $returned_data[$class] = Loader::includeClass($class, $use_default_path);
        }
        return $returned_data;
    }

    /**
     * 
     */
    public static function includeFile($file) {
        $file = str_replace(".php", "", $file);
        if (in_array($file, Loader::$files)) {
            return TRUE;
        }
        
        if (file_exists($file . ".php")) {
            self::$files[] = $file;
            require_once $file . ".php";
            return TRUE;
        }
        throw new FileNotFoundException();
    }

    /**
     * 
     * @param type $interface
     * @param type $use_default_path
     * @return boolean
     */
    public static function includeInterface($interface, $use_default_path = TRUE) {
        
        $interface = str_replace(".php", "", $interface);
        if ($use_default_path === TRUE) {
            $path = CLASS_PATH;
        } else {
            if (is_string($use_default_path)) {
                $path = $use_default_path;
            } else {
                $path = "";
            }
        }

        if (self::includeFile($path . $interface . ".php")) {
            $split_path = explode("/", $interface);
            $interface = end($split_path);
            if (interface_exists($interface, TRUE)) {
                Loader::$interfaces[] = $path . $interface;
                return TRUE;
            }
        }
        return FALSE;
    }
    
    /**
     * 
     */
    public static function includeClass($class, $use_default_path = TRUE) {
        $class = str_replace(".php", "", $class);
        if ($use_default_path === TRUE) {
            $path = CLASS_PATH;
        } else {
            if (is_string($use_default_path)) {
                $path = $use_default_path;
            } else {
                $path = "";
            }
        }


        if (self::includeFile($path . $class . ".php")) {
            $split_path = explode("/", $class);
            $class = end($split_path);
            if (class_exists($class)) {
                Loader::$classes[] = $path . $class;
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * 
     */
    public static function &getInstance() {
        return self::$instance;
    }

}

/**
 * 
 */
class FileNotFoundException extends Exception {

    /**
     * 
     */
    public function __construct($message = "Failed to load file.", $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}

/**
 * 
 */
class ModelNotFoundException extends Exception {

    /**
     * 
     */
    public function __construct($message = "Failed to load model.", $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}

/**
 * 
 */
class DatabaseNotFoundException extends Exception {

    /**
     * 
     */
    public function __construct($message = "Failed to load database.", $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}

/**
 * 
 */
class ViewNotFoundException extends Exception {

    /**
     * 
     */
    public function __construct($message = "Failed to load view.", $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}

/**
 * 
 */
class ClassNotFoundException extends Exception {

    /**
     * 
     */
    public function __construct($message = "Failed to load class.", $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}

/**
 * 
 */
class ObjectNameTakenException extends Exception {

    /**
     * 
     */
    public function __construct($message = "The libraries variable name is taken, and overwriting is OFF.", $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}

/**
 * 
 */
class LibraryNotFoundException extends Exception {

    /**
     * 
     */
    public function __construct($message = "Failed to load library.", $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}

?>
