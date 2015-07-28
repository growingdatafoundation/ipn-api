<?php
require_once ('./bootstrap.php');

/**
 * /ala.occurences.php?include=ala.details&bname=Acacia&lat=-34.928726&lon=138.59994&radius=5&dump=1
 */

/**
 * Request > Validation, required params
 */

if (!isset($_GET['guid'])) {// longitude
    \Api\View::out(400, 'Invalid parameters: `guid` required.');
}

$aggregator = new \Api\Aggregator();

$groups = new \Api\Ala\Species\Details($_GET);
$aggregator->set('ala.species.details', $groups);

/**
 * Debug: Dump
 */

if (isset($_GET['dump'])) {
    \Api\View::serviceHeaders('html');
    dump(json_decode(json_encode($aggregator)));
    //print json_encode($aggregator, JSON_PRETTY_PRINT);
    exit(1);
}

/**
 * Default: Data
 */

\Api\View::serviceHeaders();

print json_encode($aggregator);
exit(1);
