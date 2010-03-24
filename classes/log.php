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
	protected static $log_types = array(
			'_exists' => FALSE,
			'_parent_id' => NULL
		);

	// Log results array
	protected static $log_results = array(
			'_exists' => FALSE,
			'_parent_id' => NULL
		);

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

			if( ! $log_types->count())
			{
				$_log_type = Jelly::select('namespace')
					->where('name', '=', 'log_types')
					->load();

				if( ! $_log_type->loaded())
				{
					$this->_set_namespace_section('log_types');
				}
				else
				{
					Log::$log_types['_exists'] = TRUE;
					Log::$log_types['_parent_id'] = $_log_type->id;
				}
			}
			else
			{
				Log::$log_types['_exists'] = TRUE;
			}
			//Kohana::cache('log_types', $log_types, 3600);
		}

		foreach ($log_types as $log_type)
		{
			if( ! arr::get(Log::$log_types, '_parent_id'))
			{
				Log::$log_types['_parent_id'] = $log_type->parent->id;
			}
			Log::$log_types[$log_type->name] = $log_type->id;
		}

		$log_results = Kohana::cache('log_results');
		if( ! $log_results)
		{
			$log_results = Jelly::select('namespace')
				->join(DB::expr('namespaces as parent'))
				->on('namespaces.parent_id', '=', 'parent.id')
				->where('parent.name', '=', 'log_results')
				->execute();

			if( ! $log_results->count())
			{
				$_log_result = Jelly::select('namespace')
					->where('name', '=', 'log_results')
					->load();

				if( ! $_log_result->loaded())
				{
					$this->_set_namespace_section('log_results');
				}
				else
				{
					Log::$log_results['_exists'] = TRUE;
					Log::$log_results['_parent_id'] = $_log_result->id;
				}
				
			}
			else
			{
				Log::$log_results['_exists'] = TRUE;
			}
			//Kohana::cache('log_results', $log_results, 3600);
		}
		
		
		foreach ($log_results as $log_result)
		{
			if( ! arr::get(Log::$log_results, '_parent_id'))
			{
				Log::$log_results['_parent_id'] = $log_result->parent->id;
			}
			Log::$log_results[$log_result->name] = $log_result->id;
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
		if( ! arr::get(Log::$log_types, $type))
		{
			$this->_set_namespace_item($type, 'log_types');
		}

		if( ! arr::get(Log::$log_results, $result))
		{
			$this->_set_namespace_item($result, 'log_results');
		}

		Jelly::factory('log')->set(array(
					'time' => time(),
					'type' => arr::get(Log::$log_types, $type, NULL),
					'result' => arr::get(Log::$log_results, $result, NULL),
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
	 * Setting namespace section for log
	 *
	 * @param string $section
	 */
	protected function _set_namespace_section($section)
	{
		$_section = Jelly::factory('namespace')
						->set(array(
						'parent' => NULL,
						'name' => $section
					));

		$_section->save();

		Log::${$section}['_exists'] = TRUE;
		Log::${$section}['_parent_id'] = $_section->id;
	}

	/**
	 * Setting namespace item for a log section
	 *
	 * @param string $item
	 * @param string $section
	 */
	protected function _set_namespace_item($item, $section)
	{
		if( ! arr::get(Log::${$section}, '_exists'))
		{
			$this->_set_namespace_section($section);
		}

		$_item = Jelly::factory('namespace')
			->set(array(
				'parent' => Log::${$section}['_parent_id'],
				'name' => $item
			))->save();

		Log::${$section}[$item] = $_item->id;
		//Kohana::cache($section, NULL);
	}

} // End Controller log
