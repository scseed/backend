<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Controller Log
 *
 * @author avis <smgladkovskiy@gmail.com>
 */
class Log {

	// Log instance
	protected static $instance;

	/**
	 * Creates a singleton of a Log Class.
	 *
	 * @return  Log
	 */
	public static function instance()
	{
		if ( ! isset(Log::$instance))
		{
			// Create a new log instance
			Log::$instance = new Log();
		}

		return Log::$instance;
	}

	// Log types array
	protected static $_log_types = array();

	// Log results array
	protected static $_log_results = array();

	/**
	 * Constructor
	 */
	public function  __construct()
	{
		$log_types = Kohana::cache('log_types');
		if( ! $log_types)
		{
			$log_types = Jelly::select('namespace')
				->join(DB::expr('namespaces as parent'))
				->on('namespaces.parent_id', '=', 'parent.id')
				->where('parent.name', '=', 'log_types')
				->execute();
			Kohana::cache('log_types', $log_types, 3600);
		}
		
		foreach ($log_types as $log_type)
		{
			Log::$_log_types[$log_type->name] = $log_type->id;
		}

		$log_results = Kohana::cache('log_results');
		if( ! $log_results)
		{
			$log_results = Jelly::select('namespace')
				->join(DB::expr('namespaces as parent'))
				->on('namespaces.parent_id', '=', 'parent.id')
				->where('parent.name', '=', 'action_returns')
				->execute();
			Kohana::cache('log_results', $log_results, 3600);
		}
		
		
		foreach ($log_results as $log_result)
		{
			Log::$_log_results[$log_result->name] = $log_result->id;
		}
	}

	/**
	 * Writing log issue
	 *
	 * @param string $type
	 * @param string $result
	 * @param integer $user
	 * @param string $description
	 */
	public function write ($type, $result, $user = NULL, $description = NULL)
	{
		if( ! array_key_exists($type, Log::$_log_types))
		{
			$this->_set_type($type);
		}
		if( ! array_key_exists($result, Log::$_log_results))
		{
			$this->_set_result($result);
		}

		Jelly::factory('log')->set(array(
					'time' => time(),
					'type' => arr::get(Log::$_log_types, $type, NULL),
					'result' => arr::get(Log::$_log_results, $result, NULL),
					'user' => $user,
					'description' => __($description)
				))->save();
		
	}

	/**
	 * Watching last Log issues
	 *
	 * @param integer $limit
	 * @return object Logs
	 */
	public function watch($limit = 10)
	{
		$logs = Jelly::select('log')
			->limit((int) $limit)
			->order_by('id', 'DESC');
		$logs = $logs->execute();
		return $logs;
	}

	/**
	 * Setting unknown log type
	 *
	 * @param string $type
	 */
	protected function _set_type($type)
	{
		$log_type = Jelly::factory('namespace')
				->set(array(
					'parent' => 1,
					'name' => $type
				))->save();
		
		Log::$_log_types[$log_type->name] = $log_type->id;

		Kohana::cache('log_types', NULL);
	}

	/**
	 * Setting unknown log result
	 *
	 * @param string $result
	 */
	protected function _set_result($result)
	{
		$log_result = Jelly::factory('namespace')
			->set(array(
				'parent' => 2,
				'name' => $result
			))->save();

		Log::$_log_results[$log_result->name] = $log_result->id;

		Kohana::cache('log_results', NULL);
	}

} // End Controller log
