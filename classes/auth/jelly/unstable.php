<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Class Kohana_auth_jelly
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 BlueFish <http://bluefish.ru>
 */
class Auth_Jelly_Unstable extends Auth
{
	protected $_logging = FALSE;

	public function __construct($config = array())
	{
		if(class_exists('Logapp'))
			$this->_logging = TRUE;

		parent::__construct($config);
	}

	/**
	 * Checks if a session is active.
	 *
	 * @param   mixed    role name string, role ORM object, or array with role names
	 * @return  boolean
	 */
	public function logged_in($role = NULL)
	{
		$status = FALSE;
		// Get the user from the session
		$user = $this->get_user();
		if(is_object($user) AND $user instanceof Model_User AND $user->loaded()) {
			// Everything is okay so far
			$status = TRUE;
			if(!empty($role)) {
				// Multiple roles to check
				if(is_array($role)) {
					// Check each role
					foreach($role as $_role)
					{
						if(!is_object($_role)) {
							$_role = Jelly::query('role', array('name' => $_role))->limit(1)->select();
						}
						// If the user doesn't have the role
						if(!$user->has('roles', $_role)) {
							// Set the status false and get outta here
							$status = FALSE;
							break;
						}
					}
				}
					// Single role to check
				else
				{
					if(!is_object($role)) {
						// Load the role
						$role = Jelly::query('role', array('name' => $role))->limit(1)->select();
					}
					// Check that the user has the given role
					$status = $user->has('roles', $role);
				}
			}
		}
		return $status;
	}

	/**
	 * Logs a user in.
	 *
	 * @param   string   username
	 * @param   string   password
	 * @param   boolean  enable auto-login
	 * @return  boolean
	 */
	public function _login($user, $password, $remember)
	{
		// Make sure we have a user object
		$user = $this->_get_object($user);

		// If the passwords match, perform a login
		if ($user->has_role('login') AND $user->password === $password)
		{
			if ($remember === TRUE)
			{
				// Create a new autologin token
				$token = Model::factory('user_token');

				// Set token data
				$token->user = $user->id;
				$token->expires = time() + $this->_config['lifetime'];

				$token->create();

				// Set the autologin Cookie
				Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
			}

			// Finish the login
			$this->complete_login($user);

			return TRUE;
		}

		// Login failed
		return FALSE;
	}

	/**
	 * Forces a user to be logged in, without specifying a password.
	 *
	 * @param   mixed    username string, or user ORM object
	 * @return  boolean
	 */
	public function force_login($user)
	{
		if(!is_object($user)) {
			$username = $user;
			// Load the user
			$user = Jelly::query('user')->where($user->unique_key($username), '=', $username)->select();
		}
		// Mark the session as forced, to prevent users from changing account information
		$this->_session->set('auth_forced', TRUE);
		// Run the standard completion
		$this->complete_login($user);
	}

	/**
	 * Logs a user in, based on the authautologin cookie.
	 *
	 * @return  mixed
	 */
	public function auto_login()
	{
		if($token = Cookie::get('authautologin')) {
			// Load the token and user
			$token = Jelly::query('user_token')->where('token', '=', $token)->limit(1)->select();
			if($token->loaded() AND $token->user->loaded()) {
				if($token->user_agent === sha1(Request::$user_agent)) {
					// Save the token to create a new unique token
					$token->save();
					// Set the new token
					Cookie::set('authautologin', $token->token, $token->expires - time());
					// Complete the login with the found data
					$this->complete_login($token->user);
					// Automatic login was successful
					return $token->user;
				}
				// Token is invalid
				$token->delete();
			}
		}
		return FALSE;
	}

	/**
	 * Gets the currently logged in user from the session (with auto_login check).
	 * Returns FALSE if no user is currently logged in.
	 *
	 * @return  mixed
	 */
	public function get_user($default = NULL)
	{
		$user = parent::get_user($default);
		if($user === FALSE) {
			// check for "remembered" login
			$user = $this->auto_login();
		}
		return $user;
	}

	/**
	 * Log a user out and remove any autologin cookies.
	 *
	 * @param   boolean  completely destroy the session
	 * @param	boolean  remove all tokens for user
	 * @return  boolean
	 */
	public function logout($destroy = FALSE, $logout_all = FALSE)
	{
		// Set by force_login()
		$this->_session->delete('auth_forced');
		if($token = Cookie::get('authautologin')) {
			// Delete the autologin cookie to prevent re-login
			Cookie::delete('authautologin');
			// Clear the autologin token from the database
			$token = Jelly::query('user_token')->where('token', '=', $token)->limit(1)->select();
			if($token->loaded() AND $logout_all) {
				Jelly::query('user_token')->where('user_id', '=', $token->user_id)->delete();
			}
			elseif($token->loaded())
			{
				$token->delete();
			}
		}

		return parent::logout($destroy);
	}

	/**
	 * Get the stored password for a username.
	 *
	 * @param   mixed   username string, or user ORM object
	 * @return  string
	 */
	public function password($user)
	{
		if(!is_object($user)) {
			$username = $user;

			// Load the user
			$user = Jelly::query('user')
			->where(':name_key', '=', $username)
			->limit(1)
			->select();
		}
		return $user->password;
	}

	/**
	 * Complete the login for a user by incrementing the logins and setting
	 * session data: user_id, username, roles.
	 *
	 * @param   object  user ORM object
	 * @return  void
	 */
	/**
	 * Complete the login for a user by incrementing the logins and saving login timestamp
	 *
	 * @return void
	 */
	protected function complete_login($user)
	{
		if(!$user->loaded()) {
			// nothing to do
			return;
		}
		$user->logins += 1;
		$user->last_login = time();
		try
		{
			$user->save();
			return parent::complete_login($user);
		}
		catch(Validate_Exception $e)
		{
			exit(Kohana::debug($e->array()->message('save_error')));
		}
	}

	/**
	 * Compare password with original (hashed). Works for current (logged in) user
	 *
	 * @param   string  $password
	 * @return  boolean
	 */
	public function check_password($password)
	{
		$user = $this->get_user();
		if($user === FALSE) {
			// nothing to compare
			return FALSE;
		}
		$hash = $this->hash_password($password, $this->find_salt($user->password));
		return $hash == $user->password;
	}

	/**
	 * Convert a unique identifier string to a user object
	 *
	 * @param mixed $user
	 * @return Model_User
	 */
	protected function _get_object($user)
	{
		static $current;

		//make sure the user is loaded only once.
		if ( ! is_object($current) AND is_string($user))
		{
			// Load the user
			$current = Jelly::query('user')->where(':name_key', '=', $user)->limit(1)->select();
		}

		if ($user instanceof Model_User AND $user->loaded())
		{
			$current = $user;
		}

		return $current;
	}
} // End Auth ORM