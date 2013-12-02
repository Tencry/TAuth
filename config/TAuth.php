<?php defined('SYSPATH') or die('No direct script access.');

return array(
	
	/**
	 * The Cookie prefix for TAuth
	 * @default 	tauth
	 */
	'cookie_prefix'		=> 'tauth',
	
	/**
	 * The directory within /cache to write to. TAuth will create it if it doesn't exist
	 * @default 	tauth
	 */
	'cache_dir'			=> 'tauth',
		
	'session_key'  => 'tauth_user',
	
	/**
	 * If no other config value is found, this will be used.
	 */
	'default' => array(
		
		/**
		 * The users model is the model that contains your user-type objects (could be customers, monkies, whatever)
		 */
		'user_model'		=> 'User',
	
		/**
		 * The column name of the 'username' field for login (probably either username or email, the way you want to identify your user).
		 * @default		'username'
		 */
		'username_field'	=> 'username',
	
		/**
		 * Salt pattern; same as Kohana Auth, define numbers between 1 and 40 to add to the string.
		 */
		'salt_pattern'		=> '1, 11, 15, 17, 33, 36, 39',
		
		/**
		 * Cache the Package Objects for quick access next time:
		 */
		'cache'				=> true,
	),
	
	
	/**
	 * A specific, other config setup. An example for development purposes.
	 */
/*	'admin'	=> array(
		'user_model'		=> 'Admin',	
		'session_key'  => 'tauth_user',
	),*/
	'member'	=> array(
		
		'user_model'		=> 'Ua_Member',
		'username_field'	=> 'email',
	),
	'user' => array(
		'user_model'		=> 'User',
	),
	'student'	=> array(
		
		'user_model'		=> 'Webclass_Student',
		'username_field'	=> 'email',
	),
	
);
