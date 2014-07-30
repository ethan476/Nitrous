<?php

/**
 * A test model
 */
class testModel extends Nitro_Model {
    
    /**
     * Placeholder.
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Retrive a name from the $_GET array.
     */
    public function getName() {
        if (isset($_GET["name"])) {
            return $_GET["name"];
        } else {
            return "Someone";
        }
    }
    
    /**
     * Placeholder.
     */
    public function __destruct() {
        parent::__destruct();
    }
}

?>