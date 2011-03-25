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
//					'unique' => TRUE,
					'rules' => array(
						'not_empty' => array(NULL),
					),
					'label' => 'Email',

				)),
				'password' => Jelly::field('Password', array(
					'in_table' => FALSE,
					//'default' => $pass,
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
						'matches' => array('password'),
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
	 * Validate callback wrapper for checking password match
	 * @param Validate $array
	 * @param string $field
	 * @return void
	 */
	public static function _check_password_matches(Validate $array, $field)
	{
		$auth = Auth::instance();
		if($array['password'] !== $array[$field])
		{
			// Re-use the error messge from the 'matches' rule in Validate
			$array->error($field, 'matches', array('param1' => 'password'));
		}
	}

	public function has_role($role_name)
	{
		$roles = $this->roles->as_array('name', 'id');

		return array_key_exists($role_name, $roles);
	}

} // End Model_User