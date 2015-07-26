<?php
namespace Api;
use \Api\Curl as Curl;

/** 
 * Example:
    $curl = \Api\new Connector();
    $result = $curl->get(
        'https://api.flickr.com/services/rest/',
        array(
            'method' => 'flickr.photos.search',
            'api_key' => \Api\Config::$Flickr['key'],
            'tags' => 'test',
            'format' => 'json',
            'nojsoncallback' => '1'
        )
        
    );
*/

class Connector{
    
    public $response;
    
    function __construct() {
    }
    
    public $error;

    public function get($uri, $params = null, $raw = false) {
        
        //reset response
        $this->response = new Curl\Response();
        
        //endpoint
        $endpoint = $this->endpoint($uri, $params);
        
        //curl
        $ch = $this->curlInit($endpoint);
        
        //response
        $result = curl_exec($ch);
        $this->validate($ch, $endpoint);
        $this->response->body = (!$raw) ? $this->parse($result) : $result;
        curl_close($ch);
        return $this->response;

    }

    public function post($uri, $params = null, $data, $raw = false) {
        
        //reset response
        $this->response = new Curl\Response();
        
        //endpoint
        $endpoint = $this->endpoint($uri, $params);
        
        //curl
        $json = json_encode($data);
        $ch = $this->curlInit($endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($json))                                                                       
        );  
        //response
        $result = curl_exec($ch);
        $this->validate($ch, $endpoint);
        $this->response->body = (!$raw) ? $this->parse($result) : $result;
        curl_close($ch);
        return $this->response;

    }
  
    private function curlInit($endpoint){
        $ch = curl_init();
        $timeout = 60;
        
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        return $ch;
    }
    
    private function endpoint($uri, $params = null){
        $q = null;
        if(is_array($params) && !empty($params)){
            $q = '?'.  implode('&', $this->params($params));
        }
        return $uri .$q;
    }
    
    private function params($params = array()){
        $encoded = array();
        foreach ($params as $k => $v){
             $encoded[] = urlencode($k).'='.urlencode($v); 
        }
        return $encoded;
    }
    
    private function parse($result){
        return json_decode($result);
    }

    /**
     * validates curl
     * @param $curl: curl resource
     * @param string $method: invoking method
     * @param string $json: json string
     * @return: true or false
     */
    private function validate($curl, $endpoint = '') {
        $this->response->status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl)) {
            $this->response->errors[] = 'Curl error: ' . curl_errno($curl) . ': ' . curl_error($curl).', endpoint:'.$endpoint;
            return false;
        }
        
        switch($this->response->status) {
            case 200 :
                return true;
            default :
                return false;
        }
        
        return false;
        
    }//f

}//c
