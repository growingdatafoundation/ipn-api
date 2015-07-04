<?php
/** 
 * example:
require_once('./Connector.php');

$curl = new Connector();
$result = $curl->get(
	'https://api.flickr.com/services/rest/',
	 array(
	 	'method' => 'flickr.photos.search',
	 	'api_key' => Api_Key,
	 	'tags' => 'test',
	 	'format' => 'json',
	 	'nojsoncallback' => '1'
	 )
	 
);
*/
require_once ('../apiConfig.php');

class Connector {
	
	function __construct() {}

	public function get($uri, $params, $raw = false) {
			
		$endpoint = $uri . '?'.  implode('&', $this->params($params));

		$ch = curl_init();
		$timeout = 60;
		
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		
		$result = curl_exec($ch);
		$this->curlValidate($ch, $endpoint);
		curl_close($ch);

		return (!$raw) ? $this->parse($result) : $result;

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
    private function curlValidate($curl, $endpoint = '') {
        if (curl_errno($curl)) {
            $message = 'Curl error: ' . curl_errno($curl) . ': ' . curl_error($curl).', endpoint:'.$endpoint;
            die($message);
            return false;
        } else {
            $returnCode = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
            switch($returnCode) {
                case 200 :
                    return true;
                    break;
                default :
                    die('HTTP Code '.$returnCode . ', endpoint:'.$endpoint);
                    return false;
                    break;
            }
        }
        return false;
    }//f

}//c
