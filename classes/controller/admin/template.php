<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Controller Admin Template
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Controller_Admin_Template extends Kohana_Controller_Template {

	public $template = 'backend/template/main';

	public $_auth_required     = TRUE;
	public $actions_privileges = array();


	protected $_resource         = '';
	protected $_active_menu_item = '';

	protected $_actions = array();
	protected $media;

	/**
	 * Признак ajax-like запроса
	 *
	 * @var boolean
	 */
	protected $_ajax = FALSE;


	public function before()
	{
		I18n::lang('ru');

		parent::before();

	    if(Kohana::$is_cli)
		    $this->auto_render = FALSE;

		// Проверка на запрос AJAX-типа
		if (Request::current()->is_ajax() OR Request::initial() !== Request::current())
		{
			$this->_ajax = TRUE;
		}


		if($this->request->action() === 'media') {
			// Do not template media files
			$this->auto_render = FALSE;
		    $this->_auth_required = FALSE;
		}

		$default_actions = array(
			'index' => array(
				'read'
				),
			'list' => array(
				'read'
			),
			'new' => array(
				'read', 'create'
			),
			'add' => array(
				'read', 'create'
			),
			'edit' => array(
				'update'
			),
			'delete' => array(
				'delete'
			),
			'status' => array(
				'update', 'delete'
			),
			'close' => array(
				'update',
			)
		);

		if(empty($this->_actions))
		{
			$this->_actions = $default_actions;
		}
		else
		{
			$this->_actions = Arr::merge($this->_actions, $default_actions);
		}

		$config = Kohana::$config->load('admin');

		$is_logged_in =  Auth::instance('admin')->logged_in();

		//Если требуется авторизация отправлям позователя на форму логина
		if ($this->_auth_required AND !$is_logged_in AND ! Kohana::$is_cli)
		{
			Session::instance()->set('url', $_SERVER['REQUEST_URI']);
			$this->request->redirect(Route::url('admin', array('controller' => 'auth', 'action' => 'login')));
		}

		//Так как контроллеры являются ресурсами, имени ресурса присваивается имя контроллера
		if (empty($this->_resource))
		{
			$this->_resource = array(
				'route_name' => Route::name($this->request->route()),
				'directory' => $this->request->directory(),
				'controller' => $this->request->controller(),
				'action' => $this->request->action(),
				'object_id' => (Route::name($this->request->route()) == 'page')
				                ? $this->request->param('page_alias')
				                : $this->request->param('id'),
			);
		}

		//Если карта методов была иницилизирована, то проверяем контроллер на возможность запуска
		if($this->_auth_required AND ! Kohana::$is_cli)
		{
			if (isset($this->_actions[$this->request->action()]))
			{
				if ( ! ACL::instance()->is_allowed(
					Jelly::query('user', Auth::instance('admin')->get_user()->id)->select()->roles->as_array('id', 'name'),
					$this->_actions[$this->request->action()],
					$this->request))
				{
					throw new HTTP_Exception_403('Access not allowed');
				};
			}
			else
			{
				throw new HTTP_Exception_401('fail in action map of ":controller" controller', array(':controller' => $this->request->controller()));
			}
		}

		if ($this->auto_render === TRUE)
		{
			// Grab the necessary routes
			$this->media = Route::get('docs/media');

			$this->template->title            = $config['company_name'];
			$this->template->page_title       = '';
			$this->template->right_content    = '';
			$this->template->company_name     = $config['company_name'];
			$this->template->ed_copy          = $config['ed_copy'];
			$this->template->menu             = Menu::factory('admin');
			$this->template->debug            = View::factory('profiler/stats');
			$this->template->styles           = array();
			$this->template->scripts          = array();
		}
		$this->template->logged_in          = $is_logged_in;
		$this->template->content          = '';

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
				->add('js/ckeditor/ckeditor.min.js')
				->add('js/ckeditor/adapters/jquery.js')
				->add_modpath('js/admin_effects.js')
		;

		$this->template->user = Auth::instance('admin')->get_user();
	}

	public function after()
	{
//		$media = Route::get('docs/media');

		if(is_object($this->template->content))
		{
			$this->template->content->controller = $this->request->controller();
		}

		if($this->auto_render === TRUE)
		{
//			$styles = array(
//				$media->uri(array('file' => 'css/admin.css')) => 'screen, projection',
//				$media->uri(array('file' => 'css/jquery-ui-1.8.16.custom.css')) => 'screen, projection',
//			);

//			StaticCss::instance()->add('js/redactor/css/redactor.css');




//			$this->template->styles = array_merge( $this->template->styles, $styles );
//			$this->template->scripts = array_merge( $this->template->scripts, $scripts );
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

} // End Controller_Admin_Template