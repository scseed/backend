<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Resource Model for Jelly ORM
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Model_Resource extends Jelly_Model {

	/**
	 * Initializating model meta information
	 *
	 * @param Jelly_Meta $meta
	 */
	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('resources')
			->fields(array(
				'id' => new Field_Primary,
				'parent' => new Field_BelongsTo(array(
					'foreign' => 'resource',
					'column' => 'parent_id',
					'model' => 'resource'
				)),
				'name' => new Field_String
			));
	}
} // End Model_Resource