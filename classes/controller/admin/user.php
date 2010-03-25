<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Template Controller user
 *
 * @author avis <smgladkovskiy@gmail.com>
 */
class Controller_Admin_User extends Controller_Admin_Template {

	/**
	 * List of users
	 */
	public function action_list ()
	{
		$this->template->page_title = 'Список пользователей';

		$users = Jelly::select('user')->execute();
		$users_meta = Jelly::meta('user');

		$this->template->content = View::factory('backend/content/user/list')
			->bind('users', $users)
			->bind('users_meta', $users_meta);
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
		$errors = array();
		if ($_POST)
		{
			try
			{
				if($user->set($_POST)->save())
				{
					Logapp::instance()->write(
						'user_add',
						'success',
						A1::instance()->get_user()->id,
						'Создан новый пользователь'
					);
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

	/**
	 * Editing user
	 *
	 * @TODO: отправлять email пользователю при смене его пароля.
	 * @param integer $id
	 */
	public function action_edit($id)
	{
		$user = Jelly::select('user', (int) $id);

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
					Logapp::instance()->write(
						'user_edit',
						'success',
						A1::instance()->get_user()->id,
						'Данные пользователя изменены (id: '.$user->id.'; email: '.$user->email.', )'
					);
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
			Logapp::instance()->write(
				'user_delete',
				'success',
				A1::instance()->get_user()->id,
				'Удалён пользователь (id: '.$user_data['id'].'; email: '.$user_data['email'].', )'
			);

			$this->request->redirect('admin/user/list');
		}
	}

} // End Template Controller user
