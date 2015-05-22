<?php namespace Hugo\AccessControl;

class AccessControl {

	public static function retrieveByCredentials(array $credentials)
	{

		foreach ($credentials as $key => $value)
		{
			if ( ! str_contains($key, CAMPO_PASSWORD)) $query = User::where($key, $value);
		}

		return $query->first();
	}

	public static function login($credentials, $remember=false)
	{
		$app = app();

		$user = self::retrieveByCredentials($credentials);

		$password = CAMPO_PASSWORD;

        if(!$app['hash']->check($credentials[$password], $user->$password)) return false;

        $app['auth']->login($user, $remember);

        return true;
	}

	public static function logout()
	{
		$app = app();
		return $app['auth']->logout();
	}

}
