<?php

/**
 * Description of index
 *
 * @author developer
 */
class indexController extends Nitro_Controller {

    /**
     * Placeholder.
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Placeholder.
     */
    public function index() {
        $this->load->view("index/index");
    }

    /*
    public function _output($data) {
        echo Output::getInstance()->getRawOutputBuffer();
    }
     */
}
