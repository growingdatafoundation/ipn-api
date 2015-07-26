<?php
namespace Api\Ala;

class Occurences{
    
    private $body;
    public $count = 0;
    public $_status = false;
    public $common_name = array();
    public $taxon_name = array();
  
    function __construct($request){
        $this->request($request);
        $this->compile();
    }
    
    public function request($request){
        $curl = new \Api\Connector();
        $response = $curl->get(
            'http://biocache.ala.org.au/ws/occurrences/search', 
            array(
                'q'      => 'genus:'.$request['bname'], 
                'lat'    => $request['lat'],
                'lon'    => $request['lon'],
                'radius' => $request['radius']
            )
        );
        $this->_status = $response->status;
        $this->body = $response->body;
    }
    
    //Genus:Acacia => totalRecords:107, common_name results: 100, raw_taxonomy_name results 107!
    public function compile(){
        $this->count = $this->body->totalRecords;
        
        $this->common_name = $this->resultsByLabel('common_name');
        $this->taxon_name = $this->resultsByLabel('taxon_name');
    }
  
    private function resultsByLabel($label){
        $r = array();
        $list = $this->_filterFacetResults($label);
        if(!$list){
            return $r;
        }
        foreach($list->fieldResult as $item){
            $label = (isset($item->label)) ? $label : 'not set';
            $r[$item->label] = $item->count;
        }
        return $r;
    }
    
    private function _filterFacetResults($label){
        foreach($this->body->facetResults as $result){
            if($result->fieldName == $label){
                return $result;
            }
        }
        return false;
    }
}
