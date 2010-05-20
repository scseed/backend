<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Role Model for Jelly ORM
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Model_Role extends Jelly_Model {

	/**
	 * Initializating model meta information
	 *
	 * @param Jelly_Meta $meta
	 */
	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('roles')
			->fields(array(
				'id' => new Field_Primary(array(
					'in_form' => FALSE,
				)),
				'parent' => new Field_BelongsTo(array(
					'null' => TRUE,
					'editable' => FALSE,
					'foreign' => 'role',
					'column' => 'parent_id',
					'default' => NULL,
					'rules' => array(
						'numeric' => array(TRUE),
					),
					'label' => 'Родительская роль',
				)),
				'name' => new Field_String(array(
					'empty' => FALSE,
					'default' => '',
					'rules' => array(
						'not_empty' => array(TRUE),
					),
					'label' => 'Название роли',
				)),
				'description' => Jelly::field('String'),
				'rules' => new Field_ManyToMany(array(
					'in_db' => FALSE,
					'in_form' => FALSE,
				)),
				'label' => 'Описание',
			))
			->load_with(array(
				//'parent'
			))
		;
	}
} // End Model_Role