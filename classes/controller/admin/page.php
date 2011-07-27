<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Template Controller pages
 *
 * @author Sergei  <smgladkovskiy@gmail.com>
 * @copyrignt
 */
class Controller_Admin_Page extends Controller_Admin_Template {

	protected $_page_data = NULL;
	protected $_errors = NULL;
	protected $_actions = array(
		'move' => array(
			'update'
		),
	);

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
		$parent_page = Jelly::query('page', $parent)->select();
		$root = Jelly::query('page', $parent)->execute();

		$_pages = $root->descendants(FALSE, 'ASC', TRUE);

		$pages_ids = array();
		foreach($_pages as $_page)
		{
			$pages_ids[] = $_page->id;
		}

		$pages = Page::instance()->pages_structure();

		if($parent != 1 AND $parent_page->loaded())
		{
			$pages = $this->_pages_structure_select($pages, $parent_page);
		}


		$this->template->page_title = 'Список Контентных страниц';
		$this->template->content = View::factory('backend/content/page/list')
			->bind('parent_lvl_id', $parent_page->parent_page->id)
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

		$_pages           = Jelly::factory('page')->root(1)->descendants(TRUE)->as_array();
		$_pages_content   = Jelly::query('page_content')->with('page')->with('lang')->select();
		$system_languages = Jelly::query('system_lang')->select();
		$_page_types      = Jelly::query('page_type')->select();
		$page             = Jelly::factory('page');

		$page_types = array();
		foreach($_page_types as $page_type)
		{
			$page_types[$page_type->id] = $page_type->name;
		}

		$pages_content = array();
		foreach($_pages_content as $_content)
		{
			$pages_content[$_content->page->id][$_content->lang->abbr] = $_content->as_array();
		}

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
		$pages = $this->_pages_structure($_pages, $pages_content);

		// Getting page contents by system languages
		foreach($system_languages as $lang)
		{
			$content[$lang->abbr] = array(
				'content' => Jelly::factory('page_content')->set('lang', '=', $lang->id),
				'lang'    => $lang,
			);
		}

		if($this->request->method() == Request::POST)
		{
			$this->_page_data = Arr::extract($this->request->post(), array('parent_page', 'type', 'alias', 'is_active', 'is_visible'));
			// Watch for page ID
			$page_id = $this->request->post('page_id', NULL);

			if( $page_id)
			{
				$page = Jelly::query('page', (int) $page_id)->select();

				foreach($system_languages as $lang)
				{
					$content[$lang->abbr] = array(
						'content' => $page->get('page_contents')->where('lang', '=', $lang->id)->limit(1)->execute(),
						'lang'    => $lang,
					);
				}
			}

			$page = $this->_save_page($page, $_page_types, $_pages);

			$this->_save_page_contents($content, $page, $system_languages);

			if( ! $this->_errors)
			{
				$query = ($page->parent_page->id) ? URL::query(array('parent' => $page->parent_page->id)) : NULL;

				$this->request->redirect(
					Route::get('admin')->uri(array('controller' => 'page', 'action' => 'list')).$query
				);
			}

			$alias = $this->_page_data['alias'];
		}

		$this->template->content = View::factory('backend/form/content/page')
			->bind('alias', $alias)
			->bind('page', $page)
			->bind('pages', $pages)
			->bind('parent', $parent)
			->bind('errors', $this->_errors)
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
		$_pages           = Jelly::factory('page')->root(1)->descendants(TRUE)->as_array();
		$_pages_content   = Jelly::query('page_content')->with('page')->with('lang')->select();
		$parent           = $page->parent_page;
		$system_languages = Jelly::query('system_lang')->select();
		$_page_types       = Jelly::query('page_type')->select();

		$page_types = array();
		foreach($_page_types as $page_type)
		{
			$page_types[$page_type->id] = $page_type->name;
		}

		$content = array();

		// Getting page contents by system languages
		foreach($system_languages as $lang)
		{
			$content[$lang->abbr] = array(
				'content' => $page->get('page_contents')->where('lang', '=', $lang->id)->limit(1)->select(),
				'lang'    => $lang,
			);
		}

		$pages_content = array();
		foreach($_pages_content as $_content)
		{
			$pages_content[$_content->page->id][$_content->lang->abbr] = $_content->as_array();
		}

		// Pages structure
		$pages = $this->_pages_structure($_pages, $pages_content);

		// If there is parent alias - set it as start
		$alias = $page->alias;

		if($this->request->method() == Request::POST)
		{
			$this->_page_data = Arr::extract($this->request->post(), array('parent_page', 'type', 'alias', 'is_active', 'is_visible'));

			// Saving page data
			$page = $this->_save_page($page, $_page_types, $_pages);

			// Saving page contents
			$this->_save_page_contents($content, $page, $system_languages);

			if( ! $this->_errors)
			{
				$query = ($page->parent_page->id) ? URL::query(array('parent' => $page->parent_page->id)) : NULL;

				$this->request->redirect(
					Route::get('admin')->uri(array('controller' => 'page', 'action' => 'list')).$query
				);
			}
			$alias = $this->_page_data['alias'];
		}

		$this->template->content = View::factory('backend/form/content/page')
			->bind('alias', $alias)
			->bind('page', $page)
			->bind('pages', $pages)
			->bind('parent', $parent)
			->bind('errors', $this->_errors)
			->bind('content', $content)
			->bind('page_types', $page_types)
		;
	}

	public function action_delete()
	{
		$page_id = $this->request->param('id');

		Jelly::query('page', $page_id)->select()->delete_obj();

		$this->request->redirect($this->request->referrer());
	}

	/**
	 * @throws Http_Exception_404
	 * @return void
	 */
	public function action_move()
	{
		$id        = (int) $this->request->param('id');
		$direction = Arr::get($_GET, 'direction', NULL);

		if( ! $id)
			throw new Http_Exception_404('Node id is not specified');

		$node = Jelly::query('page', $id)->select();

		if( ! $node->loaded())
			throw new Http_Exception_404('Menu node with id = :id was not found', array(':id' => $id));

		switch($direction)
		{
			case 'up':
				$sibling = Jelly::query('page')
					->where('scope', '=', $node->scope)
					->where('level', '=', $node->level)
					->where('right', '=', $node->left - 1)
					->limit(1)
					->select();

				if($sibling->loaded())
					$sibling->move_to_next_sibling($node);

				break;
			case 'down':
				$sibling = Jelly::query('page')
					->where('scope', '=', $node->scope)
					->where('level', '=', $node->level)
					->where('left', '=', $node->right + 1)
					->limit(1)
					->select();

				if($sibling->loaded())
					$node->move_to_next_sibling($sibling);
				break;
			default:
				break;
		}

		$this->request->redirect($this->request->referrer());
	}

	/**
	 * Generates Pages structure
	 *
	 * @param  array $root_page
	 * @param  array $pages_content
	 * @return array
	 */
	protected function _pages_structure(array $root_page, array $pages_content)
	{
		foreach($root_page as $_page)
		{
			$pages[$_page['id']] = ($_page['level'] > 0)
				? str_repeat('-', $_page['level']) . $pages_content[$_page['id']][I18n::lang()]['title']
				: $_page['alias'];
		}

		return $pages;
	}

	public function _pages_structure_select($pages_arr, $parent_page)
	{
		foreach($pages_arr as $id => $page)
		{
			if($parent_page->id == $id)
				return $page['childrens'];

			$this->_pages_structure_select($page['childrens'], $parent_page);
		}
	}

	public function _save_page(Jelly_Model $page, $_page_types, $_pages)
	{
		$pages_types = array();
		foreach($_page_types as $page_type)
		{
			$pages_types[$page_type->id] = $page_type;
		}

		$pages_info = array();
		foreach($_pages as $_page)
		{
			$pages_info[$_page['id']] = $_page;
		}

		$this->_page_data['alias'] = trim($this->_page_data['alias']);

		switch($pages_types[$this->_page_data['type']]->route_name)
		{
			case 'page':
				$page_params = unserialize($pages_info[$this->_page_data['parent_page']]['params']);

				$page_params['page_path'] = ($page_params)
					? $page_params['page_path'].'/'.$this->_page_data['alias']
					: $this->_page_data['alias'];
				$this->_page_data['params'] = serialize($page_params);
				break;
		}

		$page->set($this->_page_data);

		try
		{
			if($page->loaded())
			{
				$page->save();
			}
			elseif( ! $page->loaded() OR $page->changed('parent_page'))
			{
				$page->insert_as_last_child($this->_page_data['parent_page']);
			}
		}
		catch(Jelly_Validation_Exception $e)
		{
			$this->_errors = $e->errors('common_validation');
		}

		return $page;
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
		foreach($system_languages as $lang)
		{
			$page_content = $this->request->post($lang->abbr);

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
				$this->_errors[$lang->abbr] = $e->errors('common_validation');
			}
		}
	}
} // End Controller_pages