<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Class Admin Menu
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Menu_Admin extends Menu {

	protected $_views_path = 'backend/menu';


	protected function _get_root($name)
	{
		$menu = parent::_get_root($name);

		if( ! $menu->loaded())
		{
			$menu = $this->_gen_admin_menu();
		}

		return $menu;
	}

	protected function _gen_admin_menu()
	{
		$admin_root = Jelly::query('menu')
			->where('name', '=', 'admin')
			->where('title', '=', NULL)
			->where('route_name', '=', 'admin')
			->limit(1)
			->select();

		if( ! $admin_root->loaded())
		{
			$max_scope_item = Jelly::query('menu')
				->order_by('scope', 'DESC')
				->limit(1)
				->select();

			$admin_root = Jelly::factory('menu')
				->set(array(
					'name' => 'admin',
					'title' => NULL,
					'route_name' => 'admin',
					'visible' => FALSE))
				->insert_as_new_root(++$max_scope_item->scope);
		}

		if( ! $admin_root->has_children())
		{
			$directories = array(
				0 => array(
					'title' => 'Главная',
					'route_name' => 'admin',
					'subdir' => array(
						0 => array(
							'title' => 'Главная админ-интерфейса',
							'route_name' => 'admin',
						),
						1 => array(
							'title' => 'Главная сайта',
							'route_name' => 'default',
						)
					)
				)
			);

			$last_admin_directory = NULL;
			foreach($directories as $directory)
			{
				$admin_directory = Jelly::factory('menu');
				$admin_directory->set($directory);

				if($last_admin_directory === NULL)
				{
					$admin_directory->insert_as_first_child($admin_root);
				}
				else
				{
					$admin_directory->insert_as_next_sibling($last_admin_directory);
				}

				$last_admin_directory = $admin_directory->id;

				if(isset($directory['subdir']))
				{
					$last_admin_subdirectory = NULL;
					foreach($directory['subdir'] as $subirectory)
					{
						$admin_subdirectory = Jelly::factory('menu');
						$admin_subdirectory->set($subirectory);

						if($last_admin_subdirectory === NULL)
						{
							$admin_subdirectory->insert_as_first_child($admin_directory);
						}
						else
						{
							$admin_subdirectory->insert_as_next_sibling($last_admin_subdirectory);
						}

						$last_admin_subdirectory = $admin_subdirectory->id;
					}
				}
			}
		}

		return $admin_root;
	}

} // End Menu