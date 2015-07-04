<?php
//Theme class
require_once('./Theme/Image.php');

//Controller
//http://en.wikipedia.org/w/api.php?action=query&list=allimages&aiprop=url&format=xml&ailimit=10&aifrom=Albert
$curl = new Connector();
$response = $curl -> get('http://en.wikipedia.org/w/api.php', 
	array('action' => 'query', 
		'aifrom' => $tag,
		'list' => 'allimages', 
		'aiprop' => 'url', 
		'format' => 'json', 
		'ailimit' => $maxresults));

$photos = $response -> query -> allimages;
?>
<div class="images">
<?php if(count($photos )> 0): ?>
<div class="item-title">Related Flickr images</div>
<?php endif; ?>
<?php
foreach((array) $photos as $key => $image){

	$size = 'm';
	$imageSrc = $image->url;

	$owner = '';
	echo Image::renderImage($imageSrc, $image->title, $owner, $image->descriptionurl);

}
?>	
</div>