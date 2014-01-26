<?php defined('SYSPATH') or die('No direct script access');
/**
 * Model Base ORM
 *
 * @package    Kohana/Basemodels
 * @category   Model
 * @author     Dennis Ruhe
 * @copyright  (c)2013 Dennis Ruhe
 */
class Model_Base_ORM extends ORM {

	/**
	 * Constructs the rules
	 *
	 * @return  Array  Array of rules
	 */
	public function rules()
	{
		$array = array();

		foreach($this->_table_columns as $colname => $properties)
		{
			$array[$colname] = $this->_build_rule($colname, $properties);
		}

		return $array;
	}

	/**
	 * Constructs the filters
	 *
	 * @return  Array  Array of filters
	 */
	public function filters()
	{
		$array = array();

		foreach($this->_table_columns as $colname => $properties)
		{
			$array[$colname] = $this->_build_filter($colname, $properties);
		}

		return $array;
	}

	/**
	 * Constructs the labels
	 *
	 * @return  Array  Array of labels
	 */
	public function labels()
	{
		$array = array();

		foreach($this->_table_columns as $colname => $properties)
		{
			$array[$colname] = Arr::get($properties, 'label', $colname);
		}

		return $array;
	}

	/**
	 * Build filters for a property based on its properties
	 *
	 * @param   String  Name of the column
	 * @param   Array   Properties of the column
	 * @return  Array   Array of filters
	 */
	private function _build_filter($colname, array $properties)
	{
		$type        = Arr::get($properties, 'type');

		$filters = array();

		if($type === 'string')
		{
			$filters[] = array('trim');
		}

		return $filters;
	}

	/**
	 * Build rules for a property based on its properties
	 *
	 * @param   String  Name of the column
	 * @param   Array   Properties of the column
	 * @return  Array   Array of rules
	 */
	private function _build_rule($colname, array $properties)
	{
		$type        = Arr::get($properties, 'type');
		$max_length  = Arr::get($properties, 'max_length', ($type == 'string') ? 255 : 11);
		$min_length  = Arr::get($properties, 'min_length', 1);
		$is_nullable = Arr::get($properties, 'is_nullable', FALSE);
		$is_unique   = Arr::get($properties, 'is_unique', FALSE);
		$regex       = Arr::get($properties, 'regex', FALSE);

		$rules = array(
			array('max_length', array(':value', $max_length))
		);

		if( ! $is_nullable AND $colname !== $this->_primary_key)
		{
			$rules[] = array('not_empty');
			$rules[] = array('min_length', array(':value', $min_length));
		}

		if($is_unique)
		{
			$rules[] = array(array($this, 'unique'), array($colname, ':value'));
		}

		if($regex)
		{
			$rules[] = array('regex', array(':value', $regex));
		}

		return $rules;
	}
} // End Model Base ORM
