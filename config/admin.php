<?php defined('SYSPATH') OR die('No direct access allowed.');

$site_config = Kohana::$config->load('site');

return array(
	/**
	 * Company name
	 * Is shown in title and head near logo
	 */
	'company_name'  => $site_config->company_name,
	'site_name'     => $site_config->site_name,
	'support_email' => $site_config->support_email,

	/**
	 * Media folder for admin views
	 */
	'media_folder' => MODPATH . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'media',

	'menu_group' => 'admin',
);