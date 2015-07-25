<?php
namespace Api\Ala;

class Occurences{
    
    private $body;
    public $totalRecords = 0;
    public $status = false;
    public $results = array(
        'common_name' => array(),
        'taxon_name' => array(),
    );
  
    function __construct($request){
        $this->get($request);
        $this->compile();
    }
    
    public function get($request){
        $curl = new \Api\Connector();
        $response = $curl->get('http://biocache.ala.org.au/ws/occurrences/search', 
            array(
            'q' => 'genus:'.$request['bname'], 
            'lat' => $request['lat'],
            'lon' => $request['lon'],
            'radius' => $request['radius']
            )
        );
        $this->status = $response->status;
        $this->body = $response->body;
    }
    
    //Genus:Acacia => totalRecords:107, common_name results: 100, raw_taxonomy_name results 107!
    public function compile(){
        $this->totalRecords = $this->body->totalRecords;
        
        $this->results['common_name'] = $this->resultsByLabel('common_name');
        $this->results['taxon_name'] = $this->resultsByLabel('taxon_name');
    }
  
    private function resultsByLabel($label){
        $r = array();
        $list = $this->_filterFacetResults($label);
        if(!$list){
            return $r;
        }
        foreach($list->fieldResult as $item){
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
