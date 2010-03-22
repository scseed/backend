<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Controller Admin Template
 *
 * @author avis <smgladkovskiy@gmail.com>
 */
class Controller_Admin_Template extends Controller_Template {

	public $template = 'backend/template/main';

	public $_auth_required = TRUE;
	protected $_resource = '';
	protected $_privelege = array('read');

	public function before()
	{
		parent::before();

		$config = Kohana::config('admin');
		
		$this->template->company_name = $config['company_name'];
		$this->template->menu = new Admin_Menu;
		
		if ($this->_auth_required AND ! A2::instance()->logged_in())
		{
			Session::instance()->set('url', $_SERVER['REQUEST_URI']);
			Request::instance()->redirect('admin/auth/login');
		}

		$this->template->user = A2::instance()->get_user();
	}

} // End Controller_Admin_Template
