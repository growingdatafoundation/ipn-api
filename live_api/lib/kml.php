<?php
function find_tag_value($str, $tag){
	$pos1 = strpos($str, "<{$tag}>");
	$pos2 = strpos($str, "</{$tag}>", $pos1);
	$value = "";
	if($pos1 > 0 && $pos2 > $pos1)
		$value = substr($str, $pos1 + strlen("<{$tag}>"), $pos2 - $pos1 - strlen("</{$tag}>") + 1);
	
	return $value;
}
function find_coordinates($str){
	$flag = true;
	$coordinates = array();
	$start = 0;
	while($flag){
		$pos1 = strpos($str, "<coordinates>", $start);
		$pos2 = strpos($str, "</coordinates>", $pos1);
		$value = "";
		if($pos1 > 0 && $pos2 > $pos1){
			$value = substr($str, $pos1 + strlen("<coordinates>"), $pos2 - $pos1 - strlen("</coordinates>") + 1);
			array_push($coordinates, trim($value));
			$start = $pos2;
		} else $flag = false;
	}
	return $coordinates;
}
function find_coordinates2($str){
	$flag = true;
	$coordinates = array();
	$start = 0;
	while($flag){
		$pos1 = strpos($str, "<coordinates>", $start);
		$pos2 = strpos($str, "</coordinates>", $pos1);
		$value = "";
		if($pos1 > 0 && $pos2 > $pos1){
			$value = substr($str, $pos1 + strlen("<coordinates>"), $pos2 - $pos1 - strlen("</coordinates>") + 1);
			array_push($coordinates, array("kml"=>trim($value), "wkt"=> ""));
			$start = $pos2;
		} else $flag = false;
	}
	return $coordinates;
}
function get_height($val){
	if($val != ""){
		$arr = explode("-", $val);
		if(count($arr) == 2){
			return array($arr[0], $arr[1]);
		} else return array($arr[0]);
	}
	return array();
}
function get_attracts($row){
	$atts = array();
	if($row[6] == 1)
		array_push($atts, "bird");
	if($row[7] == 1)
		array_push($atts, "bee");
	if($row[8] == 1)
		array_push($atts, "butterfly");
	if($row[9] == 1)
		array_push($atts, "frog");
	return $atts;
}
function get_region($lng, $lat){
	$polygon_tag = array("inside", "vertex", "boundary");
	$p = new pointLocation();
	
	$filename = "kml/doc.kml";
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	fclose($handle);

	//process file
	$flag2 = true;
	$start = 0;
	$flag = false;
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
				$flag = true;
				return $name;
			}
		}
		if($flag)
			break;
		$start = $pos2;
	}
	return "";
}
?>