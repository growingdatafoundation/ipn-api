<?php
namespace Api\Ala;

/**
 * Base class for Ala
 */

class AlaBase{

    private $defaultQ = 'taxon_name';//Query of the form field:value e.g. q=genus:Macropus

    function __construct(){
    }

    /**
     * Occurence query cache request
     * @see self::qidRequest
     */
    public function paramsCacheQuery($request){
        $curl = new \Api\Curl\Client();
        $response = $curl->post(
            'http://biocache.ala.org.au/ws/webportal/params',
            null,
            array(
                'wkt'    => $request['wkt']
            ),
            false
        );
        return $response;
    }


    /**
     * @param array $param request params ($_GT,$_POST or $_REQUEST) or already extracted sub-set request
     * @param string $key optional, sub-set key to look for in $param
     * @return array
     */
    public static function parseCommaSeparatedParam($param, $key = false){
        if($key){
            $param = $param[$key];
        }
        $names = (!is_array($param)) ? explode(',', $param) : $param;
        for($i = 0; $i < count($names); $i++){
            $names[$i] = trim($names[$i]);
        }
        return $names;
    }

    protected function property($property, $item, $returnEmpty = null){
        return (isset($item->{$property})) ? $item->{$property} : $returnEmpty;
    }

    /**
     * q: query for occurences
     */
    protected function buildQ($q){
        if(!strpos($q, ':')){
            return (empty($this->defaultQ)) ? $q : $this->defaultQ.':'.$q;
        }
        return $q;
    }
}
