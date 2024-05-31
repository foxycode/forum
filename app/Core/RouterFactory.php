<?php declare(strict_types=1);

namespace App\Core;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Nette\StaticClass;

final class RouterFactory
{
    use StaticClass;

    public static function createRouter(): RouteList
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
