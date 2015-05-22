<?php namespace Hugomelo1992\Control;

class Control {
	
    public $model;

	public static function retrieveByCredentials(array $credentials)
	{
		$query = self::model();

		foreach ($credentials as $key => $value)
		{
			if ( ! str_contains($key, CAMPO_PASSWORD)) $query->where($key, $value);
		}

		return $query->first();
	}

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

	public static function logout()
	{
		$app = app();
		return $app['auth']->logout();
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
