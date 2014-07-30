<?php

/**
 * Description of Nitro_Controller
 *
 * @author developer
 */
class Nitro_Controller {

    /**
     * Variable that holds a reference to the currently loaded instance.
     */
    private static $instance;

    /**
     * Set instance, and create default controller objects.
     */
    public function __construct() {
        self::$instance = &$this;
        $this->load     = &Loader::getInstance();
        $this->output   = &Output::getInstance();
    }

    /**
     * Placeholder.
     */
    public function __destruct() {
        
    }

    /**
     * Return a reference to the currently loaded controller.
     */
    public static function &getInstance() {
        return self::$instance;
    }

}
