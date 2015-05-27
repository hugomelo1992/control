<?php namespace Hugomelo1992\Control;

class Control {


    /**
     * Find a user by one of the fields given as $identity.
     * If one of the fields in the $identity array matches the user
     * will be retrieved.
     *
     * @param array $identity An array of attributes and values to search for
     *
     * @return ConfideUser User object
     */
    public static function getUserByIdentity($identity)
    {
        $user = self::model();

        $user = $user->where(function($user) use ($identity) {
            foreach ($identity as $attribute => $value) {
                if ( ! str_contains($attribute, CAMPO_PASSWORD)) $user = $user->where($attribute, '=', $value);
            }
        });

        $user = $user->get()->first();

        return $user;
    }

	/**
	 * Log a user into the application.
	 *
	 * @param  \Illuminate\Auth\UserInterface  $user
	 * @param  bool  $remember
	 * @return void
	 */
	public static function login($identity, $remember=false)
	{
		$app = app();

		$user = self::getUserByIdentity($identity);

        if ($user) {
			$password = CAMPO_PASSWORD;

	        if(!$app['hash']->check($identity[$password], $user->$password)) return false;

	        $app['auth']->login($user, $remember);

	        return true;
	    }

	    return false;
	}

	/**
	 * Log the user out of the application.
	 *
	 * @return void
	 */
	public static function logout()
	{
		$app = app();

		$user = $app['auth']->user();

		$session = $app['auth']->getSession();

		$session->forget($app['auth']->getName());

		$events = $app['auth']->getDispatcher();

		if (isset($events))
		{
			$events->fire('auth.logout', array($user));
		}

		$app['auth']->setUser = null;

		return;

		// $this->loggedOut = true;
	}

    /**
     * Returns the model set in auth config
     *
     * @return mixed Instantiated object of the 'auth.model' class
     */
    public static function model()
    {
		$app = app();
        
        return $app[$app['config']->get('auth.model')];

        throw new \Exception("Wrong model specified in config/auth.php", 639);
    }

}
