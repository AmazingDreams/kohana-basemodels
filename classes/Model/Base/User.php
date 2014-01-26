<?php defined('SYSPATH') or die('No direct script access');
/**
 * Model Base User
 *
 * @package    Kohana/Basemodels
 * @category   Model
 * @author     Dennis Ruhe
 * @copyright  (c)2013 Dennis Ruhe
 */
class Model_Base_User extends Model_Base_ORM {

	/**
	 * @var  Array  Has many relationships
	 */
	protected $_has_many = array(
		'roles' => array('model' => 'Role', 'through' => 'roles_users'),
	);

	/**
	 * @var  Array  Table columns
	 */
	protected $_table_columns = array(
		'id'         => array(
			'type'        => 'int',
		),
		'email'      => array(
			'type'       => 'string',
			'max_length' => 254,
			'is_unique'  => TRUE,
		),
		'username'   => array(
			'type'       => 'string',
			'max_length' => 32,
			'is_unique'  => TRUE,
			'regex'      => '~^[a-zA-Z0-9_]+$~'
		),
		'password'   => array(
			'type'       => 'string',
			'min_length' => 8,
			'max_length' => 64,
		),
		'logins'     => array(
			'type'        => 'int',
			'is_nullable' => TRUE,
		),
		'last_login' => array(
			'type'        => 'int',
			'is_nullable' => TRUE,
		),
	);

	/**
	 * Extends its parents function by adding some extra rules
	 *
	 * @return  Array  Array of rules
	 */
	public function rules()
	{
		$rules = parent::rules();

		$rules['email'][] = array('email');

		return $rules;
	}

	/**
	 * Extends its parents function by adding some extra filters
	 *
	 * @return  Array  Array of filters
	 */
	public function filters()
	{
		$filters = parent::filters();

		$filters['password'][] = array(array(Auth::instance(), 'hash'));

		return $filters;
	}

	/**
	 * Get the users profile url
	 *
	 * @return  String  Url to profile
	 */
	public function profile_url()
	{
		return 'user/profile/'.$this->id;
	}

	/**
	 * Register a new user
	 *
	 * @param   Array    Values to use
	 * @param   Array    Expected values
	 * @return  boolean  Creation succeeded or not
	 */
	public function register($values, $expected)
	{
		$validation = Validation::factory($values)
			->rule('password', 'not_empty')
			->rule('password', 'min_length', array(':value', 8))
			->rule('password_confirm', 'matches', array(':validation', ':field', 'password'));

		return $this->values($values, $expected)->create($validation);
	}

	/**
	 * Complete the login for a user by incrementing the logins and saving login timestamp
	 *
	 * @return void
	 */
	public function complete_login()
	{
		if ($this->_loaded)
		{
			// Update the number of logins
			$this->logins = new Database_Expression('logins + 1');

			// Set the last login date
			$this->last_login = time();

			// Save the user
			$this->update();
		}
	}

	/**
	 * Allows a model use both email and username as unique identifiers for login
	 *
	 * @param   string  unique value
	 * @return  string  field name
	 */
	public function unique_key($value)
	{
		return Valid::email($value) ? 'email' : 'username';
	}

} // End Model Base User
