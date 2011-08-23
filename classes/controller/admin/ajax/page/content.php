<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Template Controller content
 *
 * @author Sergei Gladkovskiy <smgladkovskiy@gmail.com>
 * @copyrignt
 */
class Controller_Admin_Ajax_Page_Content extends Controller_Admin_Ajax_Template {

	public function action_delete()
	{
		$id        = (int) $this->request->param('id');
		$jobisdone = FALSE;
		$errors    = NULL;

//		try
//		{
//			Jelly::query('page_content', $id)->delete();
//			$jobisdone = TRUE;
//		}
//		catch(Jelly_Validation_Exception $e)
//		{
//			$errors = $e->errors('validate');
//		}
		$jobisdone = TRUE;

		$status = array(
			'status' => $jobisdone,
			'error' => $errors,
		);

		$this->response->body(json_encode($status));
	}

} // End Controller_content