<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Assertion Model for Jelly ORM
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Model_Assertion extends Jelly_Model {

	/**
	 * Initializating model meta information
	 *
	 * @param Jelly_Meta $meta
	 */
	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('assertions')
			->fields(array(
				'id' => new Field_Primary,
				'rule' => new Field_BelongsTo(array(
					'rules' => array(
						'not_empty' => array(TRUE),
					),
				)),
				'resource' => new Field_BelongsTo(array(
					'rules' => array(
						'not_empty' => array(TRUE),
					),
				)),
				'user_field' => new Field_String(array(
					'empty' => FALSE
				)),
				'resource_field' => new Field_String(array(
					'empty' => FALSE
				)),
			))
			->load_with(array(
				//'rule', 'resource',
			));
	}
} // End Model_Assertion