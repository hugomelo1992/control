<?php namespace Hugomelo1992\Control;

class Control {
	
    public $model;


	/**
	 * Retrieve a user by the given credentials.
	 *
	 * @param  array  $credentials
	 * @return \Illuminate\Auth\UserInterface|null
	 */
	public static function retrieveByCredentials(array $credentials)
	{
		$query = self::model();

		foreach ($credentials as $key => $value)
		{
			if ( ! str_contains($key, CAMPO_PASSWORD)) $query->where($key, $value);
		}

		return $query->first();
	}

	/**
	 * Log a user into the application.
	 *
	 * @param  \Illuminate\Auth\UserInterface  $user
	 * @param  bool  $remember
	 * @return void
	 */
	public static function login($credentials, $remember=false)
	{
		$app = app();

		$user = self::retrieveByCredentials($credentials);

        if ($user) {
			$password = CAMPO_PASSWORD;

	        if(!$app['hash']->check($credentials[$password], $user->$password)) return false;

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
		// return $app['auth']->logout();

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
