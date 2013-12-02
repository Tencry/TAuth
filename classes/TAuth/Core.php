<?php defined('SYSPATH') or die('No direct script access.');

class TAuth_Core extends Auth {


	/*************************** Properties ***************************/
	
	protected static $instances = array();
	protected static $config;
	protected $name;


	/************************ General methods *************************/

	public static function instance($name = 'default')
	{
		if ( !array_key_exists($name, self::$instances) ) {
			self::$instances[$name] = new TAuth($name);
		}
		
		return self::$instances[$name];
	}
	
	public function __construct($name)
	{
		$this->name = $name;
		
		if( version_compare(Kohana::VERSION, '3.2.0', '>=') ) {
			self::$config = Kohana::$config->load('TAuth');
			$this->_config = Kohana::$config->load('auth');
		} else {
			self::$config = Kohana::config('TAuth');
			$this->_config = Kohana::config('auth');
		}

		$this->_session = Session::instance($this->_config['session_type']);
	}


	/************************ Support methods *************************/

	protected function read_config($key, $default=false)
	{
		if ( isset(self::$config->$key) ) {
			return self::$config->$key;
		}
		
		if ( ($this->name != 'default') 
			AND isset(self::$config->{$this->name}) 
			AND isset(self::$config->{$this->name}[$key]) ) {
			return self::$config->{$this->name}[$key];
		}
		
		if ( isset(self::$config->default) 
			AND isset(self::$config->default[$key]) ) {
			return self::$config->default[$key];
		}
		
		return $default;
	}


	/*********************** Login/out methods ************************/

	protected function _login($username, $password, $remember=FALSE)
	{
		$user = $this->load_user($username);

		if (is_string($password))
		{
			// Create a hashed password
			$password = $this->hash($password);
		}
		
		if ( $user && ($user->password == $password)) {
			
			$this->complete_login($user);
			
			return TRUE;
		}
		
		return FALSE;
	}
	
	public function logout($destroy = FALSE, $logout_all = FALSE)
	{
		if ($destroy === TRUE)
		{
			// Destroy the session completely
			$this->_session->destroy();
		}
		else
		{
			// Remove the user from the session
			$this->_session->delete($this->_config['session_key']);
			$this->_session->delete(self::$config['session_key']);

			// Regenerate session_id
			$this->_session->regenerate();
		}

		// Double check
		return ! $this->logged_in();
	}
	
	public function logged_in($role = NULL)
	{
		return (bool) $this->get_user();
	}
	
	public function load_user($username)
	{
		$model = $this->read_config('user_model');
			
		$user = ORM::factory($model)->where(
			$this->read_config('username_field'), '=', $username
		)->find();
		
		return $user;
	}

	/**
	 * Compare password with original (hashed). Works for current (logged in) user
	 *
	 * @param   string  $password
	 * @return  boolean
	 */
	public function check_password($password)
	{
		$user = $this->get_user();

		if ( ! $user)
			return FALSE;

		return ($this->hash($password) === $user->password);
	}
	
	public function password($user)
	{
		if ( ! is_object($user) ) {
			$user = $this->load_user($user);
		}
		
		return $user->password;
	}
	

	protected function complete_login($user)
	{
		// Regenerate session_id
		$this->_session->regenerate();

		// Store username in session
	//	$this->_session->set(self::$config['session_key'], $user);

		if ($this->read_config('user_model') == 'User')
		{
			$this->_session->set($this->_config['session_key'], $user);
		}
		else
		{
			$this->_session->set(self::$config['session_key'], $user);
		}
		
		
		return TRUE;
	}
	
	public function get_user($default = NULL)
	{
		if ($user = $this->_session->get($this->_config['session_key'], $default))
		{
			return $user;
		}
		
		return $this->_session->get(self::$config['session_key'], $default);
	}
}
