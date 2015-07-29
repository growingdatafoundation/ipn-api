<?php
namespace Api\Ala\Explore;

use \Api\Ala as Ala;

/**
 * Species aggregator for different ala calls, in future to be extended
 */

class Group extends Ala\AlaBase{

    private $body;
    public $_status = false;
    public $_errors = array();

    public $count = array(
        'total' => null,
        'distinct' => null,
    );

    function __construct($request){
        parent::__construct();
        $this->request($request);
        $this->compile();
    }

    public function request($request){

        // case: module included in occurence aggregator (POST)
        if(isset($request['wkt'])){
            $this->_status = 400;
            $this->count = false;
            $this->_errors[] = 'No polygon requests supported';
            return;
        }

        $request['group_name'] = (!empty($request['group_name'])) ? $request['group_name'] : null;
        $group = $this->validateGroupName($request['group_name']);
        if(!$group){
            $this->_status = 400;
            $this->_errors[] = 'Invalid group name';
            return;
        }

        $curl = new \Api\Curl\Client();
        $response = $curl->get(
            'http://biocache.ala.org.au/ws/explore/counts/group/'.ucfirst($request['group_name']),
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
        if(!is_array($this->body)){
            $this->_status = 520;
            $this->_errors[] = 'Invalid response, array expected';
        }
        if(isset($this->body[0])){
            $this->count['total'] = $this->body[0];
        }
        if(isset($this->body[1])){
            $this->count['distinct'] = $this->body[1];
        }
    }

    /**
     * deals with camelcase labels: 'FernsAndAllies'
     */
    private function validateGroupName($name){
        if(!$name){
            return false;
        }
        $name = ucfirst($name);
        if(!in_array($name, \Api\Config::$speciesGroups)){
            return false;
        }
        return $name;
    }

}
