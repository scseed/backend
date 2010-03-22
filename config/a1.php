<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(

	/**
	 * Set the ORM driver.
	 * ORM, Sprig and Mango are available.
	 * The default - Kohana ORM.
	 */
	'driver' => 'Jelly',

	/**
	 * Type of hash to use for passwords. Any algorithm supported by the hash function
	 * can be used here. Note that the length of your password is determined by the
	 * hash type + the number of salt characters.
	 * @see http://php.net/hash
	 * @see http://php.net/hash_algos
	 */
	'hash_method' => 'sha1',

	/**
	 * Defines the hash offsets to insert the salt at. The password hash length
	 * will be increased by the total number of offsets.
	 */
	'salt_pattern' => '3, 6, 9, 10, 11, 20, 22, 25, 28, 30',

	/**
	 * Set the auto-login (remember me) cookie lifetime, in seconds. The default
	 * lifetime is two weeks.
	 */
	'lifetime' => 1209600,

	/**
	 * User model
	 */
	'user_model' => 'user',
 
	/**
	 * Table column names
	 */
	'columns' => array(
		'pk'        =>  'id',        // table primary key name
		'username'  => 'email',   // username or field to login user by
		'password'  => 'password',   // password field
		'token'     => 'token',      // token field name
		'last_login'=> 'last_login', // last login (optional)
		'logins'    => 'logins'      // login count (optional)
	),

	/**
	 * Session type - native or database
	 */
	'session_type' => 'native'
);
