<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * User Model for Jelly ORM
 *
 * @package Backend
 * @author avis <smgladkovskiy@gmail.com>
 */
abstract class Model_Core_User extends Model_Auth_User {

	/**
	 * Initializating model meta information
	 *
	 * @param Jelly_Meta $meta
	 */
	public static function initialize(Jelly_Meta $meta)
	{
		parent::initialize($meta);

		$meta->name_key('email')
			->fields(array(
				'id' => Jelly::field('Primary'),
				'is_active' => Jelly::field('Boolean', array(
					'label'       => __('Статус'),
					'label_true'  => __('Активен'),
					'label_false' => __('Отключён'),
					'default'     => TRUE
				)),
				'date_create' => Jelly::field('Timestamp', array(
					'auto_now_create' => TRUE,
					'in_form'         => FALSE,
					'label'           => __('Дата создания'),
				)),
				'date_update' => Jelly::field('Timestamp', array(
					'auto_now_update' => TRUE,
					'in_form'         => FALSE,
					'in_table'        => FALSE,
					'label'           => __('Дата обновления'),
				)),
				'user_data'   => Jelly::field('BelongsTo', array(
					'allow_null' => TRUE,
					'default'    => NULL,
					'label'      => __('Данные пользователя'),
					'in_table'   => FALSE,
				)),
			));

		// Disable 'username' field
		$meta->field('username', 'String', array('in_db' => FALSE));
		$meta->field('email', 'Email', array(
			'label' => __('Email'),
			'rules' => array(
				array('not_empty'),
			),
			'unique' => TRUE,
		));
		$meta->field('logins', 'Integer', array(
			'in_form'  => FALSE,
			'in_table' => FALSE,
		));
		$meta->field('password', 'Password', array(
			'in_table' => FALSE,
			'label'    => __('Пароль'),
			'rules'    => array(
				array('not_empty'),
				array('min_length', array(':value', 8)),
			),
			'hash_with' => array(Auth::instance(), 'hash'),
		));
		$meta->field('roles', 'ManyToMany', array(
			'label' => __('Роли пользователя'),
		));
		$meta->field('user_tokens', 'HasMany', array(
			'in_form'  => FALSE,
			'in_table' => FALSE,
		));
		$meta->field('last_login', 'Timestamp', array(
			'in_form'  => FALSE,
			'in_table' => FALSE,
		));
	}

	public function unique_key($value)
	{
		return 'email';
	}

	/**
	 * Loads a user based on unique key.
	 *
	 * @param   string  $unique_key
	 * @return  Jelly_Model
	 */
	public function get_user($unique_key)
	{
		return Jelly::query('user')->where($this->unique_key($unique_key), '=', $unique_key)->limit(1)->select();
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