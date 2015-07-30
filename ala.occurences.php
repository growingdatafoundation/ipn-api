<?php
require_once ('./bootstrap.php');

/**
 * /ala.occurences.php?include=ala.details&bname=Acacia&lat=-34.928726&lon=138.59994&radius=5&dump=1
 */

/**
 * Request > Validation, required params
 */

if (!isset($_REQUEST['bname'])) {// botanical name
    \Api\View::out(400, 'Invalid parameters: `bname` required.');
}

$wkt = (isset($_POST['wkt']));

if(!$wkt){
    if (!isset($_GET['lon'])) {// longitude
        \Api\View::out(400, 'Invalid parameters: `lon` required.');
    }

    if (!isset($_GET['lat'])) {// latitude
        \Api\View::out(400, 'Invalid parameters: `lat` required.');
    }

    if (!isset($_GET['radius'])) {// latitude
        \Api\View::out(400, 'Invalid parameters: `radius` required.');
    }

}

if($wkt){
    if (empty($wkt)) {// wkt string
        $wkt = false;
        \Api\View::out(400, 'Invalid parameters: `wkt` is empty.');
    }
}

$aggregator = new \Api\Aggregator();

/**
 * Base Module: Occurences
 */

$request = ($wkt) ? $_REQUEST : $_GET;
$occurences = new \Api\Ala\Occurences($_REQUEST, $wkt);

$aggregator->set('ala.occurences', $occurences);

/**
 * Additional modules
 */

if (isset($request['include'])){
    // get species names for included modules
    $species = array_keys($occurences->taxon_name);
    $modules = $aggregator->parseModules($request['include']);

    // add species for modules who require this, keep location data for modules who require them
    $request['taxon_name'] = $species;

    foreach((array)$modules as $module){
        $service = $aggregator->moduleToNamespacedClass($module);
        if(class_exists($service)){
            $data = new $service($request, $wkt);
            $aggregator->set($module, $data);
        }
    }
}

/**
 * Debug: Dump
 */

if (isset($request['dump']) && (int) $request['dump'] > 0) {
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
