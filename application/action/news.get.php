<?php
namespace action;
class news{
    
    
	/*
	* url
	*	/index.php?a[]=news, 	/index.php?a[]=news&a[]=index
	*	/index.php/news, 		/index.php/news/index
	*	/news, 				/news/index
	*/
    public function index(){
		$accept	= accept();
        
		$result['status'] 	= 'success';
		$result['message'] 	= 'GET, news!';

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
    
    
    /*
	* url
	*	/index.php?a[]=news&a[]=sport
	*	/index.php/news/sport
	*	/news/sport
	*/
    public function sport(){
		$accept	= accept();
        
		$result['status'] 	= 'success';
		$result['message'] 	= 'GET, news, sport!';

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