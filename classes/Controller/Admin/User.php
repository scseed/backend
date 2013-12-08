<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Template Controller user
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Controller_Admin_User extends Controller_Admin_Template {

	protected $_errors = NULL;

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
				"last_name" => NULL,
				"first_name" => NULL,
				"patronymic" => NULL,
				"email" => NULL,
				"roles" => array(),
				"password" => NULL,
				"password_confirm" => NULL,
				"is_active" => TRUE,
			),
		);

		if ($this->request->method() === Request::POST)
		{
			$post_data = Arr::extract($_POST, array_keys($post), NULL);

			if($post_data['user']['id'] == '')
				unset($post_data['user']['id']);

			$post = $this->_add_edit($post_data);
		}

		$referer = Session::instance()->get('url', Route::url('admin', array('controller' => $this->request->controller(), 'action' => 'list')));
		$this->template->content = View::factory('backend/form/user/new')
			->bind('cancel_link', $referer)
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

		if( ! $user->loaded())
			throw new HTTP_Exception_404(__('User not found'));

		$roles    = Jelly::query('role')->select()->as_array('id', 'description');
		$statuses = array(1 => __('Активен'), 0 => __('Отключён'));
		$post     = array(
			"user" => array(
				'id' => $user->id,
				"last_name" => $user->last_name,
				"first_name" => $user->first_name,
				"patronymic" => $user->patronymic,
				"email" => $user->email,
				"roles" => $user->roles->as_array('name', 'id'),
				"password" => NULL,
				"password_confirm" => NULL,
				"is_active" => $user->is_active,
			),
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
	 */
	public function action_delete ()
	{
		$id = $this->request->param('id');
		$user = Jelly::query('user', (int) $id)->select();

		$user->user_data->delete();

		HTTP::redirect($this->request->referrer());
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

		$user      = (Arr::get($user_info, 'id'))
			? Jelly::query('user', (int) Arr::get($user_info, 'id'))->select()
			: Jelly::factory('user');

		if($user->loaded())
		{
			$action = 'update';
		}

		if(empty($this->_errors))
		{
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

				$referer = Session::instance()->get_once('url', Route::url('admin', array('controller' => 'user', 'action' => 'list')));
				HTTP::redirect($referer);
			}
			catch (Jelly_Validation_Exception $e)
			{
				$this->_errors['user'] = $e->errors('validate');
			}
		}

		$post['user']      = $user_info;

		return $post;
	}

	public function _send_email($user, $password)
	{
		$admin_config = Kohana::$config->load('admin');
		$message = View::factory('backend/content/email/user/credentials')
			->set('site', $admin_config)
			->bind('user', $user)
			->bind('password', $password)
		;
		$site_config = Kohana::$config->load('site');
		$email = Email::factory('Создание учётной записи', $message, 'text/html')
			->to($user->email)
			->from($admin_config->support_email)
			->send()
		;
	}

} // End Template Controller user
