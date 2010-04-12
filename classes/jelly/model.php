<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Class Model
 *
 * @author avis <smgladkovskiy@gmail.com>
 * @copyright (c) 2010 EnerDesign <http://enerdesign.ru>
 */
class Jelly_Model extends Jelly_Mode_Core {

	/**
	 * Creates or updates the current record.
	 *
	 * If $key is passed, the record will be assumed to exist
	 * and an update will be executed, even if the model isn't loaded().
	 *
	 * @param   mixed  $key
	 * @return  $this
	 **/
	public function save($key = NULL)
	{
		// Determine whether or not we're updating
		$data = ($this->_loaded OR $key) ? $this->_changed : $this->_changed + $this->_original;

		if ( ! is_null($key))
		{
			// There are no rules for this since it is a meta alias and not an actual field
			// but adding it allows us to check for uniqueness when lazy saving
			$data[':unique_key'] = $key;
		}

		// Set the key to our id if it isn't set
		if ($this->_loaded)
		{
			$key = $this->_original[$this->_meta->primary_key()];
		}

		// Run validation
		$data = $this->validate($data);

		// These will be processed later
		$values = $relations = array();

		// Iterate through all fields in original incase any unchanged fields
		// have save() behavior like timestamp updating...
		foreach ($this->_changed + $this->_original as $column => $value)
		{
			// Filters may have been applied to data, so we should use that value
			if (array_key_exists($column, $data))
			{
				$value = $data[$column];
			}

			$field = $this->_meta->fields($column);

			// Only save in_db values
			if ($field->in_db)
			{
				// See if field wants to alter the value on save()
				$value = $field->save($this, $value, (bool) $key);

				if ($value !== $this->_original[$column])
				{
					// Value has changed (or has been changed by field:save())
					$values[$field->name] = $value;
				}
				else
				{
					// Insert defaults
					if ( ! $key AND ! $this->changed($field->name) AND ! $field->primary)
					{
						$values[$field->name] = $field->default;
					}
				}
			}
			elseif ($this->changed($column) AND $field instanceof Jelly_Field_Behavior_Saveable)
			{
				$relations[$column] = $value;
			}
		}

		// If we have a key, we're updating
		if ($key)
		{
			// Do we even have to update anything in the row?
			if ($values)
			{
				Jelly::update($this)
					 ->where(':unique_key', '=', $key)
					 ->set($values)
					 ->execute();
			}
		}
		else
		{
			list($id) = Jelly::insert($this)
							 ->columns(array_keys($values))
							 ->values(array_values($values))
							 ->execute();

			// Gotta make sure to set this
			$values[$this->_meta->primary_key()] = $id;
		}

		// Set the changed data back as original
		$this->_original = array_merge($this->_original, $this->_changed, $values);

		// We're good!
		$this->_loaded = $this->_saved = TRUE;
		$this->_retrieved = $this->_changed = array();

		// Save the relations
		foreach($relations as $column => $value)
		{
			$this->_meta->fields($column)->save($this, $value, (bool) $key);
		}

		// Clear cache by model tag
		Cache::instance()->delete_tag($this->_meta->model());

		return $this;
	}
	
} // End Jelly_Model
