<?php
namespace Api\Ala;

/**
 * Base class for Ala
 */

class AlaBase{

    function __construct(){
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

    protected function property($property, $item){
        return (isset($item->{$property})) ? $item->{$property} : null;
    }

}
