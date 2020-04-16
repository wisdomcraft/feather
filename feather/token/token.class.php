<?php
class token{
    
    
    public function fetch(){
        $this->fetch_checkusernamepassword();
        
        $string = @implode($_SERVER) . time(). rand();
        $token  = hash('sha256', $string);
        $remote = $_SERVER['REMOTE_ADDR'];
        $updatetime = time();
        $valid  = 1;
        
        $config = config();
        $db     = $config['database'];
        
        $mysqli = new \mysqli($db['host'], $db['user'], $db['password'], $db['database']);
        if(strlen($mysqli->error)){
            $result['status']   = 'error';
            $result['message']  = 'mysql connect error';
            error($result, 'HTTP/1.1 500 Internal Server Error');
        }
        $sql    = "insert into feather_token (token, remote, updatetime, valid) values('{$token}', '{$remote}', '{$updatetime}', '{$valid}')";
        $mysqli->query("SET NAMES 'utf8'");
        $mysqli->query($sql);
        if(strlen($mysqli->error) > 0){
            $mysqli->close();
            $result['status']   = 'error';
            $result['message']  = 'insert data into table failed';
            error($result, 'HTTP/1.1 500 Internal Server Error');
        }
        
        $this->clear_expired_token($mysqli);
        
        $mysqli->close();
        unset($mysqli);
        
        $accept             = accept();
        $result['status']   = 'success';
        $result['message']  = 'fetch token successfully';
        $result['token']    = $token;
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
    
    
    /*
    * check username and password
    * used by fetch()
    */
    private function fetch_checkusernamepassword(){
        $accept         = accept();
        $content_type   = $_SERVER["CONTENT_TYPE"];
        if($content_type !== 'application/json'){
            $result['status']   = 'error';
            $result['message']  = 'content type not allowed in request header';
            error($result);
        }
        
        $json       = trim(file_get_contents("php://input"));
        
        if(strlen($json) < 2){
            $result['status']   = 'error';
            $result['message']  = 'json data is empty';
            error($result);
        }
        
        $array      = json_decode($json, true);
        if(!is_array($array)){
            $result['status']   = 'error';
            $result['message']  = 'post data is not json';
            error($result);
        }
        if(count($array) !== 2){
            $result['status']   = 'error';
            $result['message']  = 'post data syntax is incorrect';
            error($result);
        }
        
        $username       = @$array['username'];
        $password       = @$array['password'];
        
        $config         = config();
        $oauth_username = $config['oauth']['username'];
        $oauth_password = $config['oauth']['password'];
        
        if($username !== $oauth_username || $password !== $oauth_password){
            $result['status']   = 'error';
            $result['message']  = 'username and password are incorrect';
            error($result);
        }
        
        return true;
    }
    
    
    /*
    * check and update token
    */
    public function check(){
        $authorization = @$_SERVER['HTTP_AUTHORIZATION'];
        if(is_null($authorization)){
            $result['status']   = 'error';
            $result['message']  = 'token empty in request header';
            error($result);
        }
        
        @list($bearer, $token) = explode(' ', $authorization);
        if($bearer!=='Bearer' || strlen($token)===0){
            $result['status']   = 'error';
            $result['message']  = 'http authorization is incorrect';
            error($result);
        }

        $config = config();
        $db     = $config['database'];
        
        $mysqli = new \mysqli($db['host'], $db['user'], $db['password'], $db['database']);
        if(strlen($mysqli->error)){
            $result['status']   = 'error';
            $result['message']  = 'mysql connect error';
            error($result, 'HTTP/1.1 500 Internal Server Error');
        }
        $updatetime = time();
        $remote     = $_SERVER['REMOTE_ADDR'];
        $expired    = time() - 7200;
        $sql        = "update feather_token set updatetime={$updatetime} where token='{$token}' and remote='{$remote}' and updatetime>'{$expired}' and valid='1'";
        $mysqli->query("SET NAMES 'utf8'");
        $mysqli->query($sql);
        $affected   = $mysqli->affected_rows;
        if(strlen($mysqli->error) > 0){
            $mysqli->close();
            $result['status']   = 'error';
            $result['message']  = 'update data in database failed';
            error($result, 'HTTP/1.1 500 Internal Server Error');
        }
        
        $mysqli->close();
        unset($mysqli);

        if($affected === 0){
            $result['status']   = 'error';
            $result['message']  = 'token is invalid';
            error($result, 'HTTP/1.1 500 Internal Server Error');
        }elseif($affected === -1){
            $result['status']   = 'error';
            $result['message']  = 'fetch database operation result failed';
            error($result, 'HTTP/1.1 500 Internal Server Error');
        }
    
        $accept = accept();
        $result['status']   = 'success';
        $result['message']  = 'token is valid and updated';
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
    
    
    /*
    * clear expired token in database
    * used in this file, fetch()
    */
    private function clear_expired_token($mysqli){
        $expired    = time() - 7200;
        $sql        = "delete from feather_token where valid!='1' or updatetime<'{$expired}'";
        $mysqli->query($sql);
    }
    
    
    /*
    * check token whether valid or invalid
    * program will run this function before each action
    * used in token.class.php
    */
    public static function token_check(){
        $authorization = @$_SERVER['HTTP_AUTHORIZATION'];
        if(is_null($authorization)){
            $result['status']   = 'error';
            $result['message']  = 'token empty in request header';
            error($result, 'HTTP/1.1 401 Unauthorized');
        }
        
        @list($bearer, $token) = explode(' ', $authorization);
        if($bearer!=='Bearer' || strlen($token)===0){
            $result['status']   = 'error';
            $result['message']  = 'http authorization is incorrect';
            error($result, 'HTTP/1.1 401 Unauthorized');
        }

        $config = config();
        $db     = $config['database'];
        
        $mysqli = new \mysqli($db['host'], $db['user'], $db['password'], $db['database']);
        if(strlen($mysqli->error)){
            $result['status']   = 'error';
            $result['message']  = 'mysql connect error';
            error($result, 'HTTP/1.1 500 Internal Server Error');
        }
        $remote     = $_SERVER['REMOTE_ADDR'];
        $expired    = time() - 7200;
        $sql        = "select count(1) from feather_token where token='{$token}' and remote='{$remote}' and updatetime>'{$expired}' and valid='1'";
        $mysqli->query("SET NAMES 'utf8'");
        $query      = $mysqli->query($sql);
        $count      = $query->fetch_assoc();
        $count      = (int)array_values($count)[0];
        if(strlen($mysqli->error) > 0){
            $mysqli->close();
            $result['status']   = 'error';
            $result['message']  = 'update data in database failed';
            error($result, 'HTTP/1.1 500 Internal Server Error');
        }
        
        $mysqli->close();
        unset($mysqli);
        
        if($count !== 1){
            $result['status']   = 'error';
            $result['message']  = 'token is invalid';
            error($result, 'HTTP/1.1 401 Unauthorized');
        }
    }
    
}