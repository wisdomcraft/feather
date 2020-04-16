<?php
class route{
    
    
    public function __construct(){
        $actions    = action();
        
        $method     = $_SERVER['REQUEST_METHOD'];
        if(!in_array($method, array('GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD'))){
            $result['response']['status']   = 'error';
            $result['response']['message']  = 'method not allowed';
            die(json_encode($result));
        }
        $method     = strtolower($method);
        
        if(count($actions)===2 && $actions[0]==='token' && in_array($actions[1], array('fetch', 'check')) && $method===strtolower('POST')){
            $function   = $actions[1];
            $file       = LIBRARY_PATH . 'token/token.class.php';
            $class      = 'token';
        }else{
            $function   = end($actions);
            array_pop($actions);
            $file       = APP_PATH . 'action/' . implode('/', $actions) . ".{$method}.php";
            $class      = 'action\\' . implode('\\', $actions);
            
            if(!class_exists('token')) require LIBRARY_PATH . 'token/token.class.php';
            (new token)::token_check();
        }

        $accept         = accept();

        if(!file_exists($file)){
            header('HTTP/1.1 404 Not Found');
            $result['status']   = 'error';
            $result['message']  = 'action file not exists';
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
                $xml    = "<xml>\r\n";
                foreach($result as $key=>$value){
                    $xml.= "  <{$key}>{$value}</{$key}>\r\n";
                }
                $xml    .= '</xml>';
                die($xml);
            }
        }
        
        $object = new $class;
        $object->$function();        
    }
    
    
}