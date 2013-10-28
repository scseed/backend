<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Template Controller Admin_role
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Controller_Admin_Role extends Controller_Admin_Template {

	public function action_list()
	{
		$meta = Jelly::meta('role');
		$roles = Jelly::query('role')->select();

		$this->template->page_title = 'Список Ролей';
		$this->template->content = View::factory('backend/content/_crud/list')
			->bind('items', $roles)
			->bind('meta', $meta);
	}

} // End Controller_Admin_role