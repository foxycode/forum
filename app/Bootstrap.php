<?php declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;

final class Bootstrap
{
    public static function boot(): Configurator
    {
        $configurator = new Configurator();
        $appDir = dirname(__DIR__);

        if (getenv('NETTE_DEVEL') === '1') {
            $configurator->setDebugMode(TRUE);
        }

        $configurator->enableTracy($appDir . '/log');
        setlocale(LC_ALL, 'cs_CZ.utf8');
        $configurator->setTimeZone('Europe/Prague');
        $configurator->setTempDirectory($appDir . '/temp');

        $configurator->addConfig($appDir . '/config/common.neon');
        $configurator->addConfig($appDir . '/config/local.neon');

        return $configurator;
    }
}
