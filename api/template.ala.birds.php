<?php
/**
 * Hard coded
 * @TODO: GET param
 */
$species = "Birds";
$lon = (!empty($_GET['lon'])) ? $_GET['lon'] : "138.59994";
$lat = (!empty($_GET['lat'])) ? $_GET['lat'] : "-34.928726";
$rad = "5"; //km

echo $lon . ";" . $lat;
//Theme class
require_once('./Theme/Image.php');

//Controller
$curl = new Connector();
$response = $curl -> get('http://biocache.ala.org.au/ws/explore/groups', 
	array('lon' => $lon, 
		'lat' => $lat, 
		'radius' => $rad));

$selected = array();
foreach ($response as $value) {
	if($value->name == $species) {
		if($value->speciesCount > 0) {
			$selected[] = $value;
		}
		
	}
}

?>
<?php
//List of bird species
//"http://biocache.ala.org.au/ws/occurrences/search?q=$species&fq=kingdom:ANIMALIA&lon=$lon&lat=$lat&radius=$rad";
$responseDetails = $curl -> get('http://biocache.ala.org.au/ws/occurrences/search', 
	array('q' => $species, 
		'fq' => 'kingdom:ANIMALIA', 
		'lat' => $lat,
		'lon' => $lon,
		'radius' => $rad));
//get full item details
?>
<div class="species">
<?php if(count($selected )> 0): ?>
<div class="item-title">Bird sightings within <?php echo $rad.'km'; ?> radius of your <a href="<?php echo Helpers::googleMapsUri($lat, $lon) ?>" target="_blank">location.</a></div>
<div class="item-summary"><?php echo count($responseDetails ->facetResults[1]->fieldResult) ?> bird species found.</div>
<?php endif; ?>
<?php
$count = 0;
foreach ($responseDetails ->facetResults[1]->fieldResult as $value) {
	$count++;
	if($count > $maxresults){
		continue;
	}
	$taxon_name = str_replace(" ", "+", $value->label);
	
	//$itemUrl = "http://bie.ala.org.au/ws/search.json?q=" . $taxon_name . "&fq=kingdom:ANIMALIA";
	//$itemResponse = json_decode(file_get_contents($itemUrl));
	$itemResponse = $curl -> get('http://bie.ala.org.au/ws/search.json', 
	array('q' => $taxon_name, 
		'fq' => 'kingdom:ANIMALIA'));
		
	$item = $itemResponse->searchResults->results[0];
	$commonName = (isset($item->commonNameSingle)) ? ($item->commonNameSingle . " : ") : '';
	$name = (isset($item->commonNameSingle)) ? $item->name : '';
	if(!isset($item->commonNameSingle)) {
		$commonName = $name;
		$name = "";
	}
	echo "<div class=\"imageItem\">";
	echo "<div class=\"title\">" . $commonName . " <span class=\"taxon\">" . $name . "</span></div>";
	if(isset($item->thumbnailUrl)) {
		echo "<img src=\"$item->thumbnailUrl\" alt=\"$item->name\">";
	}
	echo "</div>";
}
echo "</div>";
