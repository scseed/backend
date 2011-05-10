<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Template Controller pages
 *
 * @author Sergei  <smgladkovskiy@gmail.com>
 * @copyrignt
 */
class Controller_Admin_Page extends Controller_Admin_Template {

	public function action_index ()
	{
		$this->action_list();
	}

	public function action_list ()
	{
		$parent = Arr::get($_GET, 'parent', 1);

		$root = Jelly::query('page', $parent)->execute();

		$_pages = $root->descendants(FALSE, 'ASC', TRUE);

		$pages_ids = array();
		foreach($_pages as $_page)
		{
			$pages_ids[] = $_page->id;
		}

		$pages = ($pages_ids)
			? Jelly::query('page_content')
				->with('lang')
				->with('page')
				->where('page_content:lang.abbr', '=', I18n::lang())
				->where('page_content:page.id', 'IN', $pages_ids)
				->execute()
			: array();

		$this->template->page_title = 'Список Контентных страниц';
		$this->template->content = View::factory('backend/content/page/list')
			->bind('pages', $pages);
	}

	public function action_add()
	{
		$parent = Arr::get($_GET, 'parent', 1);
		$parent = Jelly::query('page', (int) $parent)->select();
		$_pages = Jelly::factory('page')->root(1);
		$system_languages = Jelly::query('system_lang')->select();
		$errors = NULL;
		$page = Jelly::factory('page');

		// Если $parent не оказался, да ещё и не существует в БД, создадим...
		if( ! $parent->loaded())
		{
			$pages_root = Jelly::factory('page')->set(array(
				'alias'     => NULL,
				'is_active' => FALSE
		    ))->save();
			$pages_root->insert_as_new_root();
			$parent = $pages_root;
		}

		// Выделим все страницы, что находятся в этом скоупе
		$pages[$parent->id] = '~ Корень ~';
		foreach($_pages->children() as $_page)
		{
			if($_page->has_children())
			{
				$pages[$_page->alias] = array($_page->id => $_page->get('page_contents')->with('lang')->where('lang.abbr', '=', 'ru')->limit(1)->execute()->title);

			    foreach($_page->children() as $children)
			    {
				    $pages[$_page->alias][$children->id] = $children->get('page_contents')->with('lang')->where('lang.abbr', '=', 'ru')->limit(1)->execute()->title;
			    }
			}
		    else
		    {
			    $pages[$_page->id] = $_page->get('page_contents')
			        ->with('lang')
			        ->where('page_content:lang.abbr', '=', 'ru')
			        ->limit(1)
			        ->execute()
			        ->title;
		    }
		}

		// Получим контент для всех системных языков
		foreach($system_languages as $lang)
		{
			$content[$lang->abbr] = array(
				'content' => Jelly::factory('page_content')->set('lang', '=', $lang->id),
				'lang'    => $lang,
			);
		}

		// Если есть родительский элемент, пропишем его стартовый алиас
		$alias = ($parent->alias) ?  $parent->alias . '/' : NULL;


		if($_POST)
		{
			$page_id = Arr::get($_POST, 'page_id', NULL);

			if( $page_id)
			{
				$page = Jelly::query('page', (int) $page_id)->select();
				$alias = $page->alias;

				foreach($system_languages as $lang)
				{
					$content[$lang->abbr] = array(
						'content' => $page->get('page_contents')->where('lang', '=', $lang->id)->limit(1)->execute(),
						'lang'    => $lang,
					);
				}
			}

			$page_data = Arr::extract($_POST, array('parent', 'alias', 'is_active'));

		    $parent_id = Arr::get($_POST, 'parent');

		    $page->set($page_data);
			$page->parent = $parent_id;

		    try
		    {
			    if($page->loaded())
			    {
				    $page->save();
			    }
			    else
			    {
				    $page->insert_as_last_child($parent);
			    }
		    }
		    catch(Jelly_Validation_Exception $e)
		    {
			    $errors = $e->errors('common_validation');
		    }

			foreach($system_languages as $lang)
			{
				$content[$lang->abbr]['content']->set(Arr::get($_POST, $lang->abbr));
				$content[$lang->abbr]['content']->page = $page->id;
				$content[$lang->abbr]['content']->type = 1;
				$content[$lang->abbr]['content']->lang = $lang->id;

				try
				{
					$content[$lang->abbr]['content']->save();

				}
				catch(Jelly_Validation_Exception $e)
				{
					$errors[$lang->abbr] = $e->errors('common_validation');
				}
			}

		    if( ! $errors)
		    {
			    $this->request->redirect(Route::get('admin')->uri(array('controller' => 'page', 'action' => 'list')));
		    }

		    $alias = $page_data['alias'];
		}

	    $this->template->content = View::factory('backend/content/page/add')
			->bind('alias', $alias)
			->bind('page', $page)
			->bind('pages', $pages)
			->bind('parent', $parent)
			->bind('errors', $errors)
			->bind('content', $content);
	}

	public function action_edit()
	{
		$id = $this->request->param('id');

		$errors = NULL;

		$_pages = Jelly::factory('page')->root(1);
		$page = Jelly::query('page', (int) $id)->select();
		$parent = $page->parent;
		$system_languages = Jelly::query('system_lang')->select();

		// Получим контент для всех системных языков
		foreach($system_languages as $lang)
		{
			$content[$lang->abbr] = array(
				'content' => $page->get('page_contents')->where('lang', '=', $lang->id)->limit(1)->select(),
				'lang'    => $lang,
			);
		}

		// Выделим все страницы, что находятся в этом скоупе
		$pages[$_pages->id] = '~ Корень ~';
		foreach($_pages->children() as $_page)
		{
			if($_page->has_children())
			{
				$pages[$_page->alias] = array($_page->id => $_page->get('page_contents')->with('lang')->where('page_content:lang.abbr', '=', 'ru')->limit(1)->execute()->title);

			    foreach($_page->children() as $children)
			    {
				    $pages[$_page->alias][$children->id] = $children->get('page_contents')->with('lang')->where('page_content:lang.abbr', '=', 'ru')->limit(1)->execute()->title;
			    }
			}
		    else
		    {
			    $pages[$_page->id] = $_page->get('page_contents')
			        ->with('lang')
			        ->where('page_content:lang.abbr', '=', 'ru')
			        ->limit(1)
			        ->execute()
			        ->title;
		    }
		}

		exit(Debug::vars($pages) . View::factory('profiler/stats'));

		// Если есть родительский элемент, пропишем его стартовый алиас
		$alias = $page->alias;

		if($_POST)
		{
			$page_data = Arr::extract($_POST, array('alias', 'is_active'));
			$parent_id = Arr::get($_POST, 'parent');

		    $page->set($page_data);
			$page->parent = $parent_id;

			try
			{
				$page->save();
			}
			catch(Jelly_Validation_Exception $e)
			{
				$errors = $e->errors('common_validation');
			}

		    foreach($system_languages as $lang)
			{
				$content[$lang->abbr]['content']->set(Arr::get($_POST, $lang->abbr));
				$content[$lang->abbr]['content']->page = $page->id;
				$content[$lang->abbr]['content']->type = 1;
				$content[$lang->abbr]['content']->lang = $lang->id;

				try
				{
					$content[$lang->abbr]['content']->save();

				}
				catch(Jelly_Validation_Exception $e)
				{
					$errors[$lang->abbr] = $e->errors('common_validation');
				}
			}

		    if( ! $errors)
		    {
			    $this->request->redirect('admin/page/list');
		    }
		}

	    $this->template->content = View::factory('backend/content/page/add')
			->bind('alias', $alias)
			->bind('page', $page)
			->bind('pages', $pages)
			->bind('parent', $parent)
			->bind('errors', $errors)
			->bind('content', $content);
	}
} // End Controller_pages