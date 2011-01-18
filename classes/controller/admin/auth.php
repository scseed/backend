<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Template Controller User
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Controller_Admin_Auth extends Controller_Admin_Template
{

	public $template = 'backend/template/login';
	public $_auth_required = FALSE;

	/**
	 * Login action
	 */
	public function action_login()
	{
		if(Auth::instance('admin')->logged_in()) {
			if($url = Session::instance()->get('url')) {
				Request::instance()->redirect($url);
			}
			else
			{
				Request::instance()->redirect('admin');
			}
		}

		$post = array(
			'email' => NULL,
			'password' => NULL
		);
		$errors = array();
		if($_POST) {
			$post = Arr::extract($_POST, array('email', 'password', 'remember'));

			if(Auth::instance('admin')->login(
				$post['email'],
				$post['password'],
				! isset($post['remember']) ? TRUE : FALSE))
			{
				if($url = Session::instance()->get('url')) {
					Request::instance()->redirect($url);
				}
				else
				{
					Request::instance()->redirect('admin');
				}
			}
			else
			{
				$errors = array('common' => 'Неверное имя пользователя или пароль');
			}
		}
		$this->template->content = View::factory('backend/user/login')
			->bind('userdata', $post)
			->set('errors', $errors);
	}

	/**
	 * Logout action
	 */
	public function action_logout()
	{
		if(Auth::instance()->logged_in()) {
			$user_id = Auth::instance()->get_user()->id;
			Auth::instance()->logout();
		}
		Request::instance()->redirect('admin');
	}

	public function action_register()
	{
		$count_users = Jelly::query('user')->count();

	    if(! $count_users)
	    {
		    $errors = array();
		    $user = Jelly::factory('user');

		    if($_POST)
		    {
			    $post = Arr::extract($_POST, array('email', 'password', 'password_confirm', 'remember'));

		        $user->set($post);

		        try
		        {
			        $user->save();
			        $user->add('role', array('login', 'admin'));

		            Auth::instance('admin')->login(
			            $post['email'],
			            $post['password'],
			            !isset($post['remember']) ? TRUE : FALSE);
		            $this->request->redirect('admin');

		        }
		        catch(Validate_Exception $e)
		        {
			        $errors = $e->array->messages('common_validation');
		        }
		    }

		    $this->template->title = 'Регистрация администратора';
			$this->template->content = View::factory('backend/user/register')
				->bind('userdata', $user)
				->bind('errors', $errors);
	    }
	    else
	    {
		    Request::instance()->redirect('admin/auth/login');
	    }
	}
} // End Template Controller User
