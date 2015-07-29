<?php
namespace Api\Ala;

/**
 * Species aggregator for different ala calls, in future to be extended
 */

class DensityMap extends AlaBase{

    private $body;
    public $_status = 200;
    public $_errors = array();
    
    public $australia;

    function __construct($request){
        parent::__construct();
        $this->australia = 'http://biocache.ala.org.au/ws/density/map?q='.urlencode($request['taxon_name']);
    }

}
