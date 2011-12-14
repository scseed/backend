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
		$parent = Arr::get($_GET, 'parent', NULL);
		$parent_page = Jelly::query('page', $parent)->select();
		$roots = Jelly::query('page')
			->where('parent_page', '=', $parent)
			->execute();
		$_page_contents = Jelly::query('page_content')
			->with('lang')
			->with('page')
			->active()
			->select();

		foreach($_page_contents as $page_content)
		{
			$page_contents[$page_content->page->id][] = $page_content->lang->locale_name;
		}

		$_pages = array();
		$multiple_roots = FALSE;
		if(count($roots) == 1)
		{
			$_pages = $roots[0]->descendants(TRUE, 'ASC', TRUE);
		}
		elseif(count($roots) > 1 AND $parent_page instanceof Jelly_Model AND $parent_page->loaded())
		{
			$_pages = $parent_page->descendants(FALSE, 'ASC', TRUE);
		    $multiple_roots = TRUE;
		}
		elseif(count($roots) > 1 AND $parent == NULL)
		{
			foreach($roots as $root)
			{
				$_pages = $root->descendants(TRUE, 'ASC', TRUE);
			}
			$multiple_roots = TRUE;
		}

		$pages_ids = array();
		foreach($_pages as $_page)
		{
			$pages_ids[] = $_page->id;
		}

		$lang = ($parent_page instanceof Jelly_Model AND $parent_page->loaded())
			? $parent_page->root($parent_page->scope())->alias
			: NULL;

		$pages = Page::instance()->pages_structure($multiple_roots, $lang);

		if($parent != NULL AND $parent_page->loaded())
		{
			$pages = $this->_pages_structure_select($pages, $parent_page->id);
		}

		foreach($pages as $id => $page)
		{
			$pages[$id]['langs'] = (isset($page_contents[$id]))
				? $page_contents[$id]
				: array();
		}

		$this->template->page_title = 'Список Контентных страниц';
		$this->template->content = View::factory('backend/content/page/list')
			->set('parent_lvl', $parent_page)
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
		$parent = (int) Arr::get($_GET, 'parent', NULL);
		$parent = ($parent)
			? Jelly::query('page', $parent)->select()
			: NULL;

		$_pages_content   = Jelly::query('page_content')->with('page')->with('lang')->select();
		$system_languages = Jelly::query('system_lang')->select();
		$_page_types      = Jelly::query('page_type')->select();
		$page             = Jelly::factory('page');
		$_roots           = Jelly::query('page')->where('parent_page', '=', NULL)->select();

		$_pages = array();
		foreach($_roots as $root)
		{
			$_pages = array_merge($_pages, $root->descendants(TRUE)->as_array());
		}

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

		$parent_root = ($parent) ? $parent->root($parent->scope) : NULL;

		// Pages structure
		$pages = array(0 => __('/'));
		$pages += $this->_pages_structure($_pages, $pages_content, $parent_root);

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

			if( ! $page->is_root())
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

		$page              = Jelly::query('page', (int) $id)->select();
		$_pages_content    = Jelly::query('page_content')->with('page')->with('lang')->select();
		$parent            = $page->parent_page;
		$system_languages  = Jelly::query('system_lang')->select();
		$_page_types       = Jelly::query('page_type')->select();
		$_roots            = Jelly::query('page')->where('parent_page', '=', NULL)->select();

		$_pages = array();
		foreach($_roots as $root)
		{
			$_pages = array_merge($_pages, $root->descendants(TRUE)->as_array());
		}

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
		$pages  = array(0 => __('/'));
		$pages += $this->_pages_structure($_pages, $pages_content, $parent);

		// If there is parent alias - set it as start
		$alias = $page->alias;

		if($this->request->method() == Request::POST)
		{
			$this->_page_data = Arr::extract($this->request->post(), array('parent_page', 'type', 'alias', 'is_active', 'is_visible'));

			// Saving page data
			$page = $this->_save_page($page, $_page_types, $_pages);

			// Saving page contents
			if( ! $page->is_root() AND ! $this->_errors)
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
	 * Page deleting
	 *
	 * @return void
	 */
	public function action_delete()
	{
		$page_id = $this->request->param('id');

		Jelly::query('page', $page_id)->select()->delete_obj();

		$this->request->redirect($this->request->referrer());
	}

	/**
	 * Page moving
	 *
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
	 * @param  array            $root_page
	 * @param  array            $pages_content
	 * @param  null|Jelly_Model $parent_root
	 * @return array
	 */
	protected function _pages_structure(array $root_page, array $pages_content, $parent_root)
	{
		$scope_root = ($parent_root) ? $parent_root->root($parent_root->scope) : NULL;
		$scope_lang = ($scope_root) ? $scope_root->alias : I18n::lang();
		$default_lang = I18n::lang();

		foreach($root_page as $_page)
		{
			$content = Arr::get($pages_content, $_page['id'], array());
			$title = NULL;
			foreach($content as $_lang => $_title)
			{
				if($_lang == $scope_lang)
					$title = $_title;
			}

			if($title == NULL)
			{
				foreach($content as $_lang => $_title)
				{
					if($_lang == $default_lang)
					{
						$title = $_title;
					}
				}
			}

			$pages[$_page['id']] = ($_page['level'] > 0)
				? str_repeat('-', $_page['level']) . $title['title']
				: $_page['alias'];
		}

		return $pages;
	}

	/**
	 * Extracting child nodes of current $parent_page in $pages_arr
	 *
	 * @recursive
	 * @param  array $pages_arr
	 * @param  int   $parent_page
	 * @return array
	 */
	public function _pages_structure_select($pages_arr, $parent_page)
	{
		static $_page = array();

		foreach($pages_arr as $page)
		{
			if($page['id'] == $parent_page)
			{
				$_page = $page['childrens'];
			}

			$this->_pages_structure_select($page['childrens'], $parent_page);
		}

		return ($_page) ? $_page : NULL;
	}

	/**
	 * Saving page procedure
	 *
	 * @param  Jelly_Model      $page
	 * @param  Jelly_Collection $_page_types
	 * @param  Jelly_Collection $_pages
	 * @return Jelly_Model
	 */
	public function _save_page(Jelly_Model $page, $_page_types, $_pages)
	{
		$this->_page_data['alias'] = trim($this->_page_data['alias']);
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

		$parent_page = Arr::get($pages_info, $this->_page_data['parent_page'], NULL);

		switch($pages_types[$this->_page_data['type']]->route_name)
		{
			case 'page':
				$page_params = unserialize(Arr::get($parent_page, 'params'));

				$page_params['page_path'] = ($page_params)
					? $page_params['page_path'].'/'.$this->_page_data['alias']
					: $this->_page_data['alias'];
				$this->_page_data['params'] = ($parent_page) ? serialize($page_params) : NULL;
				break;
		}

		$page->set($this->_page_data);

		try
		{
			if($page->loaded())
			{
				$new_position = FALSE;

				if($page->changed('parent_page'))
				{
					$new_position = TRUE;
				}

				$page->save();

				if($new_position)
					$page->move_to_last_child($this->_page_data['parent_page']);
			}
			elseif( ! $page->loaded() AND $parent_page)
			{
				$page->insert_as_last_child($this->_page_data['parent_page']);
			}
			elseif( ! $page->loaded() AND ! $parent_page)
			{
				$scope = Jelly::factory('page')->get_scopes()->count();
				$page->insert_as_new_root($scope+1);
			}
		}
		catch(Jelly_Validation_Exception $e)
		{
			$this->_errors = $e->errors('validate');
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
			$page_content['title'] = trim($page_content['title']);
			$page_content['long_title'] = trim($page_content['long_title']);

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
				$this->_errors[$lang->abbr] = $e->errors('validate');
			}
		}
	}
} // End Controller_pages