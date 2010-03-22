<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Class Admin Menu
 *
 * @author avis <smgladkovskiy@gmail.com>
 */
class Admin_Menu {

	/**
	 * Building main admin menu
	 * By defaults generate two sections: main and users
	 *
	 * @return string view
	 */
	public function main()
	{
		$config = Kohana::config('admin');
		$menu = array();

		$menu['home'] = array(
			'title'   => __('Главная'),
			'href'    => Route::get('admin')
				->uri(array(
					'controller' => '',
					'action' => ''
					)),
			'class'   => '',
			'submenu' => array(
				'site' => array(
					'title' => __('На главную'),
					'href' => Route::get('admin')
						->uri(array(
							'controller' => '',
							'action' => ''
						)),
					'class' => 'new_window'
				),
				'home' => array(
					'title' => __('Предпросмотр сайта'),
					'href' => Route::get('default')
						->uri(array(
							'controller' => '',
							'action' => ''
						)),
					'class' => 'new_window'
				)
			),
			'resource' => NULL
		);

		foreach ($config['menu'] as $name => $info)
		{
			$menu[$name] = $this->_form_menu($name, $info);
		}

		$menu['user'] = array(
			'title'   => __('Пользователи'),
			'href'    => Route::get('admin')
				->uri(array(
					'controller' => 'user',
					'action' => ''
				 )),
			'class'   => '',
			'submenu' => array(
				'list' => array(
					'title'   => __('Список пользователей'),
					'href'    => Route::get('admin')
						->uri(array(
							'controller' => 'user',
							'action' => 'list',
						 )),
					'class'   => '',
					'resource' => array('user' => 'read')
				),
				'add_user' => array(
					'title'   => __('Добавить пользователя'),
					'href'    => Route::get('admin')
						->uri(array(
							'controller' => 'user',
							'action' => 'new',
						 )),
					'class'   => '',
					'resource' => array('user' => 'add')
				)
			),
			'resource' => array('user' => 'read')
		);

		$request = Request::instance();
		if (isset($menu[$request->controller]))
		{
			$menu[$request->controller]['class'] = 'active';
		}

		// @TODO: прикрутить ACL.

//		$A2 = A2::instance();
//
//		foreach($menu as $key => $item)
//		{
//			if ( ! empty($item['resource']))
//			{
//				$orm = ORM::factory(current(array_keys($item['resource'])));
//
//				if ( ! $A2->allowed($orm, current($item['resource'])))
//				{
//					unset($menu[$key]);
//					continue;
//				}
//			}
//
//			foreach ($item['submenu'] as $inner_key => $inner_item)
//			{
//				if ( ! empty($inner_item['resource']))
//				{
//					$orm = ORM::factory(current(array_keys($inner_item['resource'])));
//
//					if ( ! $A2->allowed($orm, current($inner_item['resource'])))
//					{
//						unset($menu[$key]['submenu'][$inner_key]);
//						continue;
//					}
//				}
//			}
//		}

		return View::factory('backend/menues/main')
						->bind('menu', $menu);
	}

	/**
	 * Forming menu section based on $name of section and parameters array
	 *
	 * @param strind $name
	 * @param array $info
	 * @param string $level
	 * @return array / FALSE
	 */
	protected function _form_menu($name, $info, $level = 'main')
	{
		if($level === 'submenu')
		{
			$menu[$name] = array(
				'title'   => __($info['title']),
				'href'    => Route::get('admin')
					->uri(array(
						'controller' => $info['controller'],
						'action' => $info['action']
						)),
				'class'   => $info['class'],
				'resource' => NULL

			);
			return $menu[$name];
		}
		elseif ($level == 'main')
		{
			$submenu = array();
			if( isset($info['submenu']))
			{
				foreach($info['submenu'] as $_name => $_info)
				{
					$submenu[$_name] = $this->_form_menu($_name, $_info, 'submenu');
				}
			}

			$menu[$name] = array(
				'title'   => __($info['title']),
				'href'    => Route::get('admin')
					->uri(array(
						'controller' => $info['controller'],
						'action' => $info['action']
						)),
				'class'   => $info['class'],
				'submenu' => $submenu,
				'resource' => NULL
			);
			return $menu[$name];
		}
		return FALSE;
	}

} // End Admin_Menu
