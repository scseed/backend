<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(
	/**
	 * Company name
	 * Is shown in title and head near logo
	 */
	'company_name' => 'SomeShit Ltd.',

	/**
	 * Media folder for admin views
	 */
	'media_folder' => MODPATH . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'media',

	'menu_group' => 'admin',

	/**
	 * Menu array example
	 */
	'menu' => array(
		/**
		 * Example of the menu structure
		 *
		 * 'somepage' => array(
		 *	'title' => 'somepage',
		 *	'controller' => 'some',
		 *	'action' => '',
		 *	'class' => '',
		 *
		 * // make 'submenu' => array() for empty submenu section (main section as anchor)
		 *	'submenu' => array(
		 *		'page' => array(
		 *			'title' => 'somepage',
		 *			'controller' => 'some',
		 *			'action' => 'page',
		 *			'class' => '',
		 *		),
		 *	),
		 * ),
		 */
	),

	'ed_copy' => TRUE,

);