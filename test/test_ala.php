<?php 
//hard coded
$species = "Birds";
$lon = "138.59994";
$lat = "-34.928726";
$rad = "5"; //km

echo "<h2>Test http://api.ala.org.au/</h2>";

//Overall ALA species counts
$url = "http://biocache.ala.org.au/ws/explore/groups?lon=$lon&lat=$lat&radius=$rad";
$response = json_decode(file_get_contents($url));

echo "<h3>Species Count</h3>";

foreach ($response as $value) {
	if($value->name == $species) {
		$message = " Bird sightings within 5km radius of you location.";
		if($value->speciesCount > 0) {
			echo $value->speciesCount . $message;
		}else{
			echo "No" . $message;
		}
		
	}
}

//Lis of bird species
$url = "http://biocache.ala.org.au/ws/occurrences/search?q=$species&fq=kingdom:ANIMALIA&lon=$lon&lat=$lat&radius=$rad";
$response = json_decode(file_get_contents($url));
echo "<h3>Species Sighted : <small>$response->queryTitle</small></h3>";


//get full item details
echo "<div class=\"species\">";
foreach ($response->facetResults[1]->fieldResult as $value) {
	$taxon_name = str_replace(" ", "+", $value->label);
	$itemUrl = "http://bie.ala.org.au/ws/search.json?q=" . $taxon_name . "&fq=kingdom:ANIMALIA";
	$itemResponse = json_decode(file_get_contents($itemUrl));
	$item = $itemResponse->searchResults->results[0];
	$commonName = $item->commonNameSingle . " : ";
	$name = $item->name;
	if(!isset($item->commonNameSingle)) {
		$commonName = $item->name;
		$name = "";
	}
	echo "<div class=\"item\">";
	echo "<div class=\"title\">" . $commonName . " <span class=\"taxon\">" . $name . "</span></div>";
	if(isset($item->thumbnailUrl)) {
		echo "<div class=\"imageItem\"><img src=\"$item->thumbnailUrl\" alt=\"$item->name\"></div>";
	}
	echo "</div>";
}
echo "</div>";

/*if(count($response->facetResults[2])>1) {
	
	foreach ($response->facetResults[2]->fieldResult as $value) {
		echo "<div class=\"item\">";
		echo "<div class=\"title\">" . $value->label . "</div>";
		echo "<div class=\"content\"><p></p></div>";
		echo "</div>";
	}
	
}*/

//print ("<pre>");
//print_r($response);
//print ("</pre>");

 ?>
