<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Class Admin Menu
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Admin_Menu {

	// хранилище инстанса
	protected static $instance;

	/**
	 * Инстанс класса Menu
	 *
	 * @return object Menu
	 */
	public static function instance()
	{
		if( ! is_object(self::$instance))
		{
			self::$instance = new Admin_Menu();
		}

		return self::$instance;
	}

	/**
	 * Building main admin menu
	 * By defaults generate two sections: main and users
	 *
	 * @return string View
	 */
	public function main($active_menu, $additional_menu = array())
	{
		$config = Kohana::config('admin');
		$menu = array();

		// Формирование меню из массива, что дан был в конфиге
		$menu = $this->_gen_menu($config['menu']);

		// Поиск активного уровня меню
		$menu[$this->_find_parent($menu, $active_menu)]['class'] = 'active';

		return View::factory('backend/menues/main')
						->bind('menu', $menu);
	}

	/**
	 * Генерация массива меню для вывода в представление.
	 * Обрабатывает отсутствующие значения для избежания ошибок.
	 *
	 * @param  array  $menu_array
	 * @param  string $parent
	 * @return array  $menu
	 */
	private function _gen_menu(array $menu_array, $parent = NULL)
	{
		$menu = array();
		foreach($menu_array as $item_name => $menu_item)
		{
			if($parent === NULL)
			{
				$parent_name = $item_name;
			}
			else
			{
				$parent_name = $parent;
			}

			$menu[$item_name] = array(
				'parent' => $parent_name,
				'title'   => __($menu_item['title']),
				'href'    => Route::get('default')
					->uri(array(
						'controller' => arr::get($menu_item, 'controller', 'home'),
						'action' =>  arr::get($menu_item, 'action', NULL),
						)),
				'class'   => arr::get($menu_item, 'class', NULL),
				'resource' => NULL,
				'visible' => arr::get($menu_item, 'visible', TRUE),
				'submenu' => ( ! empty($menu_item['submenu']))
					? $this->_gen_menu($menu_item['submenu'], $parent_name)
					: array(),

			);
		}

		return $menu;
	}

	/**
	 * Поиск родительского элемента для присвоения ему статуса активности
	 *
	 * @param  array  $menu_array
	 * @param  string $active_menu
	 * @return string $parent
	 */
	private function _find_parent(array $menu_array, $active_menu)
	{
		$parent = NULL;
		foreach($menu_array as $name => $item)
		{
			if ($name == $active_menu)
			{
				$parent = $item['parent'];
			}

			if ($parent)
				return $parent;

			foreach($item['submenu'] as $subname => $subitem)
			{
				if ($subname == $active_menu)
				{
					$parent = $subitem['parent'];
				}
			}

			if ($parent)
				return $parent;
		}

		$parent = ($parent != NULL) ? $parent : 'home';

		return $parent;
	}
} // End Menu