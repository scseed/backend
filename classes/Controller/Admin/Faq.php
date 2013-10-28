<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Template Controller Admin_faq
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 BlueFish <http://bluefish.ru>
 */
class Controller_Admin_Faq extends Controller_Admin_Template {

	public function action_index ()
	{
		$this->action_list();
	}

	public function action_list()
	{
		$faq_meta = Jelly::meta('faq');
		$faq = Jelly::query('faq')
			->select();

		$this->template->page_title = 'Список вопросов';
		$this->template->content = View::factory('backend/content/faq/list')
			->bind('faq', $faq)
			->bind('meta', $faq_meta);
	}

	public function action_add()
	{
		$is_active = array(0 => 'Не активен', 1 => 'Активен');
		$errors    = NULL;
		$post      = array(
			'question' => NULL,
			'answer' => NULL,
			'is_active' => TRUE,
		);

		if($this->request->method() === Request::POST)
		{
			$post_data = Arr::extract($_POST, array_keys($post));

			$faq = Jelly::factory('faq');
			$faq->set($post_data);

			try
			{
				$faq->save();
				$this->request->redirect(Route::url('admin', array('controller' => 'faq', 'action' => 'list')));
			}
			catch(Jelly_Validation_Exception $e)
			{
				$errors = $e->errors('validate');
			}

		    $post = Arr::merge($post_data, $post);
		}

		$this->template->page_title = 'Новый вопрос';
		$this->template->content = View::factory('backend/form/faq')
			->bind('post', $post)
			->bind('is_active', $is_active)
			->bind('errors', $errors)
		;
	}

	/**
	 * @todo написать chainable select для выбора модели автомобиля
	 */
	public function action_edit()
	{
		$faq_id = (int) $this->request->param('id');
		if( ! $faq_id)
			throw new HTTP_Exception_404();

		$faq       = Jelly::query('faq', $faq_id)->select();
		$is_active = array(0 => 'Не активен', 1 => 'Активен');
		$errors    = NULL;
		$post      = array(
			'question' => $faq->question,
			'answer' => $faq->answer,
			'is_active' => $faq->is_active,
		);

		if($this->request->method() === Request::POST)
		{
			$post_data = Arr::extract($_POST, array_keys($post));

			$faq->set($post_data);

			try
			{
				$faq->save();
				$this->request->redirect(Route::url('admin', array('controller' => 'faq', 'action' => 'list')));
			}
			catch(Jelly_Validation_Exception $e)
			{
				$errors = $e->errors('validate');
			}

		    $post = Arr::merge($post_data, $post);
		}

		$this->template->page_title = 'Редактирование вопроса "' . $faq->question . '"';
		$this->template->content = View::factory('backend/form/faq')
			->bind('post', $post)
			->bind('is_active', $is_active)
			->bind('errors', $errors)
		;
	}

	public function action_status()
	{
		$faq_id = (int) $this->request->param('id');
		if( ! $faq_id)
			throw new HTTP_Exception_404();

		$faq = Jelly::query('faq', $faq_id)->select();

		if($faq->loaded())
		{
			$faq->is_active = ! (bool) $faq->is_active;
			$faq->save();
		}

		$this->request->redirect(Route::url('admin', array('controller' => 'faq', 'action' => 'list')));
	}

} // End Controller_Admin_faq