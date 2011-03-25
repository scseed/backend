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
	protected $_resource = '';
	public $actions_privileges = array();
	protected $_active_menu_item = '';

	public $_actions = array();
	protected $media;
	/**
	 * Признак ajax-like запроса
	 *
	 * @var boolean
	 */
	protected $_ajax = FALSE;


	public function before()
	{

		parent::before();

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

		$config = Kohana::config('admin');

		//Если требуется авторизация отправлям позователя на форму логина
		if ($this->_auth_required AND ! Auth::instance('admin')->logged_in())
		{
			Session::instance()->set('url', $_SERVER['REQUEST_URI']);
			$this->request->redirect('admin/auth/login');
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
		if($this->_auth_required)
		{
			if (isset($this->_actions[$this->request->action()]))
			{
				if ( ! ACL::instance()->is_allowed(
					Auth::instance('admin')->get_user()->roles->as_array('id', 'name'),
					$this->_actions[$this->request->action()],
					$this->request))
				{
//					throw new Kohana_Exception403();
					die('Not allowed');
				};
			}
			else
			{
				die('fail in action map of '. $this->request->controller . ' controller');
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
			$this->template->debug = (Kohana::$environment == 'development'
			                          OR Kohana::$environment == 'test')
										? View::factory('profiler/stats') : '';
		}
		$this->template->content          = '';

		$this->template->user = Auth::instance('admin')->get_user();
	}

	public function after()
	{
		$media = Route::get('docs/media');

		if(is_object($this->template->content))
		{
			$this->template->content->controller = $this->request->controller();
		}

		if($this->auto_render === TRUE)
		{
			$styles = array(
				$media->uri(array('file' => 'css/admin.css')) => 'screen, projection',
			);

			$scripts = array(
				$media->uri(array('file' => 'js/jquery.js')),
				$media->uri(array('file' => 'js/admin_effects.js')),
			);

			$this->template->styles = array_merge( $this->template->styles, $styles );
			$this->template->scripts = array_merge( $this->template->scripts, $scripts );
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

} // End Controller_Admin_Template