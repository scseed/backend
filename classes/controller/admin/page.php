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

	/**
	 * Pages list
	 *
	 * @return void
	 */
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
				->where('page_content:lang.abbr', '=', I18n::lang())
				->where('page_content:page.id', 'IN', $pages_ids)
				->execute()
			: array();

		$this->template->page_title = 'Список Контентных страниц';
		$this->template->content = View::factory('backend/content/page/list')
			->bind('pages', $pages);
	}

	/**
	 * Adding new page
	 *
	 * @return void
	 */
	public function action_add()
	{
		// Get parent page
		$parent = Arr::get($_GET, 'parent', 1);
		$parent = Jelly::query('page', (int) $parent)->select();

		$errors = NULL;

		$_pages           = Jelly::factory('page')->root(1);
		$system_languages = Jelly::query('system_lang')->select();
		$page_types       = Jelly::query('page_type')->select()->as_array('id', 'name');
		$page             = Jelly::factory('page');

		// If there no global parent page - create it
		if( ! $parent->loaded())
		{
			if( ! count($page_types))
			{
				Jelly::factory('page_type')->set(array('name' => 'text'))->save();
				$page_types       = Jelly::query('page_type')->select();
			}

			$static_page_type_id = NULL;
			foreach($page_types as $id => $page_type_name)
			{
				if($page_type_name == 'text')
				$static_page_type_id = $id;
			}

			$pages_root = Jelly::factory('page')->set(array(
				'alias'     => NULL,
				'is_active' => FALSE,
				'type'      => $static_page_type_id
			))->save();
			$pages_root->insert_as_new_root();
			$parent = $pages_root;
		}

		// Pages structure
		$pages = $this->_pages_structure($_pages);

		// Getting page contents by system languages
		foreach($system_languages as $lang)
		{
			$content[$lang->abbr] = array(
				'content' => Jelly::factory('page_content')->set('lang', '=', $lang->id),
				'lang'    => $lang,
			);
		}

		// If there is parent alias - set it as start
		$alias = ($parent->alias) ?  $parent->alias . '/' : NULL;


		if($_POST)
		{
			// Watch for page ID
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

			$page_data = Arr::extract($_POST, array('parent_page', 'alias', 'is_active', 'type'));

			$page->set($page_data);

			try
			{
				if($page->loaded())
				{
					$page->save();
				}
				else
				{
					$page->insert_as_last_child($page_data['parent_page']);
				}
			}
			catch(Jelly_Validation_Exception $e)
			{
				$errors = $e->errors('common_validation');
			}

			$errors = $this->_save_page_contents($content, $page, $system_languages);

			if( ! $errors)
			{
				$query = ($parent) ? URL::query(array('parent' => $parent->id)) : NULL;

				$this->request->redirect(Route::get('admin')->uri(array('controller' => 'page', 'action' => 'list')).$query);
			}

			$alias = $page_data['alias'];
		}

		$this->template->content = View::factory('backend/form/content/page')
			->bind('alias', $alias)
			->bind('page', $page)
			->bind('pages', $pages)
			->bind('parent', $parent)
			->bind('errors', $errors)
			->bind('content', $content)
			->bind('page_types', $page_types)
		;
	}

	/**
	 * Editing page
	 *
	 * @return void
	 */
	public function action_edit()
	{
		$id = $this->request->param('id');

		$page             = Jelly::query('page', (int) $id)->select();
		$_pages           = Jelly::factory('page')->root(1);
		$parent           = $page->parent_page;
		$system_languages = Jelly::query('system_lang')->select();

		$errors = NULL;
		$content = array();

		// Getting page contents by system languages
		foreach($system_languages as $lang)
		{
			$content[$lang->abbr] = array(
				'content' => $page->get('page_contents')->where('lang', '=', $lang->id)->limit(1)->select(),
				'lang'    => $lang,
			);
		}

		// Pages structure
		$pages = $this->_pages_structure($_pages);


		// If there is parent alias - set it as start
		$alias = $page->alias;

		if($_POST)
		{
			$page_data = Arr::extract($_POST, array('parent_page', 'alias', 'is_active'));

			$page->set($page_data);

			try
			{
				$page->save();
			}
			catch(Jelly_Validation_Exception $e)
			{
				$errors = $e->errors('common_validation');
			}

			// Saving page contents
			$errors = $this->_save_page_contents($content, $page, $system_languages);

			if( ! $errors)
			{
				$query = ($parent) ? URL::query(array('parent' => $parent->id)) : NULL;

				$this->request->redirect(Route::get('admin')->uri(array('controller' => 'page', 'action' => 'list')).$query);
			}
			$alias = $page_data['alias'];
		}

		$this->template->content = View::factory('backend/form/content/page')
			->bind('alias', $alias)
			->bind('page', $page)
			->bind('pages', $pages)
			->bind('parent', $parent)
			->bind('errors', $errors)
			->bind('content', $content);
	}

	public function action_delete()
	{
		$page_id = $this->request->param('id');

		Jelly::query('page', $page_id)->select()->delete_obj();

		$this->request->redirect($this->request->referrer());
	}

	/**
	 * Generates Pages structure
	 *
	 * @param Jelly_Model $root_page
	 * @return array
	 */
	protected function _pages_structure(Jelly_Model $root_page)
	{
		$pages[$root_page->id] = '~ '.__('Pages Root').' ~';

		foreach($root_page->children() as $_page)
		{
			$title = $_page->get('page_contents')->where('page_content:lang.abbr', '=', I18n::lang())->limit(1)->execute()->title;
			if($_page->has_children())
			{
				$pages[$title] = array($_page->id => $title);

				foreach($_page->children() as $children)
				{
					$pages[$title][$children->id] = $children->get('page_contents')->where('page_content:lang.abbr', '=', I18n::lang())->limit(1)->execute()->title;
				}
			}
			else
			{
				$pages[$_page->id] = $title;
			}
		}

		return $pages;
	}

	/**
	 * Saves page contents
	 *
	 * @param array $content
	 * @param Jelly_Model $page
	 * @param Jelly_Collection $system_languages
	 * @return null|array
	 */
	protected function _save_page_contents(array $content, Jelly_Model $page, Jelly_Collection $system_languages)
	{
		$errors = NULL;
		foreach($system_languages as $lang)
		{
			$page_content = Arr::get($_POST, $lang->abbr);

			if($page_content['title'] == '')
				continue;

			$content[$lang->abbr]['content']->set($page_content);
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

		return $errors;
	}
} // End Controller_pages