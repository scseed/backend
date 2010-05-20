<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Privilege Model for Jelly ORM
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Model_Privilege extends Jelly_Model {

	/**
	 * Initializating model meta information
	 *
	 * @param Jelly_Meta $meta
	 */
	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('privileges')
			->fields(array(
				'id' => new Field_Primary,
				'name' => new Field_String(array(
					'rules' => array(
						'not_empty' => array(TRUE),
					),
				)),
			));
	}
} // End Model_Privilege