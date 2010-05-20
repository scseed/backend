<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Rule Model for Jelly ORM
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Model_Rule extends Jelly_Model {

	/**
	 * Initializating model meta information
	 *
	 * @param Jelly_Meta $meta
	 */
	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('rules')
			->fields(array(
				'id' => new Field_Primary(array(
					'in_form' => FALSE,
				)),
				'type' => new Field_Enum(array(
					'choices' => array('allow' => 'Разрешение', 'deny' => 'Запрещение'),
					'rules' => array(
						'not_empty' => array(TRUE),
					),
					'label' => 'Тип правила',
				)),
				'name' => new Field_String(array(
					'empty' => FALSE,
					'rules' => array(
						'not_empty' => array(TRUE),
					),
					'label' => 'Название правила',
				)),
				'resource' => new Field_BelongsTo(array(
					'rules' => array(
						'not_empty' => array(TRUE),
					),
					'label' => 'Наименование ресурса',
				)),
				'privileges' =>  new Field_ManyToMany(array(
					'through' => 'rules_privileges',
					'in_db' => FALSE,
					'rules' => array(
						'not_empty' => array(TRUE),
					),
					'label' => 'Привилегии',
				)),
				'roles' =>  new Field_ManyToMany(array(
					'through' => 'roles_rules',
					'in_db' => FALSE,
					'empty' => TRUE,
					'rules' => array(
						'not_empty' => array(TRUE),
					),
					'label' => 'Список ролей',
				)),
				'assertion' =>  new Field_HasOne(array(
					'in_db' => FALSE,
					'empty' => TRUE,
					'in_form' => FALSE,
					'label' => 'Уточнение к правилу',
				)),
			))
			->load_with(array(
				//'resource'
			));
	}
} // End Model_Rule