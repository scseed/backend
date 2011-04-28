<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * User Model for Jelly ORM
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright
 */
class Model_User extends Jelly_Model {

	/**
	 * Initializating model meta information
	 *
	 * @param Jelly_Meta $meta
	 */
	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('users')
			->name_key('email')
			->fields(array(
				'id' => Jelly::field('Primary'),
				'user_data' => Jelly::field('BelongsTo', array(
					'allow_null' => true,
					'default' => NULL,
					'label' => 'ФИО пользователя',
					'in_form' => FALSE,
					'in_table' => FALSE,
				)),
				'email' => Jelly::field('Email', array(
					'rules' => array(
						'not_empty' => array(NULL),
					),
					'label' => 'Email',

				)),
				'password' => Jelly::field('Password', array(
					'in_table' => FALSE,
					'hash_with' => array(Auth::instance(), 'hash_password'),
					'rules' => array(
						'not_empty' => array(NULL),
						'max_length' => array(50),
						'min_length' => array(4)
					),
					'label' => 'Пароль'
				)),
				'password_confirm' => Jelly::field('Password', array(
					'in_form' => TRUE,
					'in_table' => FALSE,
					'in_db' => FALSE,
					'rules' => array(
						'matches' => array(':validation', 'password_confirm', 'password'),
						'not_empty' => array(NULL),
						'max_length' => array(50),
						'min_length' => array(4)
					),
					'label' => 'Подтверждение пароля'
				)),
				'tokens' => Jelly::field('HasMany', array(
					'in_form' => FALSE,
					'in_table' => FALSE,
				)),
				'is_active' => Jelly::field('Boolean', array(
					'label' => 'Статус',
					'label_true' => 'Активен',
					'label_false' => 'Отключён',
					'default' => TRUE
				)),
				'roles' => Jelly::field('ManyToMany', array(
					'label' => 'Роли пользователя',
				)),
			));
	}

	/**
	 * Complete the login for a user by incrementing the logins and saving login timestamp
	 *
	 * @return void
	 */
	public function complete_login()
	{
		if ($this->_loaded)
		{
			// Update the number of logins
			$this->logins = $this->logins + 1;

			// Set the last login date
			$this->last_login = time();

			// Save the user
			$this->save();
		}
	}

	/**
	 * Allows a model use both email and username as unique identifiers for login
	 *
	 * @param   string  unique value
	 * @return  string  field name
	 */
	public function unique_key($value)
	{
		return Valid::email($value) ? 'email' : 'username';
	}

	/**
	 * Password validation for plain passwords.
	 *
	 * @param array $values
	 * @return Validation
	 */
	public static function get_password_validation($values)
	{
		return Validation::factory($values)
			->rule('password', 'min_length', array(':value', 8))
			->rule('password_confirm', 'matches', array(':validation', ':field', 'password'));
	}

	/**
	 * Create a new user
	 *
	 * Example usage:
	 * ~~~
	 * $user = Jelly::factory('user')->create_user($_POST, array(
	 *	'username',
	 *	'password',
	 *	'email',
	 * );
	 * ~~~
	 *
	 * @param array $values
	 * @param array $expected
	 * @throws Validation_Exception
	 */
	public function create_user($values, $expected)
	{
		// Validation for passwords
		$extra_validation = Model_User::get_password_validation($values);

		return $this->set(Arr::extract($values, $expected))->save($extra_validation);
	}

	/**
	 * Update an existing user
	 *
	 * [!!] We make the assumption that if a user does not supply a password, that they do not wish to update their password.
	 *
	 * Example usage:
	 * ~~~
	 * $user = Jelly::factory('user', 1)
	 *	->update_user($_POST, array(
	 *		'username',
	 *		'password',
	 *		'email',
	 *	);
	 * ~~~
	 *
	 * @param array $values
	 * @param array $expected
	 * @throws Validation_Exception
	 */
	public function update_user($values, $expected)
	{
		if (empty($values['password']))
		{
			unset($values['password'], $values['password_confirm']);
		}

		// Validation for passwords
		$extra_validation = Model_User::get_password_validation($values);

		return $this->set(Arr::extract($values, $expected))->save($extra_validation);
	}

	/**
	 * Is the model has specified role
	 *
	 * @param string|null $role_name
	 * @return bool
	 */
	public function has_role($role_name = NULL)
	{
		$roles = $this->roles->as_array('name', 'id');

		return array_key_exists($role_name, $roles);
	}

} // End Model_User