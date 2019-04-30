<?php

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\SimpleRouter;


/**
 * Router factory.
 */
class RouterFactory
{

	/**
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter()
	{
		Route::$defaultFlags = Route::SECURED;

		$router = new RouteList();

		$router[] = new Route('thread/new', 'Thread:new');
		$router[] = new Route('thread/<id \d+>', 'Thread:default');
		$router[] = new Route('thread/<id \d+>/reply', 'Thread:reply');

		$router[] = new Route('hledat', 'Homepage:search');

		$router[] = new Route('uzivatel/nastaveni', 'Setting:default');
		$router[] = new Route('uzivatel/prihlasit', 'Sign:in');
		$router[] = new Route('uzivatel/odhlasit', 'Sign:out');

		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');

		return $router;
	}

}
