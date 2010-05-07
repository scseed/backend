<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Controller Admin Template
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Controller_Admin_Template extends Kohana_Controller_Template {

	public $template = 'backend/template/main';

	public $_auth_required = TRUE;
	protected $_resource;
	protected $_privelege = array('read');

	public function before()
	{
		parent::before();

		$config = Kohana::config('admin');
		$this->template->title      = 'Bluefish';
		$this->template->content    = '';
		$this->template->company_name = $config['company_name'];
		$this->template->menu = new Admin_Menu;
		$this->template->debug = View::factory('profiler/stats');
		$this->template->styles = array();
  		$this->template->scripts = array();

		
		if ($this->_auth_required AND ! A2::instance()->logged_in())
		{
			Session::instance()->set('url', $_SERVER['REQUEST_URI']);
			Request::instance()->redirect('admin/auth/login');
		}

		$this->_resource = $this->request->controller;
		$this->template->user = A2::instance()->get_user();
	}

	public function after()
	{
		if ($this->auto_render)
		{
			$styles = array(
				'css/admin.css' => 'screen, projection',
			);

			$scripts = array(
				'js/jquery-1.4.2.min.js',
				'js/admin_effects.js',
			);

			$this->template->styles = array_merge( $this->template->styles, $styles );
			$this->template->scripts = array_merge( $this->template->scripts, $scripts );
		}

		parent::after();
	}

} // End Controller_Admin_Template