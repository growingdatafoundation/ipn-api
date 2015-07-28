<?php
namespace Api\Ala\Species;

use \Api\Ala as Ala;

/**
 * http://bie.ala.org.au/ws/species/{guid}.json
 */

class Details extends Ala\AlaBase{
    private $body;
    public $_status = false;

    public $name;
    public $isAustralian;
    public $classification = array();
    public $commonNames = array();
    public $conservationStatuses = array();
    public $descriptions = array();
    public $references = array();
    public $images = array();

    function __construct($request){
        parent::__construct();
        $this->request($request);
        $this->compile();
    }

    public function request($request){
        $curl = new \Api\Curl\Client();
        $response = $curl->get(
            'http://bie.ala.org.au/ws/species/'.$request['guid']
        );
        $this->_status = $response->status;
        $this->body = $response->body;
    }

    public function compile(){
        $this->name();
        $this->isAustralian  = $this->property('isAustralian', $this->body);
        $this->classification();
        $this->commonNames();
        $this->conservationStatuses();
        $this->simpleProperties();
        $this->images();
    }

    private function name(){
        if(isset($this->body->classification)){
            $this->name = $this->property('species', $this->body->taxonName);
        }
        if(!$this->name && isset($this->body->taxonName)){
            $this->name = $this->property('nameComplete', $this->body->taxonName);
        }
        if(!$this->name && isset($this->body->taxonConcept)){
            $this->name = $this->property('nameString', $this->body->taxonConcept);
        }
    }

    private function classification(){
        if(!isset($this->body->classification)){
            return;
        }
        $this->classification['kingdom']    = $this->property('kingdom',    $this->body->classification);
        $this->classification['phylum']     = $this->property('phylum',     $this->body->classification);
        $this->classification['class_']     = $this->property('clazz',      $this->body->classification);
        $this->classification['order']      = $this->property('order',      $this->body->classification);
        $this->classification['family']     = $this->property('family',     $this->body->classification);
        $this->classification['genus']      = $this->property('genus',      $this->body->classification);
        $this->classification['species']    = $this->property('species',    $this->body->classification);
    }

    private function commonNames(){
        if(empty($this->body->commonNames)){
            return;
        }
        foreach((array)$this->body->commonNames as $name){
            if(isset($name->isBlackListed) && $name->isBlackListed){
                continue;
            }
            if(empty($name->nameString)){
                continue;
            }
            $this->commonNames[] = $name->nameString;
        }
    }

    private function conservationStatuses(){
        if(empty($this->body->conservationStatuses)){
            return;
        }
        foreach((array)$this->body->conservationStatuses as $status){
            if(empty($status->region)){
                continue;
            }
            $val = new \StdClass;
            $val->system = $this->property('system', $status);
            $val->status = $this->property('status', $status);
            $val->status = $this->property('rawStatus', $status);
            $val->rawCode = $this->property('rawCode', $status);
            $this->conservationStatuses[$status->region] = $val;
        }

    }

    private function simpleProperties(){
        if(empty($this->body->simpleProperties)){
            return;
        }
        foreach((array)$this->body->simpleProperties as $item){
            if(!isset($item->name)){
                return;
            }
            switch($item->name){
                case 'http://ala.org.au/ontology/ALA#hasDescriptiveText':
                    $this->_description('Description', $item);
                break;
                case 'http://ala.org.au/ontology/ALA#hasDistributionText':
                    $this->_description('Distribution', $item);
                break;
                case 'http://ala.org.au/ontology/ALA#hasFloweringSeason':
                    $this->_description('Flowering Season', $item);
                break;
                case 'http://ala.org.au/ontology/ALA#hasReference':
                    $this->references[] = $this->_reference($item);
                break;
                case 'http://ala.org.au/ontology/ALA#hasMorphologicalText':
                    $this->_description('Morphology', $item);
                break;
                case 'http://ala.org.au/ontology/ALA#hasConservationText':
                    $this->_description('Conservation', $item);
                break;
                case 'http://ala.org.au/ontology/ALA#hasHabitatText':
                    $this->_description('Habitat', $item);
                break;
                case 'http://ala.org.au/ontology/ALA#hasDietText':
                    $this->_description('Diet', $item);
                break;
            }
        }

    }

    private function images(){
        if(empty($this->body->images)){
            return;
        }
        foreach((array)$this->body->images as $image){
            $img = new \StdClass;
            $img->source = $this->property('sourceName', $image);
            $img->contentType = $this->property('contentType', $image);
            $img->thumbnail = $this->property('thumbnail', $image);
            $img->title = $this->property('title', $image);

            $lg  = $this->property('largeImageUrl', $image);
            if(!$lg){
                $lg  = $this->property('identifier', $image);
            }

            $img->thumbnail = $this->property('thumbnail', $image);
            $this->images[] = $img;
        }
    }

    private function _reference($item){
        $r = new \StdClass;
        $r->source= $this->property('infoSourceName', $item, '');
        $r->title = $this->property('title', $item, '');
        $r->url = $this->property('identifier', $item, '');
        return $r;
    }

    private function _description($key, $item){
        if(!isset($this->descriptions[$key])){
            $this->descriptions[$key] = '';
        }
        $this->descriptions[$key] = $this->property('value', $item, '');
    }
}
