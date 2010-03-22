<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Template Controller user
 *
 * @author avis <smgladkovskiy@gmail.com>
 */
class Controller_Admin_User extends Controller_Admin_Template {

	public function action_list ()
	{
		$this->template->page_title = 'Список пользователей';

		$users = Jelly::select('user')->execute();
		$users_meta = Jelly::meta('user');

		$this->template->content = View::factory('backend/content/user/list')
			->bind('users', $users)
			->bind('users_meta', $users_meta);
	}

	public function action_new ()
	{
		$this->template->page_title = 'Новый пользователь';

		$user = Jelly::factory('user');
		$errors = array();
		if ($_POST)
		{
			try
			{
				if($user->set($_POST)->save())
				{
					$this->request->redirect('admin/user/list');
				}

			}
			catch (Validate_Exception $e)
			{
				$errors =  $e->array->errors('user/add');
			}
		}

		$this->template-> content = View::factory('backend/content/user/new')
			->set('user', $user)
			->set('fields', $user->meta()->fields())
			->set('errors', $errors);
	}

	public function action_edit($id)
	{
		$user = Jelly::select('user', $id);

		if( ! $user->loaded()) Request::factory('error/404')->execute();

		$errors = array();

		if ($_POST)
		{
			try
			{
				if($_POST['password'] === '' AND $_POST['password_confirm'] === '')
				{
					unset ($_POST['password'], $_POST['password_confirm']);
				}
				if($user->set($_POST)->save())
				{
					$this->request->redirect('admin/user/list');
				}
			}
			catch (Validate_Exception $e)
			{
				$errors =  $e->array->errors('user/add');
			}
		}

		$this->template->content = View::factory('backend/content/user/edit')
			->set('user', $user)
			->set('fields', $user->meta()->fields())
			->set('errors', $errors);

		$this->template->page_title = 'Правка данных пользователя ' . $user->name;
	}

	public function action_delete ($id)
	{
		Jelly::factory('user')->delete($id);

		$this->request->redirect('admin/user/list');
	}

} // End Template Controller user
