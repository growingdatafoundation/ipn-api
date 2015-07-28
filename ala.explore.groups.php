<?php
require_once ('./bootstrap.php');

/**
 * /ala.occurences.php?include=ala.details&bname=Acacia&lat=-34.928726&lon=138.59994&radius=5&dump=1
 */

/**
 * Request > Validation, required params
 */

if (!isset($_GET['lon'])) {// longitude
    \Api\View::out(400, 'Invalid parameters: `lon` required.');
}

if (!isset($_GET['lat'])) {// latitude
    \Api\View::out(400, 'Invalid parameters: `lat` required.');
}

if (!isset($_GET['radius'])) {// latitude
    \Api\View::out(400, 'Invalid parameters: `lat` required.');
}

$response = new StdClass;
$response->ala = new StdClass;
$response->ala->explore = new \Api\Ala\Explore\Groups($_GET);

/**
 * Debug: Dump
 */

if (isset($_GET['dump'])) {// botanical name
    \Api\View::serviceHeaders('html');
    dump(json_decode(json_encode($response)));
    //print json_encode($response, JSON_PRETTY_PRINT);
    exit(1);
}

/**
 * Default: Data
 */

\Api\View::serviceHeaders();

print json_encode($response);
exit(1);
