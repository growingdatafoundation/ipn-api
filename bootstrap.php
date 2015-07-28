<?php
define('CONFIG_DEBUG', true);

if(CONFIG_DEBUG){
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

require_once ('./vendor/autoload.php');
require_once ('./ApiConfig.php');
