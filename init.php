<?php defined('SYSPATH') or die('No direct script access.');

// Static file serving (CSS, JS, images)
Route::set('docs/media', 'admin/media(/<file>)', array('file' => '.+'))
	->defaults(array(
		'controller' => 'admin_home',
		'action'     => 'media',
		'file'       => NULL,
	));

Route::set('admin', 'admin(/<controller>(/<action>(/<id>)))')
	->defaults(array(
		'directory' => 'admin',
		'controller' => 'home',
		'action' => 'index',
));
Route::set('admin_ajax', 'admin/ajax/<controller>(/<action>(/<id>))')
	->defaults(array(
		'directory' => 'admin/ajax',
		'controller' => NULL,
		'action' => NULL,
));