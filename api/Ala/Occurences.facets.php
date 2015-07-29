<?php
/*
 * http://biocache.ala.org.au/ws/occurrence/facets
 * @TODO unfinished
 * This call is much faster than http://biocache.ala.org.au/ws/occurrences/search, however the results differ greatly
 * Genus:Acacia => totalRecords:107, common_name results: 100, raw_taxonomy_name results 107!
 * Not implemented until it's clear why the difference
 */

namespace Api\Ala;

class Occurences extends AlaBase{

    private $body;
    public $_status = false;
    public $_errors = array();

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
        $curl = new \Api\Curl\Client();
        $response = $curl->get('http://biocache.ala.org.au/ws/occurrence/facets',
            array(
                'q' => 'genus:'.$request['bname'],
                'facets' => 'taxon_name',
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
        $result = $this->body[0];
        $this->count = $result->count;
        $this->{$result->fieldName} = $this->_fieldResult($result);
    }

    private function _fieldResult($result){
        $r = array();
        foreach($result->fieldResult as $item){
            if(!empty($item->label) && isset($item->count)){
                $r[$item->label] = $item->count;
            }
        }
        return $r;
    }

}
