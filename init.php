<?php defined('SYSPATH') or die('No direct script access.');

// Static file serving (CSS, JS, images)
Route::set('docs/media', 'admin/media(/<file>)', array('file' => '.+'))
	->defaults(array(
		'controller' => 'admin_home',
		'action'     => 'media',
		'file'       => NULL,
	));

Route::set('docs/media', 'admin/images')
	->defaults(array(
		'controller' => 'admin_home',
		'action'     => 'images',
	));

Route::set('admin', 'admin(/<controller>(/<action>(/<id>)))')
	->defaults(array(
		'directory' => 'admin',
		'controller' => 'home',
		'action' => 'index',
));