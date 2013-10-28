<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Template Controller menu
 *
 * @package Menu
 * @author  Sergei Gladkovskiy <smgladkovskiy@gmail.com>
 */
class Controller_Menu extends Controller_Core_Menu {

	public function before()
	{
		throw new HTTP_Exception_404('Страница не найдена');
	}
}