<?php
$password = ""; //myname
$mongo_string = "mongodb://kenny:{$password}@ds051160.mongolab.com:51160/gdf-dev";
$mongo_db = "gdf-dev";

//
function mongo_connection(){
	global $mongo_string;
	global $mongo_db;
	$m = new MongoClient($mongo_string); // connect
	$db = $m->selectDB($mongo_db);
	return $db;
}
?>
