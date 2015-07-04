<?php

define('CONFIG_DEBUG', true);

if(CONFIG_DEBUG){
    ini_set('display_errors', 1); 
    error_reporting(E_ALL);
}
require_once ('../vendor/autoload.php');
require_once ('../ApiConfig.php');

/**
 * Dispatcher
 * https://www.flickr.com/services/api/explore/flickr.photos.search
 */

//controller vars
$tag = '';
$maxresults = 5;

/**
 * Request > Validation, required params
 */
 
if (!isset($_GET["bname"])) {// botanical name
    \Api\Config::out(400, 'Invalid parameters: `bname` required.');
}

if (!isset($_GET["template"])) {// selected template
    \Api\Config::out(400, 'Invalid parameters: `template` required.');
}

/**
 * Request > Format request params
 */
 
$tag = htmlspecialchars($_GET["bname"]);
$template = htmlspecialchars($_GET["template"]);
//maxresults: optional 
$maxresults = (!empty($_GET["maxresults"]) && (int) $_GET["maxresults"] > 0) ? $_GET["maxresults"] : $maxresults;


/**
 * Response > set headers
 * Enable CORS
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,HEAD,OPTIONS');
header('Access-Control-Allow-Headers: Origin,Content-Type,Authorization,Accept,X-Experience-API-Version,If-Match,If-None-Match');
header('Content-Type: text/html; charset=utf-8');

/**
 * Response > include templates
 */
 
$templates = explode(',', $template);

foreach($templates as $template){
    
    $templateFile = 'template.'.$template.'.php';
    if(!is_file($templateFile)){
        \Api\Config::out(400, 'Invalid `template`.');
    }
    require($templateFile);
}
