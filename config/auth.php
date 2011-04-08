<?php defined('SYSPATH') or die('No direct access allowed.');

return array(

	'types' => array(
		'default' => 'login',
		'admin' => 'admin',
	),

	'driver'       => 'Jelly',
	'hash_method'  => 'sha256',
	'hash_key'     => '',
	'lifetime'     => 1209600,
	'session_key'  => 'auth_user',

	// Username/password combinations for the Auth File driver
	'users' => array(
		// 'admin' => 'b3154acf3a344170077d11bdb5fff31532f679a1919e716a02',
	),

);