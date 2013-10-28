<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * News Model for Jelly ORM
 *
 * @author avis <smgladkovskiy@gmail.com>
 */
abstract class Model_Core_News extends Jelly_Model {

	/**
	 * Initializating model meta information
	 *
	 * @param Jelly_Meta $meta
	 */
	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('news')
			->fields(array(
				'id' => Jelly::field('Primary'),
				'pubdate' => Jelly::field('Timestamp', array(
					'rules' => array(
						array('not_empty')
					),
					'label' => 'Дата публикации новости',
				)),
				'title' => Jelly::field('String', array(
					'rules' => array(
						array('not_empty')
					),
					'label' => 'Краткий заголовок',
				)),
				'longtitle' => Jelly::field('String', array(
					'rules' => array(
						array('not_empty')
					),
					'in_grid' => FALSE,
					'label' => 'Полный заголовок',

				)),
				'introtext' => Jelly::field('Text', array(
					'rules' => array(
						array('not_empty')
					),
					'label' => 'Краткое описание',
				)),
				'text' => Jelly::field('Text', array(
					'rules' => array(
						array('not_empty')
					),
					'in_grid' => FALSE,
					'label' => 'Полное описание',
				)),
				'is_active' => Jelly::field('Boolean', array(
					'label' => 'Статус',
					'label_true' => 'Опубликовано',
					'label_false' => 'Снято с публикации',
				)),
			))
		;
	}
} // End Model_News