<?php
namespace Api\Ala;

/**
 * Species aggregator for different ala calls, in future to be extended
 */
 
class DensityMap{

    private $body;
    public $australia;
  
    function __construct($request){
        $this->australia = 'http://biocache.ala.org.au/ws/density/map?q='.urlencode($request['taxon_name']);
    }
    
}
