<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Template Controller home
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Controller_Admin_Home extends Controller_Admin_Template {

	public function action_index ()
	{
		$logs = Logapp::instance()->watch(10);
		$logs_meta = Jelly::factory('log_jelly');

		$this->template->page_title = $this->template->title = __('Главная');
		$this->template->content = View::factory('backend/content/home')
			->set('company_name', $this->template->company_name)
			->set('logs_meta', $logs_meta->meta())
			->bind('logs', $logs);
	}

} // End Template Controller home
