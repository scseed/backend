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
	 */
	public function action_new ()
	{
		$this->template->page_title = 'Новый пользователь';

		$roles            = Jelly::query('role')->select()->as_array('id', 'description');
		$statuses         = array(1 => __('Активен'), 0 => __('Отключён'));
		$user_fields      = Jelly::meta('user')->fields();
		$user_data_fields = Jelly::meta('user_data')->fields();

		$errors = NULL;
		$post   = array();
		foreach($user_fields as $user_field)
		{
			if($user_field->in_form)
				$post['user'][$user_field->name] = ($user_field->default) ? $user_field->default : NULL;
		}

		foreach($user_data_fields as $user_data_field)
		{
			if($user_data_field->in_form)
				$post['user_data'][$user_data_field->name] = ($user_data_field->default) ? $user_data_field->default : NULL;
		}

		if ($this->request->method() === Request::POST)
		{
			$post['user']['password_confirm'] = NULL;
			$save_array = $this->_add_edit($post);

			$post = $save_array['post'];
			$errors = $save_array['errors'];
		}

		$this->template->content = View::factory('backend/form/user/new')
			->bind('post', $post)
			->bind('roles', $roles)
			->bind('statuses', $statuses)
			->bind('errors', $errors);
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
		$user = Jelly::query('user', (int) $id)->select();
		if($user->user_data->delete())
		{
			$this->request->redirect(
				Route::url('admin', array('controller' => 'user', 'action' => 'list'))
			);
		}
	}

	/**
	 * Creating/Updating user
	 *
	 * @param  array $post
	 * @return array
	 * @TODO: Sending email on success creating/updating
	 */
	public function _add_edit($post)
	{
		$action         = 'create';
		$errors         = NULL;
		$user_info      = Arr::extract(Arr::get($_POST, 'user'), array_keys($post['user']), NULL);
		$user_data_info = Arr::extract(Arr::get($_POST, 'user_data'), array_keys($post['user_data']), NULL);

		$user      = (Arr::get($user_info, 'id'))
			? Jelly::query('user', Arr::get($user_info, 'id'))->select()
			: Jelly::factory('user');
		$user_data = (Arr::get($user_data_info, 'id'))
			? Jelly::query('user_data', Arr::get($user_data_info, 'id'))->select()
			: Jelly::factory('user_data');

		if($user->loaded())
		{
			$action = 'update';
		}

		$user_data->set($user_data_info);
		try
		{
			$user_data->save();
			$user_data_info['id'] = $user_data->id;
		}
		catch(Jelly_Validation_Exception $e)
		{
			$errors['user_data'] = $e->errors('common_validation');
		}

		if(empty($errors))
		{
			$user_info['user_data'] = $user_data->id;
			try
			{
				if($user->loaded())
				{
					$user->set($user_info);
					$user->save();
//					$user->add('roles', $user_info['roles'])->save();
				}
				else
				{
					$user->create_user($user_info, array_keys($post['user']));
				}

				if($user->has_role('admin') AND $action == 'create')
					$this->_send_email($user, $user_info['password']);

				$this->request->redirect(
					Route::url('admin', array('controller' => 'user', 'action' => 'list'))
				);
			}
			catch (Jelly_Validation_Exception $e)
			{
				$errors['user'] = $e->errors('common_validation');
			}
		}

		$post['user']      = $user_info;
		$post['user_data'] = $user_data_info;

		return array(
			'post' => $post,
			'errors' => $errors,
		);
	}

	public function _send_email($user, $password)
	{
		$message = View::factory('backend/content/email/user/credentials')
			->set('site', Kohana::config('admin'))
			->bind('user', $user)
			->bind('password', $password)
		;
		Email::connect();
		Email::send(
			$user->email,
			'no-reply@adidas.ru',
			'Данные для входа в админ-центр',
			$message,
			TRUE
		);
	}

} // End Template Controller user
