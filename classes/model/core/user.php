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
				'user_data' => Jelly::field('BelongsTo', array(
					'allow_null' => true,
					'default' => NULL,
					'label' => __('User profile'),
					'in_form' => FALSE,
					'in_table' => FALSE,
				)),
				'is_active' => Jelly::field('Boolean', array(
					'label' => 'Статус',
					'label_true' => 'Активен',
					'label_false' => 'Отключён',
					'default' => TRUE
				)),
				'date_create' => Jelly::field('Timestamp', array(
					'auto_now_create' => TRUE,
				)),
				'date_update' => Jelly::field('Timestamp', array(
					'auto_now_update' => TRUE,
				)),
			));

		// Disable 'username' field
		$meta->field('username', 'String', array('in_db' => FALSE));
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