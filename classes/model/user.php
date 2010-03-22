<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Jelly Model user
 *
 * @author avis <smgladkovskiy@gmail.com>
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
			->fields(array(
			'id' => new Field_Primary(array(
				'in_table' => TRUE,
				'in_form' => FALSE,
			)),
			'email' => new Field_Email(array(
				'unique' => TRUE,
				'rules' => array(
					'not_empty' => array(TRUE),
					'email' => NULL
				),
				'in_table' => TRUE,
				'in_form' => TRUE,
			)),
			'name' => new Field_String(array(
				'in_table' => TRUE,
				'label' => __('Имя'),
				'in_form' => TRUE,
			)),
			'password' => new Field_Password(array(
				'label' => __('Пароль'),
				'hash_with' => array(A1::instance(), 'hash_password'),
				'rules' => array(
					'not_empty' => array(TRUE),
					'max_length' => array(50),
					'min_length' => array(4)
				),
				'in_table' => FALSE,
				'in_form' => TRUE,
			)),
			'password_confirm' => new Field_Password(array(
				'label' => __('Подтверждение пароля'),
				'in_db' => FALSE,
				'callbacks' => array(
					'matches' => array('Model_User', '_check_password_matches')
				),
				'rules' => array(
					'not_empty' => array(TRUE),
					'max_length' => array(50),
					'min_length' => array(4),
				),
				'in_table' => FALSE,
				'in_form' => TRUE,
			)),
			'token' => new Field_String(array(
				'in_table' => FALSE,
				'in_form' => FALSE,
			)),
			'logins' => new Field_Integer(array(
				'default' => 0,
				'in_table' => FALSE,
				'in_form' => FALSE,
				'label' => __('Количество входов')
			)),
			'last_login' => new Field_Timestamp(array(
				'in_table' => TRUE,
				'in_form' => FALSE,
				'pretty_format' => 'd.m.Y',
				'label' => __('Последний вход')
			)),
//			'roles' => new Field_ManyToMany
		));
    }

	/**
	 * Validate callback wrapper for checking password match
	 * @param Validate $array
	 * @param string   $field
	 * @return void
	 */
	public static function _check_password_matches(Validate $array, $field)
	{
		if(!isset($array[$field]))
		{
			$array->error($field, 'matches', array('param1' => 'password'));
		}
		if ($array['password'] !== $array[$field])
		{
			// Re-use the error messge from the 'matches' rule in Validate
			$array->error($field, 'matches', array('param1' => 'password'));
		}
	}

	/**
	 * Check if user has a particular role
	 * @param mixed $role 	Role to test for, can be Model_Role object, string role name of integer role id
	 * @return bool			Whether or not the user has the requested role
	 */
	public function has_role($role)
	{
		// Check what sort of argument we have been passed
		if ($role instanceof Model_Role)
		{
			$key = 'id';
			$val = $role->id;
		}
		elseif (is_string($role))
		{
			$key = 'name';
			$val = $role;
		}
		else
		{
			$key = 'id';
			$val = (int) $role;
		}

		foreach ($this->roles as $user_role)
		{
			if ($user_role->{$key} === $val)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

} // End Jelly Model user