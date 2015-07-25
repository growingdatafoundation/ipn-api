<?php
/**
 * /api/json.php?return=ala.occurences,ala.details&bname=Acacia&lat=-34.928726&lon=138.59994&radius=5
 */
define('CONFIG_DEBUG', true);

if(CONFIG_DEBUG){
    ini_set('display_errors', 1); 
    error_reporting(E_ALL);
}
require_once ('../vendor/autoload.php');
require_once ('../ApiConfig.php');



/**
 * Request > Validation, required params
 */
 
if (!isset($_GET['bname'])) {// botanical name
    \Api\Config::out(400, 'Invalid parameters: `bname` required.');
}

if (!isset($_GET['lon'])) {// longitude
    \Api\Config::out(400, 'Invalid parameters: `lon` required.');
}

if (!isset($_GET['lat'])) {// latitude
    \Api\Config::out(400, 'Invalid parameters: `lat` required.');
}

if (!isset($_GET['radius'])) {// latitude
    \Api\Config::out(400, 'Invalid parameters: `lat` required.');
}

if (!isset($_GET['return'])) {// selected template
    \Api\Config::out(400, 'Invalid parameters: `return` required.');
}

$models = explode(',', $_GET['return']);
$response = new StdClass;

foreach((array)$models as $model){
    $model = strtolower(trim($model));
    $namespace = explode('.', $model);
    $service = '\\Api\\'.ucfirst($namespace[0]).'\\'.ucfirst($namespace[1]);

    if(class_exists($service)){
        $data = new $service($_GET);
        $response->{$namespace[0]} = new StdcLass;
        $response->{$namespace[0]}->{$namespace[1]} = $data;
    }
}

/**
 * Dump
 */
 
if (isset($_GET['dump'])) {// botanical name
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
 
