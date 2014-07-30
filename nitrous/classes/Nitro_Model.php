<?php

/**
 * Description of Nitro_Model
 *
 * @author developer
 */
class Nitro_Model {

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
     * Allows models to access loaded classes
     */
    public function __get($key) {
        $controller = &getInstance();
        return $controller->$key;
    }

}
