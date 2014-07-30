<?php

/**
 * Description of BaseDriverInterface
 *
 * @author developer
 */
interface BaseDriverInterface {
    
    public function query($query, $vars = array());
    
    public function fetch();
    
    public function fetchAll();
}
