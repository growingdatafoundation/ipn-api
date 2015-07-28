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
    \Api\View::out(400, 'Invalid parameters: `lat` required.');
}

if (!isset($_GET['include'])) {// selected template
    \Api\View::out(400, 'Invalid parameters: `include` required.');
}

$response = new StdClass;
$response->ala = new StdClass;
$response->ala->occurences = new \Api\Ala\Occurences($_GET);

// get species names
$species = array_keys($response->ala->occurences->taxon_name);

/**
 * Additional modules
 */

$modules = explode(',', $_GET['include']);
foreach((array)$modules as $module){
    $module = strtolower(trim($module));
    $namespace = explode('.', $module);
    $service = '\\Api\\'.ucfirst($namespace[0]).'\\'.ucfirst($namespace[1]);

    if(class_exists($service)){
        $data = new $service(array('taxon_name' => $species));
        if(!isset($response->{$namespace[0]})){
            $response->{$namespace[0]} = new StdcLass;
        }
        $response->{$namespace[0]}->{$namespace[1]} = $data;
    }
}

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
