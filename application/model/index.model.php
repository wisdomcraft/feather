<?php
namespace model;
class index{
    
    
    public function index(){
        $result['status']   = 'success';
        $result['message']  = 'hello, world! this is index';
		return $result;
    }
    
    
}