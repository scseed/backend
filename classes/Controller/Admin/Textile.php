<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Controller textile
 *
 * @author Sergei Gladkovskiy <smgladkovskiy@gmail.com>
 * @copyrignt
 */
class Controller_Admin_Textile extends Controller {

	public function action_preview()
	{
		$textile = new Textile();
		$this->response->body($textile->TextileThis(HTML::chars($_POST['data'])));
	}

} // End Controller_Admin_Textile