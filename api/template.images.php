<?php
//Controller
$curl = new \Api\Connector();
$response = $curl->get('https://api.flickr.com/services/rest/', 
    array('method' => 'flickr.photos.search', 
        'api_key' => \Api\Config::$Flickr['key'], 
        'tags' => $tag, 
        'per_page' => $maxresults, 
        'format' => 'json', 
        'licenses' => '1,2,3,4,5,6,7,8', //@see https://www.flickr.com/services/api/flickr.photos.licenses.getInfo.html
        'sort' => 'relevance',
        'nojsoncallback' => '1'
    )
);

if($response->error()){
    //do nothing
    return;
}

$photos = $response->body->photos->photo;
?>
<div class="images">
<?php if(count($photos )> 0): ?>
<div class="item-title">Related Flickr images</div>
<?php endif; ?>
<?php
foreach((array) $photos as $key => $image){

    $size = 'm';
    $imageSrc = 'http://farm'.$image->farm.'.staticflickr.com/'.$image->server.'/'.$image->id.'_'.$image->secret.'_'.$size.'.'.'jpg';

    //owner
    $owner = $curl->get('https://api.flickr.com/services/rest/', 
        array('method' => 'flickr.people.getInfo', 
            'api_key' => \Api\Config::$Flickr['key'],
            'user_id' => $image->owner,
            'format' => 'json', 
            'nojsoncallback' => '1'
        )
    );
    
    if($owner->error()){
        //do nothing
        return;
    }
    
    $owner = $owner->body;
    
    //image
    $url = 'https://www.flickr.com/photos/'.$image->owner.'/'.$image->id;
    $owner = \Api\Theme\Image::renderOwner($owner->person->username->_content, $owner->person->photosurl->_content);
    echo \Api\Theme\Image::renderImage($imageSrc, $image->title, $owner, $url);

}
?>  
</div>

