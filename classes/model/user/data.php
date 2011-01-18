<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * User_data Model for Jelly ORM
 *
 * @author devolonter <devolonter@enerdesign.ru>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Model_User_Data extends Jelly_Model {

	/**
	 * Initializating model meta information
	 *
	 * @param Jelly_Meta $meta
	 */
	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('user_data')
			->fields(array(
				'id' => Jelly::field('Primary', array(
					'in_form' => FALSE,
				)),
				'last_name' => Jelly::field('String', array(
					'empty' => FAlSE,
					'rules' => array(
						'not_empty' => array(TRUE)
					),
					'label' => 'Фамилия',
				)),
				'first_name' => Jelly::field('String', array(
					'empty' => FAlSE,
					'rules' => array(
						'not_empty' => array(TRUE)
					),
					'label' => 'Имя',
				)),
				'patronymic' => Jelly::field('String', array(
					'allow_null' => TRUE,
					'label' => 'Отчество',
				)),
				'phone' => Jelly::field('String', array(
					'allow_null' => TRUE,
					'label' => 'Телефон',
				)),
				'company' => Jelly::field('String', array(
					'allow_null' => TRUE,
					'label' => 'Компания',
				)),
				'position' => Jelly::field('String', array(
					'allow_null' => TRUE,
					'label' => 'Должность',
				)),
			));
	}
} // End Model_User_data