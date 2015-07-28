<?php
namespace Api\Ala\Explore;

use \Api\Ala as Ala;

/**
 * Species aggregator for different ala calls, in future to be extended
 */

class Groups extends Ala\AlaBase{

    private $body;
    public $count = 0;//total
    public $groups = array();

    function __construct($request){
        parent::__construct();
        $this->request($request);
        $this->compile();
    }

    public function request($request){
        $curl = new \Api\Curl\Client();
        $response = $curl->get(
            'http://biocache.ala.org.au/ws/explore/groups',
            array(
                'lat'    => $request['lat'],
                'lon'    => $request['lon'],
                'radius' => $request['radius']
            )
        );
        $this->_status = $response->status;
        $this->body = $response->body;
    }

    public function compile(){
        foreach((array)$this->body as $group){
            if(!empty($group->name) && isset($group->count)){
                if(strtolower($group->name) == 'all_species' ){
                    $this->count = $group->count;
                    continue;
                }
                $this->groups[$this->humanize($group->name)] = $group->count;
            }
        }
    }

    /**
     * deals with camelcase labels: 'FernsAndAllies'
     */
    private function humanize($str){
        return preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ' $0', $str);
    }

}
