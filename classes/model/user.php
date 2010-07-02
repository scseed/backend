<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * User Model for Jelly ORM
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
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
				'user_data' => new Field_BelongsTo(array(
					'null' => true,
					'label' => 'ФИО пользователя',
					'in_form' => FALSE,
					'in_table' => FALSE,
				)),
				'email' => Jelly::field('Email', array(
					'empty'  => FALSE,
					'unique' => TRUE,
					'rules' => array(
						'not_empty' => NULL,
					),
					'label' => 'Email',

				)),
				'password' => new Field_Password(array(
					'in_table' => FALSE,
					'hash_with' => array(Auth::instance(), 'hash_password'),
					'rules' => array(
						'not_empty' => NULL,
						'max_length' => array(50),
						'min_length' => array(4)
					),
					'label' => 'Пароль'
				)),
				'password_confirm' => new Field_Password(array(
					'in_form' => TRUE,
					'in_table' => FALSE,
					'in_db' => FALSE,
					'rules' => array(
						'matches' => array('password'),
					),
					'label' => 'Подтверждение пароля'
				)),
				'token' => new Field_String(array(
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

	public function get_role_id()
	{
		$roles = array();
		
		foreach($this->roles as $role)
		{
			$roles[$role->id] = $role->name;
		}

		return $roles;
	}
} // End Model_User