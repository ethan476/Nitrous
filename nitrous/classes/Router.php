<?php

/**
 * Turn a URI into a class->method(params)
 */
class Router {

    /**
     * 
     */
    private static $reserved_functions = array(
        "_output"
    );

    /**
     * Default routing table.
     */
    private static $routing_table = array();

    /**
     * Placeholder.
     */
    public function __construct() {
        
    }

    /**
     * Placeholder.
     */
    public function __destruct() {
        
    }

    /**
     * Load the routing table.
     */
    public static function load_routing_table() {
        $file = CONFIG_PATH . "Routes.php";

        if (file_exists($file)) {
            require $file;
            if (!isset($routes)) {
                $routes = array();
            }
        } else {
            $routes = array();
        }

        return $routes;
    }

    /**
     * Merge two routing tables.
     */
    public static function set_routing_table($table) {
        Router::$routing_table = array_merge(Router::$routing_table, $table);
    }

    /**
     * Turn URI into a callable object, and execute it.
     */
    public static function route($override = FALSE) {
        Router::$routing_table = array_merge(Router::$routing_table, Router::load_routing_table());

        if ($override == FALSE) {
            $path = Router::find_request_path();
        } else {
            $path = $override;
        }

        if (strpos($path[0], "__") === 0) {
            if (empty(Router::$routing_table)) {
                throw new NoRouteFoundException();
            } else {
                if ($path[0] == "__DEFAULT_PAGE__") {
                    if (isset(Router::$routing_table["__DEFAULT_PAGE__"])) {
                        return Router::route(array(
                                    Router::$routing_table["__DEFAULT_PAGE__"]
                        ));
                    } else {
                        throw new NoRouteFoundException();
                    }
                } else if (strpos($path[0], "__ERROR_") == 0) {
                    $path[0] = intval(substr($path[0], strlen("__ERROR_"), strlen($path[0]) - strlen("__ERROR_") - 2));
                    $path["path"] = ERROR_PATH;
                    if (file_exists($path["path"] . $path[0] . ".php")) {
                        Router::load_error($path);
                    } else {
                        echo "A 404 error has occurred.";
                        http_response_code(404);
                        exit(1);
                    }
                }
            }
        } else {
            $path["path"] = CONTROLLERS_PATH;
            return Router::load_and_run_controller($path);
        }
    }

    /**
     * Load and run a view
     */
    private static function load_error($path) {
        $error_code = $path[0];
        unset($path["path"]);
        unset($path[0]);
        Loader::error($error_code, array_values($path));
    }

    /**
     * Load and run a controller.
     */
    private static function load_and_run_controller($path) {
        if (file_exists($path["path"] . $path[0])) {
            require_once $path["path"] . $path[0];

            /** Add Controller and strip .php extension */
            $path[0] = str_replace(".php", "", $path[0]) . "Controller";
            if (class_exists($path[0])) {
                $class = new $path[0]();
                $method = isset($path[1]) ? $path[1] : "index";

                /** Is this a REAL controller? */
                if (is_subclass_of($class, "Nitro_Controller")) {

                    if (method_exists($class, $method) && !in_array($method, self::$reserved_functions)) {
                        call_user_func_array(array(
                            $class,
                            $method
                                ), array_slice($path, 2, -1));
                    } else {
                        throw new InvalidMethodException();
                    }
                } else {
                    throw new InvalidControllerException();
                }
            } else {
                throw new NoControllerException();
            }
        } else {
            throw new NoControllerException();
        }
    }

    /**
     * Find the requested class->method(params) from a URI.
     */
    private static function find_request_path() {
        $path = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
        $uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

        foreach ($path as $key => $val) {
            if (isset($uri[$key])) {
                if ($val == $uri[$key]) {
                    unset($uri[$key]);
                } else {
                    break;
                }
            } else {
                break;
            }
        }

        if (empty($uri[0])) {
            $uri[0] = '__DEFAULT_PAGE__';
        } else {
            //$uri[0] = str_replace(".php", "", $uri[0]);
            if (($pos = strpos($uri[0], "?")) !== FALSE) {
                $uri[0] = substr($uri[0], 0, $pos);
            }
            $uri[0] .= ".php";
        }
        return $uri;
    }

}

/**
 * No default routing table, therefore __DEFAULT_PAGE__ and __ERROR_[code]__ cannot be loaded
 */
class NoRouteFoundException extends Exception {

    /**
     * Implementation
     */
    public function __construct($message = "No routing path found.", $code = 0, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }

}

/**
 * No controller has been found.
 */
class NoControllerException extends Exception {

    /**
     * Implementation.
     */
    public function __construct($message = "No controller found.", $code = 0, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }

}

/**
 * Method not found in controller.
 */
class InvalidMethodException extends Exception {

    /**
     * Implementation.
     */
    public function __construct($message = "Method not found in controller", $code = 0, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }

}

/**
 * The controller is is invalid.
 */
class InvalidControllerException extends Exception {

    /**
     * Implementation.
     */
    public function __construct($message = "Invalid controller.", $code = 0, $previous = NULL) {
        parent::__construct($message, $code, $previous);
    }

}

?>