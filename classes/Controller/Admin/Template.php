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

	protected $_user       = NULL;
	protected $_user_roles = array();

	protected $_resource         = '';
	protected $_active_menu_item = '';

	protected $media;

	/**
	 * Признак ajax-like запроса
	 *
	 * @var boolean
	 */
	protected $_ajax = FALSE;

	public function __construct(Request $request, Response $response)
	{
		// Ajax-like request setting if HMVC call or POST request with param `is_ajax` == `true`
		if ($request->is_ajax() OR ($request !== Request::initial() AND $request->param('code') === NULL)
		OR ($request->method() === HTTP_Request::POST AND $request->post('is_ajax') === 'true'))
		{
			$request->requested_with('xmlhttprequest');
			$this->_ajax = TRUE;
		}

		parent::__construct($request, $response);
	}

	public function before()
	{
		I18n::lang('ru');

		parent::before();

		if($this->request->action() === 'media') {
			// Do not template media files
			$this->auto_render    = FALSE;
		    $this->_auth_required = FALSE;
		}

		if(PHP_SAPI == 'cli')
			$this->auto_render = FALSE;

		$config = Kohana::$config->load('admin');

		$this->is_logged_in =  Auth::instance('admin')->logged_in();

		// Auth check
		$this->_auth_check();

		if ($this->auto_render === TRUE)
		{
			// Grab the necessary routes
			$this->media = Route::get('docs/media');

			$this->template->title            = $config['company_name'];
			$this->template->page_title       = '';
			$this->template->company_name     = $config['company_name'];
			$this->template->menu             = Menu::factory('admin', $this->_user_roles, $this->request);
			$this->template->debug            = View::factory('profiler/stats');
			$this->template->styles           = array();
			$this->template->scripts          = array();
		}
		$this->template->logged_in = $this->is_logged_in;
		$this->template->content   = '';

		StaticCss::instance()
			->add_modpath('css/admin.css')
			->add_modpath('css/bootstrap.min.css')
			->add_modpath('css/bootstrap-responsive.min.css')
			->add_modpath('css/datepicker.css')
			->add_modpath('css/font-awesome.min.css')
		;
		StaticJs::instance()
				->add_modpath('js/jquery-1.10.2.min.js')
				->add_modpath('js/bootstrap.min.js')
				->add_modpath('js/bootstrap-datepicker.js')
				->add_modpath('js/bootstrap-datepicker.ru.js')
				->add_modpath('js/admin_effects.js')
		;

		$this->template->user = Auth::instance('admin')->get_user();
	}

	public function after()
	{
		if(is_object($this->template->content))
		{
			$this->template->content->controller = $this->request->controller();
		}

		// При ajax запросе как ответ используется контент шаблона
		if ($this->_ajax === TRUE)
		{
			$this->response->body($this->template->content);
		}
		else
		{
			parent::after();
		}

	}

	public function _back()
	{
		$this->request->redirect(
			Route::url('admin', array(
				'controller' => $this->request->controller(),
				'action' => 'list')
			)
		);
	}

	protected function _auth_check()
	{
		// Auth require check and setting $this->_user
		if ($this->_auth_required AND PHP_SAPI != 'cli' AND ! $this->is_logged_in)
		{
			Session::instance()->set('url', $_SERVER['REQUEST_URI']);
			HTTP::redirect(Route::url('admin', array('controller' => 'auth', 'action' => 'login')));
		}
		elseif($this->_auth_required AND (class_exists('Auth') AND Auth::instance()->logged_in() OR Auth::instance()->logged_in()))
		{
			$this->_user = Jelly::query('user', Auth::instance()->get_user()->id)->select();
			$this->_check_activity();
			View::set_global('_user', $this->_user);
		}

		if(class_exists('Auth') AND Auth::instance()->logged_in() AND ! $this->_user)
		{
			$this->_user = Jelly::query('user', Auth::instance()->get_user()->id)->select();
			$this->_check_activity();
			View::set_global('_user', $this->_user);
		}
	}

	protected function _check_activity()
	{
		if( ! $this->_user->is_active AND $this->request->controller() != 'Error')
			throw new HTTP_Exception_403(__('Пользователь не зарегистирован или отключён'));

		$this->_user_roles = $this->_user->roles->as_array('id', 'name');
		$this->_deputy = Deputy::instance();
		$roles = Arr::extract(Kohana::$config->load('deputy.roles'), $this->_user_roles);
		$this->_deputy->set_roles($roles);
		$resource = array(
			$this->request->controller(),
			$this->request->action(),
		);
		$resource = implode('/', $resource);

		if($this->_deputy->allowed($resource) == FALSE)
			throw new HTTP_Exception_403(__('Действие запрещено'));

//		$this->_check_rules_acceptance();
	}

} // End Controller_Admin_Template