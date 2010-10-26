<?php defined('SYSPATH') or die('No direct access allowed.');
abstract class Auth extends Kohana_Auth
{

	// Auth instances
	protected static $_instances;
	protected static $_type;

	/**
	 * Singleton pattern
	 *
	 * @return Auth
	 */
	public static function instance($type = 'default')
	{
		if(!isset(Auth::$_instances[$type])) {
			// Load the configuration for this type
			$config = Kohana::config('auth');
			Auth::$_type = $config['types'][$type];
			if(!$driver = $config->get('driver')) {
				$driver = 'ORM';
			}
			// Set the session class name
			$class = 'Auth_' . ucfirst($driver);
			// Create a new session instance
			Auth::$_instances[$type] = new $class($config);
		}
		return Auth::$_instances[$type];
	}

	/**
	 * Loads Session and configuration options.
	 *
	 * @return  void
	 */
	public function __construct($config = array())
	{
		// Clean up the salt pattern and split it into an array
		if(!is_array($config->get('salt_pattern')))
			$config['salt_pattern'] = preg_split('/,\s*/', $config->get('salt_pattern'));
		// Save the config in the object
		$this->_config = $config;
		$this->_session = Session::instance();
	}
}