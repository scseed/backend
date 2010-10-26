<?php defined('SYSPATH') or die('No direct script access.');

// Static file serving (CSS, JS, images)
Route::set('docs/media', 'admin/media(/<file>)', array('file' => '.+'))
	->defaults(array(
		'controller' => 'admin_home',
		'action'     => 'media',
		'file'       => NULL,
	));