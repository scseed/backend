<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Template Controller admin_news
 *
 * @author avis <smgladkovskiy@gmail.com>
 */
class Controller_Admin_News extends Controller_Admin_Template {

	public function action_index()
	{
		$this->action_list();
	}

	public function action_list()
	{
		$news_meta = Jelly::meta('news');

		$news = Jelly::query('news')
			->order_by('pubdate', 'DESC');

			$count =  $news->count();
			$pagination = Pagination::factory(array(
				'total_items'    => $count,
				'items_per_page' => 10,
			));

		$news = $news->limit($pagination->items_per_page)->offset($pagination->offset)->select();
		$pagination = $pagination->render('pagination/floating');

		$this->template->page_title = 'Список новостей';
		$this->template->content = View::factory('backend/content/news/list')
			->bind('news', $news)
			->bind('pagination', $pagination)
			->bind('meta', $news_meta);
	}

	public function action_add()
	{
		$errors = NULL;
		$post = array(
		    "pubdate" => NULL,
		    "title" => NULL,
		    "longtitle" => NULL,
		    "introtext" => NULL,
		    "text" => NULL,
		    "is_active" => TRUE,
		);

		if($this->request->method() === Request::POST)
		{
			$news = Jelly::factory('news');

			$post_data = Arr::extract($_POST, array_keys($post));

			$news->set($post_data);

			try
			{
				$news->save();
				HTTP::redirect(Route::url('admin', array('controller' => 'news', 'action' => 'list')));
			}
			catch(Jelly_Validation_Exception $e)
			{
				$errors = $e->errors('validate');
			}

		    $post = Arr::merge($post_data, $post);
		}

		$this->template->page_title = 'Новая новость';
		$this->template->content = View::factory('backend/form/news')
			->bind('post', $post)
			->bind('errors', $errors)
		;
	}

	/**
	 * @todo написать chainable select для выбора модели автомобиля
	 */
	public function action_edit()
	{
		$news_id = (int) $this->request->param('id');

		if( ! $news_id)
			throw new HTTP_Exception_404('News id is not specified!');

		$news      = Jelly::query('news', (int) $news_id)->select();

		$post = array(
		    "pubdate"   => date('d.m.Y', $news->pubdate),
		    "title"     => $news->title,
		    "longtitle" => $news->longtitle,
		    "introtext" => $news->introtext,
		    "text"      => $news->text,
		    "is_active" => $news->is_active,
		);

		if($this->request->method() === Request::POST)
		{
			$post_data = Arr::extract($_POST, array_keys($post));

			$news->set($post_data);

			try
			{
				$news->save();
				HTTP::redirect(Route::url('admin', array('controller' => 'news', 'action' => 'list')));
			}
			catch(Jelly_Validation_Exception $e)
			{
				$errors = $e->errors('validate');
			}
		}

		$this->template->page_title = $news->title.' <small>Редактирование</small>';
		$this->template->content = View::factory('backend/form/news')
			->bind('post', $post)
			->bind('errors', $errors);
	}

	public function action_status()
	{
		$news_id = (int) $this->request->param('id');

		if( ! $news_id)
			throw new HTTP_Exception_404('News id is not specified!');

		$news = Jelly::query('news', $news_id)->select();

		if($news->loaded())
		{
			$news->is_active = ! (bool) $news->is_active;
			$news->save();
		}

		HTTP::redirect(Route::url('admin', array('controller' => 'news', 'action' => 'list')));
	}

	public function after()
	{
		parent::after();
		if ($this->auto_render)
		{
			$styles = array(
				'js/jquery/ui_theme/theme.css' => 'screen, projection',
			);

			$scripts = array(
				'js/jquery/jquery-ui.js',
			);

			$this->template->styles = array_merge( $this->template->styles, $styles );
			$this->template->scripts = array_merge( $this->template->scripts, $scripts );
		}
	}
} // End Template Controller admin_news
