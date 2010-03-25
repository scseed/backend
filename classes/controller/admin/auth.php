<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Template Controller User
 *
 * @author avis <smgladkovskiy@gmail.com>
 */
class Controller_Admin_Auth extends Controller_Admin_Template {

	public $template = 'backend/template/login';
	public $_auth_required = FALSE;

	/**
	 * Login action
	 */
	public function action_login ()
	{
		if(A1::instance()->logged_in())
		{
			if($url = Session::instance()->get('url'))
			{
				Request::instance()->redirect($url);
			}
			else
			{
				Request::instance()->redirect('admin');
			}
		}
		$user = Jelly::meta('user');

		$post = Validate::factory($_POST)
							->rules('email', $user->fields('email')->rules)
							->rules('password', $user->fields('password')->rules);
		if ($post->check())
		{
			if (A1::instance()->login(
				$post['email'],
				$post['password'],
				!isset($post['remember']) ? TRUE : FALSE))
			{
				Logapp::instance()->write(
					'login',
					'success',
					A1::instance()->get_user()->id,
					'Пользователь успешно вошёл в систему'
				);

				if($url = Session::instance()->get('url'))
				{
					Request::instance()->redirect($url);
				}
				else
				{
					Request::instance()->redirect('admin');
				}

			}
			else
			{
				Logapp::instance()->write(
					'login',
					'fail',
					NULL,
					'Неудачная попытка входа в систему.<br />Неверный логин или пароль ('.$post['email'].').'
				);
				
				$this->template->content = View::factory('backend/user/login')
					->set('userdata', $post->as_array())
					->set('errors', array('common' => __('Неверный логин или пароль')));
			}
		}
		else
		{
			$this->template->content = View::factory('backend/user/login')
				->set('userdata', $post->as_array())
				->set('errors', $post->errors('common_validation', TRUE));
		}
	}

	/**
	 * Logout action
	 */
	public function action_logout()
	{
		if(A1::instance()->logged_in())
		{
			$user_id = A1::instance()->get_user()->id;

			A1::instance()->logout();

			Logapp::instance()->write(
				'logout',
				'success',
				$user_id,
				'Пользователь вышел из системы'
			);
		}

		Request::instance()->redirect('admin');
	}

} // End Template Controller User
