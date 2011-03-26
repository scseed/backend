<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Template Controller Admin_resource
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Controller_Admin_Resource extends Controller_Admin_Template {

	public function action_list()
	{
		$meta = Jelly::meta('resource');
		$resources = Jelly::query('resource')->select();

		$this->template->page_title = 'Список Ресурсов';
		$this->template->content = View::factory('backend/content/_crud/list')
			->bind('items', $resources)
			->bind('meta', $meta);
	}

} // End Controller_Admin_resource