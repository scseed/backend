<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Template Controller home
 *
 * @author avis <smgladkovskiy@gmail.com>
 */
class Controller_Admin_Home extends Controller_Admin_Template {

	public function action_index ()
	{
		exit(Kohana::debug(
			$this->_resource
		));
	}

} // End Template Controller home
