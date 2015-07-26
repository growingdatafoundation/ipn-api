<?php
/**
 * /ala.occurences.php?include=ala.details&bname=Acacia&lat=-34.928726&lon=138.59994&radius=5&dump=1
 */
define('CONFIG_DEBUG', true);

if(CONFIG_DEBUG){
    ini_set('display_errors', 1); 
    error_reporting(E_ALL);
}
require_once ('./vendor/autoload.php');
require_once ('./ApiConfig.php');



/**
 * Request > Validation, required params
 */
 
if (!isset($_GET['taxon_name'])) {// taxonomy name
    \Api\Config::out(400, 'Invalid parameters: `taxon_name` required.');
}

$response = new StdClass;
$response->ala = new StdClass;
$response->ala->species = new \Api\Ala\Species($_GET);

/**
 * Dump
 */
 
if (isset($_GET['dump'])) {
    dump(json_decode(json_encode($response)));
    //print json_encode($response, JSON_PRETTY_PRINT);
    exit(1);
}

/**
 * Response > set headers
 * Enable CORS
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,HEAD,OPTIONS');
header('Access-Control-Allow-Headers: Origin,Content-Type,Authorization,Accept,X-Experience-API-Version,If-Match,If-None-Match');
header('Content-Type: text/html; charset=utf-8');
print json_encode($response);
exit(1);

/**
 * Response > include templates
 */
 
