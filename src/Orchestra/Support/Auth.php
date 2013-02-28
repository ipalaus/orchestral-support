<?php namespace Orchestra\Support;

use Illuminate\Support\Facades\Auth as AuthFacade, 
	Illuminate\Support\Facades\Event;

class Auth {
	
	/**
	 * Cached user to roles relationship
	 * 
	 * @var array
	 */
	protected static $user_roles = null;
	
	/**
	 * Get the current user's roles of the application.
	 *
	 * If the user is a guest, empty array should be returned.
	 *
	 * @static
	 * @access  public
	 * @return  array
	 */
	public static function roles()
	{
		$user    = AuthFacade::user();
		$roles   = array();
		$user_id = 0;

		// only search for roles when user is logged
		is_null($user) or $user_id = $user->id;

		if ( ! isset(static::$user_roles[$user_id]) or is_null(static::$user_roles[$user_id]))
		{
			static::$user_roles[$user_id] = Event::until('orchestra.auth: roles', array(
				$user, 
				$roles,
			));
		}

		return static::$user_roles[$user_id];
	}

	/**
	 * Determine if current user has the given role
	 *
	 * @static
	 * @access public
	 * @param  string   $role
	 * @return boolean
	 */
	public static function is($role)
	{
		$roles = static::roles();

		return in_array($role, $roles);
	}
}