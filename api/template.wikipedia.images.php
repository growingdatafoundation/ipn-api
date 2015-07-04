<?php
//Controller
//http://en.wikipedia.org/w/api.php?action=query&list=allimages&aiprop=url&format=xml&ailimit=10&aifrom=Albert

$curl = new \Api\Connector();
$response = $curl->get('https://en.wikipedia.org/w/api.php', 
    array(
        'action' => 'query', 
        'aifrom' => $tag,
        'list' => 'allimages', 
        'aiprop' => 'url', 
        'format' => 'json', 
        'ailimit' => $maxresults
    )
);

if($response->error()){
    //do nothing
    return;
}

$photos = $response->body->query->allimages;
?>
<div class="images">
<?php if(count($photos )> 0): ?>
<div class="item-title">Related Wikipedia images</div>
<?php endif; ?>
<?php
foreach((array) $photos as $key => $image){

    $size = 'm';
    $imageSrc = $image->url;

    $owner = '';
    echo \Api\Theme\Image::renderImage($imageSrc, $image->title, $owner, $image->descriptionurl);

}
?>  
</div>
