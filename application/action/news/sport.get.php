<?php
namespace action/news;
class sport{
    
    
    /*
	* url
	*	/index.php?a[]=news&a[]=sport&a[]=football
	*	/index.php/news/sport/football
	*	/news/sport/football
	*/
    public function football(){
        echo 'GET, news sport football!';
    }
    
    
    /*
	* url
	*	/index.php?a[]=news&a[]=sport&a[]=basketball
	*	/index.php/news/sport/basketball
	*	/news/sport/basketball
	*/
    public function basketball(){
        echo 'GET, news sport basketball!';
    }
    
    
    /*
	* url
	*	/index.php?a[]=news&a[]=sport&a[]=swim
	*	/index.php/news/sport/swim
	*	/news/sport/swim
	*/
    public function swim(){
        echo 'GET, news sport swim!';
    }
    
    
}