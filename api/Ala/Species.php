<?php
namespace Api\Ala;

/**
 * Species aggregator for different ala calls, in future to be extended
 */
 
class Species{

    private $body;
    public $_status = false;
    public $species;
  
    function __construct($request){
        $lookup = new \Api\Ala\BulkSpeciesLookup($request);
        $this->_status = $lookup->_status;
        $this->species = $lookup->species;
        
        foreach($this->species as $name => $item){
            $this->species[$name]->densityMap =  new \Api\Ala\DensityMap(array('taxon_name' => $name));
        }
    }
    
}
