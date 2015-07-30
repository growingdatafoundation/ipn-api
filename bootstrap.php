<?php
define('CONFIG_DEBUG', false);

if(CONFIG_DEBUG){
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

require_once ('./vendor/autoload.php');
require_once ('./ApiConfig.php');
