<?php
namespace Api\Ala;

class BulkSpeciesLookup{
    
    private $body;
    public $_status = false;
    public $species = array();
    function __construct($request){
        
        $names = (!is_array($request['taxon_name'])) ? explode(',', $request['taxon_name']) : $request['taxon_name'];
   
        for($i=0; $i < count($names); $i++){
            $names[$i] = trim($names[$i]);
        }
        
        $this->request($names);
        $this->compile();
    }
    
    public function request($names){
        $curl = new \Api\Connector();
        $response = $curl->post(
            'http://bie.ala.org.au/ws/species/lookup/bulk', 
            null,
            array('names' => $names)
        );
        $this->_status = $response->status;
        $this->body = $response->body;
    }
    
    //Genus:Acacia => totalRecords:107, common_name results: 100, raw_taxonomy_name results 107!
    public function compile(){
        foreach($this->body as $item){
            $this->species[$item->name]                = new \StdClass;
            $this->species[$item->name]->guid          = $this->property('guid', $item);
            $this->species[$item->name]->common_name   = $this->property('commonNameSingle', $item);
            $this->species[$item->name]->isAustralian  = $this->property('isAustralian', $item);
            $this->species[$item->name]->image         = $this->property('image', $item);
            $this->species[$item->name]->thumbnail     = $this->property('thumbnail', $item);
        }
    }
    
    private function property($property, $item){
        return (isset($item->{$property})) ? $item->{$property} : null;
    }
    
}
