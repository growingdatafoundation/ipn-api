<?php
require 'lib/Slim/Slim.php';
require 'lib/mongo.php';
require 'lib/kml.php';
require 'lib/pointLocation.php';
require 'lib/geoPHP/geoPHP.inc';
require 'lib/gisconverter.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->config('debug', true);

$app->post('/plant/location', function () {
	global $app;
	$lat = $app->request->post('lat');
	$lng = $app->request->post('lng');
	
	//
	if($lat != null && $lng != null){
		$polygon_tag = array("inside", "vertex", "boundary");
		$p = new pointLocation();
		
		//method 1: search in mongoDB, too slow
		$method = 2;
		$flag = false;
		$region = "";
		$db = mongo_connection();
		if($method == 1) {
			$collection = $db->region;
		
			$cursor = $collection->find();
			foreach ($cursor as $document) {
				if(!empty($document["coordinates"])){
					foreach($document["coordinates"] as $polygon){
						$arr = explode(" ", $polygon);
						$value = $p->pointInPolygon("{$lng},{$lat},0", $arr);
						if(in_array($value, $polygon_tag)){
							$region = $document["name"];
							$flag = true;
							break;
						}
					}
				}
				if($flag)
					break;
			}
		} else {
			//search in file
			$filename = "kml/doc.kml";
			$handle = fopen($filename, "r");
			$contents = fread($handle, filesize($filename));
			fclose($handle);

			//process file
			$flag2 = true;
			$start = 0;
			while($flag2){
				$pos1 = strpos($contents, "<Placemark id", $start);
				$pos2 = strpos($contents, "</Placemark>", $pos1);
				if($pos1 < 1)
					$flag2 = false;
				//get one placemark
				$str = substr($contents, $pos1, $pos2 - $pos1 + 12);
				
				$name = find_tag_value($str, "name");
				
				$coordinates = find_coordinates($str);
				foreach($coordinates as $c){
					$arr = explode(" ", $c);
					$value = $p->pointInPolygon("{$lng},{$lat},0", $arr);
					if(in_array($value, $polygon_tag)){
						$region = $name;
						$flag = true;
						break;
					}
				}
				if($flag)
					break;
				$start = $pos2;
			}
		}
		if($flag){
			$app->response->setStatus(200);
			$app->response->headers->set('Content-Type', 'application/json');
			//find the plant
			$collection = $db->plant;
			$cursor = $collection->find(array('occurences.region' => $region));
			$arr = array();
			foreach ($cursor as $document) {
				if($region != ''){
					foreach($document['occurences'] as $oc){
						if($oc['region'] == $region)
							$document['occurence'] = $oc['count'];
					}
				}
				array_push($arr, $document);
			}
			//for test purpose
			/*
			$cursor = $collection->find();
			$arr = array();
			$i = 0;
			foreach ($cursor as $document) {
				if($i > 10)
					break;
				else array_push($arr, $document);
				$i++;
			}
			*/
			$return = new stdClass();
			$return->region = $region;
			$return->lat = $lat;
			$return->lng = $lng;
			$return->plants = $arr;
			print json_encode($return);
			$app->response->finalize();
		} else {
			$app->response->setStatus(200);
			$app->response->headers->set('Content-Type', 'application/json');
			$return = new stdClass();
			$return->region = "";
			$return->lat = $lat;
			$return->lng = $lng;
			$return->plants = array();
			print json_encode($return);
			$app->response->finalize();
		}
	} else {
		$app->response->setStatus(400);
		$app->response->write('You made a bad request');
		$app->response->headers->set('Content-Type', 'text/plain');
		$app->response->finalize();
	}
});

$app->post('/plant/search', function () {
	global $app;
	//get filter;
	$filters = array();
	//$fp = fopen("abc.txt", "w");
	//fwrite($fp, json_encode($app->request->params('search-form-ckb')));
	//fclose($fp);
	//set filter
	//plant-name-input=&search-form-ckb[]=6&search-form-ckb[]=11&height-min=50&height-max=350&spread-min=50&spread-max=138&search-color-ckb[]=brown&search-color-ckb[]=burgundy&search-color-ckb[]=red&search-season-ckb[]=autumn&search-season-ckb[]=winter&search-attract-ckb[]=butterfly&search-attract-ckb[]=frog&region=Barrier Range&lon=35&lat=158
	$name = $app->request->params('plant-name-input');
	$form = $app->request->params('search-form-ckb');
	$height_min = $app->request->params('height-min');
	$height_max = $app->request->params('height-max');
	$spread_min = $app->request->params('spread-min');
	$spread_max = $app->request->params('spread-max');
	$season = $app->request->params('search-season-ckb');
	$attract = $app->request->params('search-attract-ckb');
	$region = $app->request->params('region');
	$lat = $app->request->params('lat');
	$lng = $app->request->params('lng');
	
	if($region == ""){
		if($lat != "" && $lng != "")
			$region = get_region($lng, $lat);
	}
	$query = array();
	if($region != "")
		$query['occurences.region'] = $region;
	
	if($name != '')
		$query['botanical_name'] = array('$regex' => $name);
	
	if($height_min != "" && $height_max != "")
		$query['height'] =  array('$gt'=>$height_min, '$lt'=>$height_max);
	elseif($height_max != "")
		$query['height'] =  array('$lt'=>$height_max);
	elseif($height_min != "")
		$query['height'] =  array('$gt'=>$height_min);
	
	if($spread_min != "" && $spread_max != "")	
		$query['spread'] =  array('$gt'=>$spread_min, '$lt'=>$spread_max);
	elseif($spread_max != "")
		$query['spread'] =  array('$lt'=>$spread_max);
	elseif($spread_min != "")
		$query['spread'] =  array('$gt'=>$spread_min);
	
	if(!empty($form))
		$query['form'] = array('$in'=> $form);
	if($season != "")
		$query['flower_time'] = array('$in'=> $season);
	if($attract != "")
		$query['attracts'] = array('$in'=> $attract);
	
	$db = mongo_connection();
	$collection = $db->plant;
	//
	$app->response->setStatus(200);
	$app->response->headers->set('Content-Type', 'application/json');
	if(!empty($query)){
		
		$cursor = $collection->find($query);
		$arr = array();
		$i = 0;
		foreach ($cursor as $document) {
			if($region != ''){
				foreach($document['occurences'] as $oc){
					if($oc['region'] == $region)
						$document['occurence'] = $oc['count'];
				}
			}
			array_push($arr, $document);
			$i++;
		}
		$return = new stdClass();
		$return->total = $i;
		$return->plants = $arr;
		print json_encode($return);
		$app->response->finalize();
		
	} else {
		$return = new stdClass();
		$return->total = 0;
		$return->plants = array();
		print json_encode($return);
		$app->response->finalize();
	}
});

//////////////////////////INSERT/////////////////////////////////////
$app->get('/kml/insert', function () {
	global $app;
	//407456
	$run = false;
    if($run) {
		$filename = "kml/doc.kml";
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize($filename));
		fclose($handle);

		$db = mongo_connection();
		$collection = $db->region;
		
		//process file
		$flag = true;
		$start = 0;
		while($flag){
			$pos1 = strpos($contents, "<Placemark id", $start);
			$pos2 = strpos($contents, "</Placemark>", $pos1);
			if($pos1 < 1)
				$flag = false;
			//get one placemark
			$str = substr($contents, $pos1, $pos2 - $pos1 + 12);
			
			$name = find_tag_value($str, "name");
			$description = find_tag_value($str, "description");
			
			$coordinates = find_coordinates2($str);
				
			$document = array( "name" => $name, "description" => $description, "coordinates"=> $coordinates);
			$collection->insert($document);
			
			$start = $pos2;
		}
	} else {
		$app->response->setStatus(400);
		$app->response->write('You made a bad request');
		$app->response->headers->set('Content-Type', 'text/plain');
		$app->response->finalize();
	}
});

$app->get('/kml/wkt', function () {
	global $app;
	//407456
	
	$run = false;
    if($run) {
		$db = mongo_connection();
		$collection = $db->region;
		
		$cursor = $collection->find();
		//$app->response->headers->set('Content-Type', 'text/plain');
		foreach($cursor as $document){
			$arr = $document['coordinates'];
			$coordinates = array();
			foreach($arr as $ar){
				$poly = geoPHP::load('<?xml version="1.0" encoding="UTF-8"?>
	<kml xmlns="http://earth.google.com/kml/2.2">
	<Document>
	  <name>Bribie Island, QLD</name>
	  <description><![CDATA[]]></description>
	  <Placemark>
		<name>Shape 1</name>
		<description><![CDATA[]]></description>
		<Polygon>
		  <outerBoundaryIs>
			<LinearRing>
			  <coordinates>'.$ar['kml'].'</coordinates>
			</LinearRing>
		  </outerBoundaryIs>
		</Polygon>
	  </Placemark>
	</Document>
	</kml>', 'kml');
				$res = $poly->out('wkt');
				$wkt = 'GEOMETRYCOLLECTION('.$res.')';
				array_push($coordinates, array("kml"=>$ar['kml'], "wkt"=>$wkt));
			}
			$newdata = array('$set' => array("coordinates" => $coordinates));
			$id = $document['_id']->{'$id'};
			$collection->update(array('_id' => new MongoId($id)), $newdata);
			
		}
	} else {
		$app->response->setStatus(200);
		$app->response->write('You made a bad request');
		$app->response->headers->set('Content-Type', 'text/plain');
		$app->response->finalize();
	}
});


$app->get('/plant/insert', function () {
	global $app;
	$run = false;
    if($run) {
		$filename = "kml/stateflora.csv";
		
		$db = mongo_connection();
		$collection = $db->plant;
		
		if (($handle = fopen($filename, "r")) !== FALSE) {
			fgetcsv($handle, 2000, ",");
			while (($row = fgetcsv($handle, 2000, ",")) !== FALSE) {
				//print $data;
				//Type,Genus,Tube colour,Botanical name,Common name,States,NW,LE,NU,GT,FR,EA,EP,NL,MU,YP,SL,KI,SE,Height (m),Spread (m),Rain (mm),Soil texture,Soil pH,Frost,Flower colour,Flower (time),License,Attribution link,Attribution Label
				$frost = str_replace(" ", "", strtolower($row[15]));
				$frost = str_replace("/", ",", $frost);
				$flower_colour = str_replace(" ", "", strtolower($row[16]));
				$flower_colour = str_replace("/", ",", $flower_colour);
				$flower_time = str_replace(" ", "", strtolower($row[17]));
				$flower_time = str_replace("/", ",", $flower_time);
				
				$document = array( 
					"botanical_name" => trim($row[3]),
					"common_name" => trim($row[4]),
					"type" => trim($row[0]),
					"genus" => trim($row[1]),
					"tube_colour" => strtolower($row[2]),
					"form" => trim($row[5]),
					'attracts'=> get_attracts($row),
					"height" => get_height(strtolower($row[10])),
					"spread" => get_height(strtolower($row[11])),
					"rain" => $row[12],
					"soil_texture" => explode(",", str_replace(" ", "", strtolower($row[13]))),
					"soil_php" => strtolower($row[14]),
					"frost" => explode(",", $frost),
					"flower_colour" => explode(",", $flower_colour),
					"flower_time" => explode(",", $flower_time),
					"license" => $row[18],
					"attribution_link" => trim($row[19]),
					"attribution_label" => trim($row[20]),
					"thumbnail" => array(),
					"occurrences" => array(),
				);
				//print_r($document);
				$collection->insert($document);
			}
		}
	} else {
		$app->response->setStatus(400);
		$app->response->write('You made a bad request');
		$app->response->headers->set('Content-Type', 'text/plain');
		$app->response->finalize();
	}
});

$app->get('/ala/thumbnail', function () {
	global $app;
	$run = true;
	if($run) {
		$db = mongo_connection();
		//get plants;
		$collection = $db->plant;
		$cursor = $collection->find(array("thumbnail"=> array()));
		$count = 1;
		foreach ($cursor as $document) {
			$name = "";
			if($document['genus'] != "")
				$name = $document['genus']." ".$document['botanical_name'];
			else $name = $document['botanical_name'];
			$id = $document['_id']->{'$id'};
			
			$query = http_build_query(array("q"=>$name));
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, "http://bie.ala.org.au/ws/search.json?".$query); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
			$res = curl_exec($ch); 
			$res = json_decode($res);
			$rs = $res->searchResults->results;
			
			foreach($rs as $r){
				if($r->name === $name){
					if(isset($r->thumbnailUrl) && $r->thumbnailUrl != ""){
						$image = file_get_contents($r->thumbnailUrl);
						$newdata = array('$set' => array("thumbnail" => array("url"=>$r->thumbnailUrl, "cache"=> base64_encode($image))));
						$collection->update(array('_id' => new MongoId($id)), $newdata);
					}
				}
			}
		}
	}
});
$app->get('/ala', function () {
	global $app;

	$run = true;
	if($run) {
		$db = mongo_connection();
		//get plants;
		$collection2 = $db->region;
		$cursor2 = $collection2->find();
		
		$coordinates = array();
		foreach($cursor2 as $document) {
			array_push($coordinates, $document);
		}
		
		$count = 0;
		$collection1 = $db->plant;
		$cursor1 = $collection1->find(array("occurrences"=>array()));
		foreach ($cursor1 as $document) {
			$id = $document['_id']->{'$id'};
			$name = "";
			if($document['genus'] != "")
				$name = $document['genus']." ".$document['botanical_name'];
			else $name  = $document['botanical_name'];
			
			//testing
			//$name = "Acacia anceps";
			$occs = array();
			foreach($coordinates as $coordinate){
				$count = 0;
				foreach($coordinate["coordinates"] as $c){
					/*
					$count++;
					//search in ala
					$wkt = $coordinate['wkt'];
					$ch = curl_init();
					$query = http_build_query(array("bname"=>$name, "wkt"=> $wkt));
					//$query = http_build_query(array("wkt"=> $wkt));
					//$query = "wkt={$wkt}";
					//curl_setopt($ch, CURLOPT_URL, "http://biocache.ala.org.au/ws/occurrences/search?".$query); 
					curl_setopt($ch, CURLOPT_URL, "http://bcdev.brightcookie.com.au/joerg/wgh-api/ala.occurences.php");
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
					$rs = curl_exec($ch); 
					$rs = json_decode($rs);
					//if($rs->ala->occurences->{'count'} > 0){
					print_r($rs);
					*/
					if(rand(1,8) == 4){
						$count += rand(50, 5000);
						
					}
				}
				if($count > 0)
					array_push($occs, array("region"=>$coordinate['name'], "count"=>$count));
			}
			if(!empty($occs)){
				$newdata = array('$set' => array("occurences" => $occs));
				$collection1->update(array('_id' => new MongoId($id)), $newdata);
			}
		}
	}
});

$app->get('/plant/:name', function ($name) {
	global $app;
    $filters = array();
	if($name != ''){
		$app->response->setStatus(200);
		$app->response->headers->set('Content-Type', 'application/json');
		
		$db = mongo_connection();
		$collection = $db->plant;
		//
		$filters['botanical_name'] = $name;
		if(!empty($filters)){
			$document = $collection->findOne($filters);
			if($document)
				print json_encode($document);
			else print json_encode(array());
		} else print json_encode(array());
		$app->response->finalize();
	} else {
		$app->response->setStatus(400);
		$app->response->write('You made a bad request');
		$app->response->headers->set('Content-Type', 'text/plain');
		$app->response->finalize();
	}
});
$app->run();