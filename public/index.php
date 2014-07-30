<?php

define("NITRO_TIME_START", microtime(true));
define("BASE_PATH", __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);
define("NITRO_PATH", BASE_PATH.'nitrous'.DIRECTORY_SEPARATOR);
define("CLASS_PATH", NITRO_PATH.'classes'.DIRECTORY_SEPARATOR);

try {
    require CLASS_PATH . "Paths.php";
    require NITRO_PATH . "Bootstrap.php";
} catch(Exception $e) {
    echo "There appears to be an issue with application setup.";
    exit(1);
}

Bootstrap::setMode(Mode::DEVELOPMENT);


Bootstrap::run();

?>
