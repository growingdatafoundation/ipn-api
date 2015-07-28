<?php
namespace Api;

class Utils{
    /**
     * @TODO: radius => zoom
     */
    public static function googleMapsUri($latitude, $longitude){
        return 'http://maps.google.com/?q='.$latitude.','.$longitude;
    }

    public static function modulesToNamespaces($modulesCommaSeparated){

        $r = array();

        $modules = explode(',', $modulesCommaSeparated);

        for($i = 0; $i < count($modules); $i++){
            $modules[$i] = trim($modules[$i]);
            $namespaces = explode('.', $modules[$i]);
            for($k = 0; $k < count($namespaces); $k++){
                $namespaces[$k] = ucfirst($namespaces[$k]);
            }
            $r[] = '\\Api\\'.implode('\\', $namespaces);
        }

        return $r;
    }

}//c
