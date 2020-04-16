<?php

/*
* common function
* used in this file, function action(){...}, function model(){...}
* used in route.class.php
*/
function accept(){
    if($_SERVER['HTTP_ACCEPT'] === 'application/xml'){
        return 'application/xml';
    }else{
        return 'application/json';
    }
}


/*
* common function
* fetch all actions as an array
* used in route.class.php
*/
function action(){
    $accept = accept();
    
    $config = config();
    $url    = $config['url'];
    if(!in_array($url, array('native', 'api'))){
        $result['status']   = 'error';
        $result['message']  = 'url variable incorrect in config';
        if($accept === 'application/json'){
            header('Content-Type: application/json');
            die(json_encode($result));
        }else{
            header('Content-Type: application/xml');
            $xml     = "<xml>\r\n";
            foreach($result as $key=>$value){
                $xml .= "  <{$key}>{$value}</{$key}>\r\n";
            }
            $xml     .= '</xml>';
            die($xml);
        }
    }
    
    if($url === 'native'){
        $actions           = @$_GET['a'];
        if(is_null($actions)){
            $actions    = array();
        }
        if(!is_array($actions)){
            header('HTTP/1.1 500 Internal Server Error');
            $result['status']   = 'error';
            $result['message']  = 'action is not array in url';
            if($accept === 'application/json'){
                header('Content-Type: application/json');
                die(json_encode($result));
            }else{
                header('Content-Type: application/xml');
                $xml     = "<xml>\r\n";
                foreach($result as $key=>$value){
                    $xml .= "  <{$key}>{$value}</{$key}>\r\n";
                }
                $xml     .= '</xml>';
                die($xml);
            }
        }
        if(count($actions) === 0){
            $actions[0] = 'index';
            $actions[1] = 'index';
        }elseif(count($actions) === 1){
            $actions[1] = 'index';
        }
        return $actions;
    }else{
        $pathinfo = $_SERVER['PATH_INFO'];
        if(is_null($pathinfo)){
            header('HTTP/1.1 500 Internal Server Error');
            $result['status']   = 'error';
            $result['message']  = 'PATH_INFO not enabled in server';
            if($accept === 'application/json'){
                header('Content-Type: application/json');
                die(json_encode($result));
            }else{
                header('Content-Type: application/xml');
                $xml     = "<xml>\r\n";
                foreach($result as $key=>$value){
                    $xml .= "  <{$key}>{$value}</{$key}>\r\n";
                }
                $xml     .= '</xml>';
                die($xml);
            }
        }
        if(strlen($pathinfo) === 0 || @$_SERVER['PHP_SELF']==='PATH_INFO'){
            $actions[0] = 'index';
            $actions[1] = 'index';
        }else{
            $api = $config['api'];
            if(@is_null($api[$pathinfo])){
                header('HTTP/1.1 500 Internal Server Error');
                $result['status']   = 'error';
                $result['message']  = 'api not exist in config';
                if($accept === 'application/json'){
                    header('Content-Type: application/json');
                    die(json_encode($result));
                }else{
                    header('Content-Type: application/xml');
                    $xml     = "<xml>\r\n";
                    foreach($result as $key=>$value){
                        $xml .= "  <{$key}>{$value}</{$key}>\r\n";
                    }
                    $xml     .= '</xml>';
                    die($xml);
                }
            }else{
                $actions = $api[$pathinfo];
                if(!is_array($actions) || @count($actions)===0){
                    header('HTTP/1.1 500 Internal Server Error');
                    $result['status']   = 'error';
                    $result['message']  = 'api incorrect in config';
                    if($accept === 'application/json'){
                        header('Content-Type: application/json');
                        die(json_encode($result));
                    }else{
                        header('Content-Type: application/xml');
                        $xml     = "<xml>\r\n";
                        foreach($result as $key=>$value){
                            $xml .= "  <{$key}>{$value}</{$key}>\r\n";
                        }
                        $xml     .= '</xml>';
                        die($xml);
                    } 
                }
                if(count($actions)===1) $actions[1] = 'index';
            }
        }
        return $actions;
    }
}


/*
* common function
* run model in action file
*/
function model($function){
    $actions    = action();
	array_pop($actions);
	$file       = APP_PATH . 'model/' . implode('/', $actions) . ".model.php";
	$class      = 'model\\' . implode('\\', $actions);
	
	$accept 	= accept();
	
	if(!file_exists($file)){
		header('HTTP/1.1 404 Not Found');
		$result['status']   = 'error';
		$result['message']  = 'model file not exists';
		if($accept === 'application/json'){
			header('Content-Type: application/json');
			die(json_encode($result));
		}else{
			header('Content-Type: application/xml');
			$xml     = "<xml>\r\n";
			foreach($result as $key=>$value){
				$xml .= "  <{$key}>{$value}</{$key}>\r\n";
			}
			$xml     .= '</xml>';
			die($xml);
		}
	}
	
	if(!class_exists($class)){
		require $file;
	}
	
	if(!class_exists($class)){
		header('HTTP/1.1 500 Internal Server Error');
		$result['status']   = 'error';
		$result['message']  = 'program developed incorrect';
		if($accept === 'application/json'){
			header('Content-Type: application/json');
			die(json_encode($result));
		}else{
			header('Content-Type: application/xml');
			$xml	= "<xml>\r\n";
			foreach($result as $key=>$value){
				$xml.= "  <{$key}>{$value}</{$key}>\r\n";
			}
			$xml 	.= '</xml>';
			die($xml);
		}
	}
	
	$object	= new $class;
	return $object->$function();    
}


/*
* fetch config variable
* used in token.class.php
*/
function config(){
	$file1 		= LIBRARY_PATH . 'common/config.php';
	$config1 	= require $file1;
	$file2 		= APP_PATH . 'config/config.php';
	if(!file_exists($file2)){
		return $config1;
	}else{
		$config2 	= require $file2;
	}
	
	foreach($config2 as $key=>$value){
		if(@is_null($config1[$key])){
			$config1[$key] = $value;
			continue;
		}
        if(!is_array($value) && !is_null($value)){
            $config1[$key] = $value;
            continue;
        }
		foreach($value as $key2=>$value2){
			$config1[$key][$key2] = $value2;
		}
	}
	
	return $config1;
}


/*
* die error
* json, {"status":"error", "message":"..."}
* xml, <xml><status>error</status><message>...</message></xml>
* used in token.class.php
*/
function error($result, $http=null){
    $accept     = accept();
    if(!is_null($http)) header($http);
    if($accept === 'application/json'){
        header('Content-Type: application/json');
        die(json_encode($result));
    }else{
        header('Content-Type: application/xml');
        $xml    = "<xml>\r\n";
        foreach($result as $key=>$value){
            $xml.= "  <{$key}>{$value}</{$key}>\r\n";
        }
        $xml    .= '</xml>';
        die($xml);
    }
}