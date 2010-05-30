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

	public function before()
	{
		parent::before();

		$config = Kohana::config('admin');
		
		//Если требуется авторизация отправлям позователя на форму логина
		if ($this->_auth_required AND ! A1::instance()->logged_in())
		{
			Session::instance()->set('url', $_SERVER['REQUEST_URI']);
			Request::instance()->redirect('admin/auth/login');
		}

		//Так как контроллеры являются ресурсами, имени ресурса присваивается имя контроллера
		if (empty($this->_resource))
		{
			$this->_resource = $this->request->controller;
		}

		//Если карта методов была иницилизирована, то проверяем контроллер на возможность запуска
//		if (isset($this->actions_privileges[$this->request->action]))
//		{
//			if ( ! A2::instance()->allowed($this->_resource, $this->actions_privileges[$this->request->action]))
//			{
//				throw new Kohana_Exception403();
//			};
//		}

		//вычисляем ключ активного пункта меню по умолчанию
		$this->_active_menu_item = $this->request->controller;

		if ($this->request->action != 'index')
		{
			$this->_active_menu_item .= '_'.$this->request->action;
		}
		
		if ($this->auto_render === TRUE)
		{
		
			$this->template->title            = $config['company_name'];
			$this->template->page_title       = '';
			$this->template->content          = '';
			$this->template->right_content    = '';
			$this->template->company_name     = $config['company_name'];
			$this->template->menu             = Admin_Menu::instance();
			$this->template->debug            = View::factory('profiler/stats');
			$this->template->styles           = array();
			$this->template->scripts          = array();
		}

		$this->template->user = A2::instance()->get_user();
	}

	public function after()
	{
		if ($this->auto_render)
		{
			$this->template->content->controller = $this->request->controller;
			
			$styles = array(
				'css/admin.css' => 'screen, projection',
			);

			$scripts = array(
				'js/jquery-1.4.2.min.js',
				'js/admin_effects.js',
			);

			$this->template->active_menu_item = $this->_active_menu_item;
			$this->template->styles = array_merge( $this->template->styles, $styles );
			$this->template->scripts = array_merge( $this->template->scripts, $scripts );
		}

		parent::after();
	}

} // End Controller_Admin_Template