<?php defined('SYSPATH') or die('No direct script access');

class Model_Base_Role extends Model_Base_ORM {

	protected $_table_columns = array(
		'id'   => array('type' => 'int'),
		'name' => array('type' => 'string'),
		'description' => array('type' => 'string'),
	);

	// Relationships
	protected $_has_many = array(
		'users' => array('model' => 'User','through' => 'roles_users'),
	);

}
