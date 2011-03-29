<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Template Controller pages
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyrignt
 */
class Controller_Admin_Page extends Controller_Admin_Template {

	public function action_list ()
	{
		$parent = Arr::get($_GET, 'parent', 1);

		$pages = Jelly::query('page_content')
				->with('lang')
				->with('page')
				->where('page_content:lang.abbr', '=', 'ru')
				->where('page_content:page.parent', '=', $parent)
				->execute();

		$this->template->page_title = 'Список Контентных страниц';
		$this->template->content = View::factory('backend/content/page/list')
			->bind('pages', $pages);
	}

	public function action_edit($id = NULL)
	{
		$errors = NULL;

		$page = Jelly::query('page', (int) $id)->select();

		$ru_content = $page->get('page_contents')->with('lang')->where('page_content:lang.abbr', '=', 'ru')->limit(1)->select();
		$en_content = $page->get('page_contents')->with('lang')->where('page_content:lang.abbr', '=', 'en')->limit(1)->select();

		if($_POST)
		{
			$page_data = Arr::extract($_POST, array('alias', 'is_active'));
		    $ru_content_data = Arr::get($_POST, 'ru');
		    $en_content_data = Arr::get($_POST, 'en');

		    $ru_content_data['page'] = $en_content_data['page'] = $page->id;
		    $ru_content_data['lang'] = 1;
		    $en_content_data['lang'] = 2;

		    $page->set($page_data);
		    $ru_content->set($ru_content_data);
		    $en_content->set($en_content_data);

		    try
		    {
			    $page->save();
		    }
		    catch(Validation_Exception $e)
		    {
			    $errors = $e->array->errors('common_validation');
		    }

		    if(! $errors)
		    {
			    try
				{
					$ru_content->save();
				}
				catch(Validation_Exception $e)
				{
					$errors = $e->array->errors('common_validation');
				}
		    }

		    if(! $errors)
		    {
			    try
				{
					$en_content->save();
				}
				catch(Validation_Exception $e)
				{
					$errors = $e->array->errors('common_validation');
				}
		    }

		    if( ! $errors)
		    {
			    $this->request->redirect('admin/page/list');
		    }
		}

	    $this->template->content = View::factory('backend/content/page/edit')
			->bind('page', $page)
			->bind('errors', $errors)
			->bind('ru_content', $ru_content)
			->bind('en_content', $en_content);
	}

	public function action_add()
	{
		$parent = Arr::get($_GET, 'parent', 1);
		$parent = Jelly::query('page', (int) $parent)->select();

		$_pages = Jelly::factory('page')->root(1);

		$pages[$parent->id] = '- // -';
		foreach($_pages->children() as $page)
		{
			if($page->has_children())
			{
				$pages[$page->alias] = array($page->id => $page->get('page_contents')
					->with('lang')
					->where('page_content:lang.abbr', '=', 'ru')->limit(1)->execute()->title);

			    foreach($page->children() as $children)
			    {
				    $pages[$page->alias][$children->id] = $children->get('page_contents')
				        ->with('lang')
				        ->where('page_content:lang.abbr', '=', 'ru')->limit(1)->execute()->title;
			    }
			}
		    else
		    {
			    $pages[$page->id] = $page->get('page_contents')
			        ->with('lang')
			        ->where('page_content:lang.abbr', '=', 'ru')->limit(1)->execute()->title;
		    }
		}

	    $errors = NULL;

		$page = Jelly::factory('page');

		$ru_content = Jelly::factory('page_content')->set('lang', '=', 1);
		$en_content = Jelly::factory('page_content')->set('lang', '=', 2);

		$alias = $parent->alias.'/';

		if($_POST)
		{
			$page_id = Arr::get($_POST, 'page_id', NULL);
			if( ! $page_id)
			{
				$page = Jelly::query('page', (int) $page_id)->select();
				$alias = $page->alias;
			    $ru_content = $page->get('page_contents')->with('lang')->where('page_content:lang.abbr', '=', 'ru')->limit(1)->execute();
				$en_content = $page->get('page_contents')->with('lang')->where('page_content:lang.abbr', '=', 'en')->limit(1)->execute();
			}


			$page_data = Arr::extract($_POST, array('parent', 'alias', 'is_active'));
		    $ru_content_data = Arr::get($_POST, 'ru');
		    $en_content_data = Arr::get($_POST, 'en');
		    $parent = Arr::get($_POST, 'parent', $parent);
		    $ru_content_data['lang'] = 1;
		    $en_content_data['lang'] = 2;

		    $page->set($page_data);


		    try
		    {
			    if($page_id != '')
			    {
				    $page->save();
			    }
			    else
			    {
				    $page->insert_as_last_child($parent);
			    }
		    }
		    catch(Validation_Exception $e)
		    {
			    $errors = $e->array->errors('common_validation');
		    }

		    $ru_content_data['page'] = $en_content_data['page'] = $page->id;

		    $ru_content->set($ru_content_data);
		    if(! $errors)
		    {
			    try
				{
					$ru_content->save();
				}
				catch(Validation_Exception $e)
				{
					$errors = $e->array->errors('common_validation');
				}
		    }

		    $en_content->set($en_content_data);
		    if(! $errors)
		    {
			    try
				{
					$en_content->save();
				}
				catch(Validation_Exception $e)
				{
					$errors = $e->array->errors('common_validation');
				}
		    }

		    if( ! $errors)
		    {
			    $this->request->redirect(
					Route::get('admin')
			            ->uri(array('controller' => 'page', 'action' => 'list'))
			        );
		    }

		    $alias = $page_data['alias'];
		}

	    $this->template->content = View::factory('backend/content/page/add')
			->bind('alias', $alias)
			->bind('page', $page)
			->bind('pages', $pages)
			->bind('parent', $parent)
			->bind('errors', $errors)
			->bind('ru_content', $ru_content)
			->bind('en_content', $en_content);
	}
} // End Controller_pages