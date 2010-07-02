<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Template Controller Admin_acl
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Controller_Admin_Acl extends Controller_Admin_Template {

	public function action_list()
	{
		$meta = Jelly::meta('acl');
		$rules = Jelly::select('acl')->execute();

		$this->template->page_title = 'Список Правил ACL';
		$this->template->content = View::factory('backend/content/_crud/list')
			->bind('items', $rules)
			->bind('meta', $meta);
	}

} // End Controller_Admin_acl