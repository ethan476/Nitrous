<?php

/**
 * Description of Output
 *
 * @author developer
 */
class Output {

    /**
     * 
     */
    private $use_zlib;

    /**
     * 
     */
    private $zlib_compression;

    /**
     * 
     */
    private $headers = array();

    /**
     * 
     */
    private $output = "";

    /**
     * 
     */
    private $mime_types = array(
    );

    /**
     * 
     */
    private static $instance;

    /**
     * 
     */
    public function __construct() {
        $this->zlib_compression = @ini_get('zlib.output_compression');

        if (file_exists(CONFIG_PATH . "Mimes.php")) {
            include CONFIG_PATH . "Mimes.php";
        } else {
            $mimes = array();
        }
        $this->mime_types = array_merge($this->mime_types, $mimes);

        self::$instance = &$this;
    }

    /**
     * 
     */
    public function setOutput($data) {
        $this->output = $data;
    }

    /**
     * 
     */
    public function display($output = "", $parse_exec_vars = TRUE) {
        if ($output == "") {
            $output = & $this->output;
        }

        if ($parse_exec_vars) {
            $elapsed_time = microtime(true) - NITRO_TIME_START;
            $memory_usage = (!function_exists('memory_get_usage')) ? '0' : round(memory_get_usage() / 1024 / 1024, 2) . "MB";

            $output = str_replace('{elapsed_time}', $elapsed_time, $output);
            $output = str_replace('{memory_usage}', $memory_usage, $output);
        }

        if (Config::get("output")["zlib_compression"] == TRUE) {
            if ($this->zlib_compression == FALSE) {
                if (extension_loaded('zlib')) {
                    if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) {
                        ob_start('ob_gzhandler');
                    }
                }
            }
        }

        if (count($this->headers) > 0) {
            foreach ($this->headers as $header) {
                @header($header[0], $header[1]);
            }
        }

        if (method_exists(getInstance(), "_output")) {
            getInstance()->_output($output);
        } else {
            echo $output;
        }
    }

    /**
     * 
     */
    public function setHeader($header, $replace = TRUE) {
        $this->headers[] = array(
            $header,
            $replace
        );
    }

    public function setStatusCode($code = 200) {
        try {
            http_response_code($code);
        } catch (Exception $e) {
            
        }
    }

    public function setContentType($type) {
        if (strpos("/", $type)) {
            $ext = ltrim($type, '.');

            if (isset($this->mime_types[$ext])) {
                $type = &$this->mime_types[$ext];

                if (is_array($type)) {
                    $type = current($type);
                }
            }
        }

        $header = "Content-Type: " . $type;
        $this->setHeader($header);
    }

    /**
     * 
     */
    public function append($data) {
        $this->output .= $data;
    }

    /**
     * 
     */
    public function getRawOutputBuffer() {
        return $this->output;
    }

    /**
     * 
     */
    public function __destruct() {
        
    }

    public static function &getInstance() {
        return self::$instance;
    }

}
