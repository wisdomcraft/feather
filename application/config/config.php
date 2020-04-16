<?php
return array(
	'database' 	=> array(
		'host' 		=> '172.31.169.89',
		'user' 		=> 'feather_user',
		'password' 	=> 'Nns<:%6-',
	),
	'url'			=> 'api',	//native or api
	'api'			=> array(
        '/hello'			    => array('hello'),
		'/news'					=> array('news'),
		'/news/sport'			=> array('news', 'sport'),
		'/news/sport/football'	=> array('news', 'sport', 'football'),
	),
);