<?php
namespace action;
class index{
    
    
	/*
	* url
	*	/index.php
	*	/index.php?a[]=index, 	/index.php?a[]=index&a[]=index
	*	/index.php/index, 		/index.php/index/index
	*	/index, 				/index/index
	*/
    public function index(){
		$accept	= accept();
        $result	= model('index');

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