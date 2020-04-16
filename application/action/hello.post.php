<?php
namespace action;
class hello{
    
    
    public function index(){
        $accept	= accept();
        
		$result['status'] 	= 'success';
		$result['message'] 	= 'POST, hello, world!';

        if($accept === 'application/json'){
            header('Content-Type: application/json');
            die(json_encode($result));
        }else{
            header('Content-Type: application/xml');
            $xml  		= "<xml>\r\n";
            foreach($result as $key=>$value){
                $xml 	.= "  <{$key}>{$value}</{$key}>\r\n";
            }
            $xml     	.= '</xml>';
            die($xml);
        }
    }
    
    
}