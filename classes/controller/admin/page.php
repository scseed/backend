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

	public function action_edit()
	{
		$id = $this->request->param('id', NULL);

		if ($id === NULL)
			throw new HTTP_Exception_404();

		$system_languages = Jelly::query('system_lang')->select();
		$page = Jelly::query('page', (int) $id)->select();
		$parent = Jelly::query('page', (int) $page->parent_page->id)->select();
		$pages = $this->_get_pages_in_dropdown();

		$alias = $page->alias;

		$content = array();
		foreach($system_languages as $lang)
		{
			$content[$lang->abbr] = array(
				'content' => $page->get('page_contents')->where('lang', '=', $lang->id)->limit(1)->execute(),
				'lang'    => $lang,
			);
		}

		if($_POST)
		{
			$page_data = Arr::extract($_POST, array('parent', 'alias', 'is_active'));

		    $errors = $this->_save_page($system_languages, $content, $parent, $page, $page_data);
		}

	    $this->template->content = View::factory('backend/content/page/add')
			->bind('alias', $alias)
			->bind('page', $page)
			->bind('pages', $pages)
			->bind('parent', $parent)
			->bind('errors', $errors)
			->bind('content', $content)
			;
	}

	public function action_add()
	{
		$parent           = Arr::get($_GET, 'parent', 1);
		$parent           = Jelly::query('page', (int) $parent)->select();
		$system_languages = Jelly::query('system_lang')->select();
		$page             = Jelly::factory('page');

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

		$pages = $this->_get_pages_in_dropdown();

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

				$content = array();
				foreach($system_languages as $lang)
				{
					$content[$lang->abbr] = array(
						'content' => $page->get('page_contents')->where('lang', '=', $lang->id)->limit(1)->execute(),
						'lang'    => $lang,
					);
				}
			}

			$page_data = Arr::extract($_POST, array('parent', 'alias', 'is_active'));

			$errors = $this->_save_page($system_languages, $content, $parent, $page, $page_data);

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

	/**
	 * Saving page data
	 *
	 * @param  Jelly_Collection $system_languages
	 * @param  array            $content
	 * @param  integer          $parent
	 * @param  Jelly_Model      $page
	 * @param  array|null       $page_data
	 * @return array
	 */
	protected function _save_page(Jelly_Collection $system_languages, array $content, $parent, Jelly_Model $page,  array $page_data = NULL)
	{
		$errors = NULL;
		$page->set($page_data);

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
			$errors = $e->array->errors('common_validation');
		}

		foreach($system_languages as $lang)
		{
			$content[$lang->abbr]['content']->set(Arr::get($_POST, $lang->abbr));
			$content[$lang->abbr]['content']->page = $page->id;
			$content[$lang->abbr]['content']->type = 1;
			$content[$lang->abbr]['content']->lang = $lang->id;

			if($content[$lang->abbr]['content']->content)
			{
				try
				{
					$content[$lang->abbr]['content']->save();

				}
				catch(Jelly_Validation_Exception $e)
				{
					$errors[$lang->abbr] = $e->array->errors('common_validation');
				}
			}
		}

		if( ! $errors)
		{
			$this->request->redirect(Route::get('admin')->uri(array('controller' => 'page', 'action' => 'list')));
		}

		return $errors;
	}

	public function _get_pages_in_dropdown()
	{
		$_pages = Jelly::factory('page')->root(1);

		// Выделим все страницы, что находятся в этом скоупе
		$pages[1] = '~ Корень ~';
		foreach($_pages->children() as $_page)
		{
			$pages[$_page->id] = $_page->get('page_contents')
			        ->with('lang')
			        ->where('page_content:lang.abbr', '=', I18n::lang())
			        ->limit(1)
			        ->execute()
			        ->title;
			if($_page->has_children())
			{
				$title = $_page->get('page_contents')->with('lang')->where('page_content:lang.abbr', '=', I18n::lang())->limit(1)->execute()->title;
				$pages[$title] = array();

			    foreach($_page->children() as $children)
			    {
				    $pages[$title][$children->id] = $children->get('page_contents')->with('lang')->where('page_content:lang.abbr', '=', I18n::lang())->limit(1)->execute()->title;
			    }
			}
		}

		return $pages;
	}
} // End Controller_pages