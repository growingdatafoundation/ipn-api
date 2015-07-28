<?php
require_once ('./bootstrap.php');

/**
 * /ala.occurences.php?include=ala.details&bname=Acacia&lat=-34.928726&lon=138.59994&radius=5&dump=1
 */

/**
 * Request > Validation, required params
 */

if (!isset($_GET['bname'])) {// botanical name
    \Api\View::out(400, 'Invalid parameters: `bname` required.');
}

if (!isset($_GET['lon'])) {// longitude
    \Api\View::out(400, 'Invalid parameters: `lon` required.');
}

if (!isset($_GET['lat'])) {// latitude
    \Api\View::out(400, 'Invalid parameters: `lat` required.');
}

if (!isset($_GET['radius'])) {// latitude
    \Api\View::out(400, 'Invalid parameters: `radius` required.');
}

if (!isset($_GET['include'])) {// selected template
    \Api\View::out(400, 'Invalid parameters: `include` required.');
}

$aggregator = new \Api\Aggregator();

/**
 * Base Module: Occurences
 */

$occurences = new \Api\Ala\Occurences($_GET);
$aggregator->set('ala.occurences', $occurences);

/**
 * Additional modules
 */
 // get species names for included modules
$species = array_keys($occurences->taxon_name);
$modules = $aggregator->parseModules($_GET['include']);

foreach((array)$modules as $module){
    $service = $aggregator->moduleToNamespacedClass($module);
    if(class_exists($service)){
        $data = new $service(array('taxon_name' => $species));
        $aggregator->set($module, $data);
    }
}

/**
 * Debug: Dump
 */

if (isset($_GET['dump'])) {// botanical name
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
