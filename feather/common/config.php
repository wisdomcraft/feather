<?php
return array(
	'oauth'		=> array(
		'username'	=> 'admin',
		'password'	=> '123456',
	),
	'database' 	=> array(
		'type' 		=> 'mysql',
		'host' 		=> '127.0.0.1',
		'database' 	=> 'feather',
		'user' 		=> 'root',
		'password' 	=> '',
		'port' 		=> 3306,
	),
	'url'			=> 'native',	//native or api
	'api'			=> array(
		'/token/fetch'	=> array('token', 'fetch'),
		'/token/check'	=> array('token', 'check'),
	),
);