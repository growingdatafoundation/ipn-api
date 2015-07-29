<?php
namespace Api\Ala\Occurences;

use \Api\Ala as Ala;

/**
 * http://biocache.ala.org.au/ws/occurrences/search
 * Compiles an extract of occurences for a species name search term.
 * Occurences counts will be listed in two arrays, by taxon_name and common_name.
 * The most complete result will be the taxon_name occurences as not all species have a common_name
 */

class Region extends Ala\Occurences{

    private $body;
    public $_status = false;

    function __construct($request){
        
        $cached = $this->paramsCacheQuery($request);

        if($cached->status != 200){
            $this->_status = $response->status;
            $this->_errors = $response->errors;
            return false;
        }
        
        parent::__construct($request, $cached->body);
    }
    
    /**
     * Occurence query cache request
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
    
}
