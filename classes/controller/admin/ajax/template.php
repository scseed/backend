<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Template Controller template
 *
 * @author Sergei Gladkovskiy <smgladkovskiy@gmail.com>
 * @copyrignt 
 */
abstract class Controller_Admin_Ajax_Template extends Controller {

	protected $_auth_required = TRUE;
	protected $_user;

	public function before()
	{
		if($this->request->method() === HTTP_Request::POST and $this->request->post('is_ajax') === 'true')
			$this->request->requested_with('xmlhttprequest');

		parent::before();

		// Ajax Request check
		if( ! $this->request->is_ajax() AND Kohana::$environment == Kohana::PRODUCTION)
		{
			throw new HTTP_Exception_403('non-ajax request!');
		}

		// Auth requirement
		if ($this->_auth_required AND ! Auth::instance()->logged_in())
		{
			throw new HTTP_Exception_401('Auth required!');
		}
		elseif(($this->_auth_required AND Auth::instance()->logged_in()) OR Auth::instance()->logged_in())
		{
			$this->_user = Auth::instance()->get_user();
		}

		$this->response->headers('Content-Type', 'application/json; charset=utf-8');
	}
}// End Controller_template