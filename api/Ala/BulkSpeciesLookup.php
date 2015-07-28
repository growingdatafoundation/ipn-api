<?php
namespace Api\Ala;

class BulkSpeciesLookup extends AlaBase{

    private $body;
    public $_status = false;
    public $species = array();

    function __construct($request){
        parent::__construct();
        $names = self::parseCommaSeparatedParam($request, 'taxon_name');

        $this->request($names);
        $this->compile();
    }

    public function request($names){
        $curl = new \Api\Curl\Client();
        $response = $curl->post(
            'http://bie.ala.org.au/ws/species/lookup/bulk',
            null,
            array('names' => $names)
        );
        $this->_status = $response->status;
        $this->body = $response->body;
    }

    //Genus:Acacia => totalRecords:107, common_name results: 100, raw_taxonomy_name results 107!
    public function compile(){
        foreach($this->body as $item){
            $this->species[$item->name]                = new \StdClass;
            $this->species[$item->name]->guid          = $this->property('guid', $item);
            $this->species[$item->name]->common_name   = $this->property('commonNameSingle', $item);
            $this->species[$item->name]->isAustralian  = $this->property('isAustralian', $item);
            $this->species[$item->name]->image         = $this->property('image', $item);
            $this->species[$item->name]->thumbnail     = $this->property('thumbnail', $item);
        }
    }
}
