<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Template Controller user
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Controller_Admin_User extends Controller_Admin_Template {

	public function action_index()
	{
		return $this->action_list();
	}
	/**
	 * List of users
	 */
	public function action_list ()
	{
		$this->template->page_title = 'Список пользователей';

		$users = Jelly::query('user')->select();
		$users_meta = Jelly::meta('user');

		$this->template->content = View::factory('backend/content/user/list')
			->bind('users', $users)
			->bind('meta', $users_meta);
	}

	/**
	 * Creating new user
	 *
	 * @TODO: отпарвлять письмо новому пользователю с его данными для входа.
	 */
	public function action_new ()
	{
		$this->template->page_title = 'Новый пользователь';

		$user = Jelly::factory('user');
		$errors = NULL;
		if ($_POST)
		{
			foreach($user->meta()->fields() as $field)
			{
				if($field->in_form)
					$post_fields[] = $field->name;
			}
			$post = arr::extract($_POST, $post_fields, NULL);

			$user->set($post);

			try
			{
				$user->save();
				$this->request->redirect('admin/user/list');
			}
			catch (Validate_Exception $e)
			{
				$errors =  $e->array->errors('user');
			}
		}

		$this->template->content = View::factory('backend/content/_crud/add')
			->set('item', $user)
			->set('errors', $errors);
	}

	/**
	 * Editing user
	 *
	 * @TODO: отправлять email пользователю при смене его пароля.
	 * @param integer $id
	 */
	public function action_edit($id)
	{
		$roles = Jelly::query('role')->select()->as_array('id', 'name');
		$user = Jelly::factory('user', (int) $id);

		if( ! $user->loaded()) Request::factory('error/404')->execute();

		$errors = NULL;

		if ($_POST)
		{
			foreach($user->meta()->fields() as $field)
			{
				if($field->in_form)
					$post_fields[] = $field->name;
			}
			$post = arr::extract($_POST, $post_fields, NULL);
			if($post['password'] == '' AND $post['password_confirm'] == '')
			{
				unset($post['password'], $post['password_confirm']);
			}

			$user->set($post);

			try
			{
				$user->save();
				$this->request->redirect('admin/user/list');
			}
			catch (Validate_Exception $e)
			{
				$errors =  $e->array->errors('user');
			}
		}

		$this->template->content = View::factory('backend/content/_crud/edit')
			->set('roles', $roles)
			->set('item', $user)
			->set('meta', $user->meta())
			->set('errors', $errors);

		$this->template->page_title = 'Правка данных пользователя ' . $user->name;
	}

	/**
	 * Deleting user
	 *
	 * @param integer $id
	 */
	public function action_delete ($id)
	{
		$user = Jelly::select('user')->load( (int) $id);

		$user_data = array('id' => $user->id, 'email' => $user->email);

		if($user->delete())
		{
			$this->request->redirect('admin/user/list');
		}
	}

} // End Template Controller user
