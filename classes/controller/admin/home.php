<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Template Controller home
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Controller_Admin_Home extends Controller_Admin_Template {

	public $_actions = array(
		'index' => array(
			'read'
			)
		);

	public function action_index ()
	{
		$logs_meta = array();
		$this->template->page_title = $this->template->title = __('Главная');
		$this->template->content = View::factory('backend/content/home')
			->set('company_name', $this->template->company_name);
//			->bind('logs_meta', $logs_meta)
//			->bind('logs', $logs);

//		if(class_exists('Logapp'))
//		{
//			$logs = Logapp::instance()->watch(10);
//			$logs_meta = Jelly::meta('log_jelly');
//		}
	}

	/**
	 * Media files rendering
	 *
	 * @return void
	 */
	public function action_media()
	{
		// Generate and check the ETag for this file
		$this->response->check_cache(sha1($this->request->uri()), $this->request);

		// Get the file path from the request
		$file = $this->request->param('file');

		// Find the file extension
		$ext = pathinfo($file, PATHINFO_EXTENSION);

		// Remove the extension from the filename
		$file = substr($file, 0, -(strlen($ext) + 1));
		if($file = Kohana::find_file('media', $file, $ext)) {
			// Send the file content as the response
			$this->response->body(file_get_contents($file));
		}
		else
		{
			// Return a 404 status
			$this->response->status(404);
		}

		// Set the proper headers to allow caching
		$this->response->headers('Content-Type', File::mime_by_ext($ext));
		$this->response->headers('Content-Length', '' . filesize($file));
		$this->response->headers('Last-Modified', date('r', filemtime($file)));

		$this->response->send_headers();
	}

} // End Template Controller Home