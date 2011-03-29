<?php defined('SYSPATH') or die('No direct access allowed.');

return array(

	'driver'       => 'Jelly_unstable',
	'hash_method'  => 'sha1',
	'salt_pattern' => '3, 4, 7, 10, 12, 15, 18, 20, 23, 25, 27, 30',
	'lifetime'      => 1209600,
	'session_key'   => 'auth_user',
	'autologin_key' => 'auth_autologin',
	'forced_key'    => 'auth_forced',

	// Username/password combinations for the Auth File driver
	'users' => array(
		// 'admin' => 'b3154acf3a344170077d11bdb5fff31532f679a1919e716a02',
	),

);