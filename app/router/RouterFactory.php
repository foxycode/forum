<?php

namespace App\Router;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

/**
 * Router factory.
 */
class RouterFactory
{
    /**
     * @return \Nette\Application\IRouter
     */
    public static function createRouter()
    {
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
