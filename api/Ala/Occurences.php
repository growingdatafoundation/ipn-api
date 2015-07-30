<?php
namespace Api\Ala;

/**
 * http://biocache.ala.org.au/ws/occurrences/search
 * Compiles an extract of occurences for a species name search term.
 * Occurences counts will be listed in two arrays, by taxon_name and common_name.
 * The most complete result will be the taxon_name occurences as not all species have a common_name
 */

class Occurences extends AlaBase{

    private $body;
    public $_status = false;
    public $_errors = array();

    public $count = 0;
    public $common_name = array();
    public $taxon_name = array();

    function __construct($request, $hasWkt = false){
        parent::__construct();

        if($hasWkt){
            $cached = $this->paramsCacheQuery($request);
            if($cached->status != 200){
                $this->_status = $response->status;
                $this->_errors = $response->errors;
                return false;
            }
            $this->qidRequest($request, $cached->body);
        }else{
            $this->request($request);
        }

        $this->compile();
    }

    /**
     * Location request
     * @param array $request $_GET: required fields: lon, lat, rad, q
     */
    public function request($request){
        $curl = new \Api\Curl\Client();
        $response = $curl->get(
            'http://biocache.ala.org.au/ws/occurrences/search',
            array(
                'q'      => $this->buildQ($request['bname']),
                'lat'    => $request['lat'],
                'lon'    => $request['lon'],
                'radius' => $request['radius']
            )
        );
        $this->_status = $response->status;
        $this->body = $response->body;
    }

    /**
     * Region request (wkt polygon + occurence cache request)
     * @see \Api\Ala\Occurences\Region
     * @param array $request $_GET: required fields: q
     * @param string $qid occurence query cache id, provided by http://biocache.ala.org.au/ws/webportal/params
     */
    public function qidRequest($request, $qid){
        $curl = new \Api\Curl\Client();
        $response = $curl->get(
            'http://biocache.ala.org.au/ws/occurrences/search',
            array(
                'q'      => 'qid:'.$qid,
                'fq'     => $this->buildQ($request['bname']),
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
