<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Template Controller user
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Controller_Admin_User extends Controller_Admin_Template {

	protected $_errors = NULL;

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

		$role_users = Jelly::query('roles_users')
			->where(':role.name', '=', 'admin')
			->select()
		;

		$users_meta = Jelly::meta('user');

		$this->template->content = View::factory('backend/content/user/list')
			->bind('users', $role_users)
			->bind('meta', $users_meta);
	}

	/**
	 * Creating new user
	 */
	public function action_add ()
	{
		$this->template->page_title = 'Новый пользователь';

		$roles    = Jelly::query('role')->select()->as_array('id', 'description');
		$statuses = array(1 => __('Активен'), 0 => __('Отключён'));
		$post     = array(
			"user" => array(
				'id' => NULL,
				"email" => NULL,
				"roles" => array(),
				"password" => NULL,
				"password_confirm" => NULL,
				"is_active" => TRUE,
			),
			"user_data" => array(
				'id' => NULL,
				"last_name" => NULL,
				"first_name" => NULL,
				"patronymic" => NULL,
			)
		);

		if ($this->request->method() === Request::POST)
		{
			$post_data = Arr::extract($_POST, array_keys($post), NULL);

			if($post_data['user']['id'] == '')
				unset($post_data['user']['id']);

			if($post_data['user_data']['id'] == '')
				unset($post_data['user_data']['id']);

			$post = $this->_add_edit($post_data);
		}

		$this->template->content = View::factory('backend/form/user/new')
			->bind('post', $post)
			->bind('roles', $roles)
			->bind('statuses', $statuses)
			->bind('errors', $this->_errors);
	}

	/**
	 * Editing user
	 *
	 * @TODO: отправлять email пользователю при смене его пароля.
	 */
	public function action_edit()
	{
		$user_id = (int) $this->request->param('id');

		if( ! $user_id)
			throw new HTTP_Exception_404(__('User id is not defined'));

		$user = Jelly::query('user', $user_id)->select();
		$user_data = $user->user_data;

		if( ! $user->loaded())
			throw new HTTP_Exception_404(__('User not found'));

		$roles    = Jelly::query('role')->select()->as_array('id', 'description');
		$statuses = array(1 => __('Активен'), 0 => __('Отключён'));
		$post     = array(
			"user" => array(
				'id' => $user->id,
				"email" => $user->email,
				"roles" => $user->roles->as_array('name', 'id'),
				"password" => NULL,
				"password_confirm" => NULL,
				"is_active" => $user->is_active,
			),
			"user_data" => array(
				'id' => $user_data->id,
				"last_name" => $user_data->last_name,
				"first_name" => $user_data->first_name,
				"patronymic" => $user_data->patronymic,
			)
		);

		if ($this->request->method() === Request::POST)
		{
			$post_data = Arr::extract($_POST, array_keys($post));
			if($post_data['user']['password'] == '' AND $post_data['user']['password_confirm'] == '')
			{
				unset($post_data['user']['password'], $post_data['user']['password_confirm']);
			}

			if( ! $post['user']['id'])
				unset($post['user']['id']);

			if( ! $post['user_data']['id'])
				unset($post['user_data']['id']);

			$post = $this->_add_edit($post_data, 'update');
		}

		$this->template->page_title = 'Правка данных пользователя ' . $user->name;
		$this->template->content = View::factory('backend/form/user/new')
			->set('roles', $roles)
			->set('post', $post)
			->set('statuses', $statuses)
			->set('errors', $this->_errors);
	}

	/**
	 * Deleting user
	 *
	 * @param integer $id
	 */
	public function action_delete ()
	{
		$user_id = intval($this->request->param('id'));
		$user = Jelly::query('user', (int) $user_id)->select();

		$user->user_data->delete();
//		$user->delete();
		$this->request->redirect($this->request->referrer());
	}

	/**
	 * Creating/Updating user
	 *
	 * @param  array $post
	 * @param  string $action
	 * @return array
	 * @TODO: Sending email on success creating/updating
	 */
	public function _add_edit($post, $action = 'create')
	{
		$user_info      = Arr::extract(Arr::get($post, 'user'), array_keys($post['user']), NULL);
		$user_data_info = Arr::extract(Arr::get($post, 'user_data'), array_keys($post['user_data']), NULL);

		$user      = (Arr::get($user_info, 'id'))
			? Jelly::query('user', (int) Arr::get($user_info, 'id'))->select()
			: Jelly::factory('user');
		$user_data = (Arr::get($user_data_info, 'id'))
			? Jelly::query('user_data', (int) Arr::get($user_data_info, 'id'))->select()
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
			$this->_errors['user_data'] = $e->errors('validate');
		}

		if(empty($this->_errors))
		{
			$user_info['user_data'] = $user_data->id;
			$user_info['name'] = $user_data->last_name.' '.$user_data->first_name;
			$user_info['roles'] = Arr::flatten($user_info['roles']);

			try
			{
				if($user->loaded())
				{
					$user->update_user($user_info, array_keys($user_info));
				}
				else
				{
					$user->create_user($user_info, array_keys($user_info));
				}

				$user_info['id'] = $user->id;

				if($user->has_role('admin') AND $action == 'create')
					$this->_send_email($user, $user_info['password']);

				$this->request->redirect(
					Route::url('admin', array('controller' => 'user', 'action' => 'list'))
				);
			}
			catch (Jelly_Validation_Exception $e)
			{
				$this->_errors['user'] = $e->errors('validate');
			}
		}

		$post['user']      = $user_info;
		$post['user_data'] = $user_data_info;

		return $post;
	}

	public function _send_email($user, $password)
	{
		$message = View::factory('backend/content/email/user/credentials')
			->set('site', Kohana::$config->load('admin'))
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
