<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * users Model for Jelly ORM
 *
 * @author Sergei Gladkovskiy <smgladkovskiy@gmail.com>
 * @copyrignt
 */
class Model_Roles_Users extends Jelly_Model {

	/**
	 * Initializating model meta information
	 *
	 * @param Jelly_Meta $meta
	 */
	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('roles_users')
			->fields(array(
				'role' => Jelly::field('BelongsTo'),
				'user' => Jelly::field('BelongsTo'),
			))
			->load_with(array('role','user'));
	}
} // End Model_users