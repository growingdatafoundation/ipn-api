<?php
namespace Api\Curl;

class Response{
    public $status;
    public $body;
    public $errors = array();
    
    function __construct(){
    }
    
    public function error(){
        return ($this->status > 300);
    }
}
