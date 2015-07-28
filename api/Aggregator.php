<?php
namespace Api;

class Aggregator{

    function __construct(){
    }

    /**
     * set data from a module
     */
    public function set($module, $result){
        $tree = explode('.', $module);
        $pointer = $this->_register($tree, $this, $result);
    }

    private function _register($arr, $pointer, $result){
        $key = trim(array_shift($arr));
        if(!isset($pointer->{$key})){
            $pointer->{$key} = new \StdClass;
        }
        if(count($arr) > 0){
            return $this-> _register($arr, $pointer->{$key}, $result);
        }
        $pointer->{$key} = $result;
    }

    public function parseModules($modulesCommaSeparated){
        $modules = explode(',', $modulesCommaSeparated);
        for($i = 0; $i < count($modules); $i++){
            $modules[$i] = trim($modules[$i]);
        }
        return $modules;
    }

    public function moduleToNamespacedClass($module){
        $namespaces = explode('.', $module);
        for($k = 0; $k < count($namespaces); $k++){
            $namespaces[$k] = ucfirst($namespaces[$k]);
        }
        return '\\Api\\'.implode('\\', $namespaces);
    }

}
