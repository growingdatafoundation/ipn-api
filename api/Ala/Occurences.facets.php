<?php
// @TODO unfinished
namespace Api\Ala;

class Occurences extends AlaBase{

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
            $r[$item->label] = $item->count;
        }
        return $r;
    }

}
