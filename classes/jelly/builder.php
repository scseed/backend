<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Jelly Builder extention to make result to be cacheble
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Jelly_Builder extends Jelly_Builder_Core {

	/**
	 * Builds the builder into a native query
	 *
	 * @param   string  $type
	 * @return  void
	 */
	public function execute($db = 'default')
	{
		// Don't repeat queries
		if ( ! $this->_result)
		{
			if ($this->_meta)
			{
				// See if we can use a better $db group
				$db = $this->_meta->db();

				// Select all of the columns for the model if we haven't already
				if (empty($this->_select))
				{
					$this->select('*');
				}
			}

			// Make cache id based on sql query to avoid information crossing
			$id = md5($this->_build()->__toString());

			// Extract cached data if it exists
			$this->_result = Cache::instance()->get($id);

			// Make cache routine if result is empty
			if( ! $this->_result)
			{
				// We've now left the Jelly
				$this->_result = $this->_build()->execute($db);

				// Hand it over to Jelly_Collection if it's a select
				if ($this->_type === Database::SELECT)
				{
					$model = ($this->_meta) ? $this->_meta->model() : NULL;
					$this->_result = new Jelly_Collection($model, $this->_result);

					// If the record was limited to 1, we only return that model
					// Otherwise we return the whole result set.
					if ($this->_limit === 1)
					{
						$this->_result = $this->_result->current();
					}

					// Set cache data
					Cache::instance()->set_with_tags($id, $this->_result, NULL, array($model));
				}
			}

		}

		// Hand off the result to the Jelly_Collection
		return $this->_result;
	}
} // End Jelly_Builder