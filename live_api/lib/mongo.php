<?php
$mongo_string = "";  //connection endpoint
$mongo_db = ""; //database name

//
function mongo_connection(){
	global $mongo_string;
	global $mongo_db;
	$m = new MongoClient($mongo_string); // connect
	$db = $m->selectDB($mongo_db);
	return $db;
}
?>
