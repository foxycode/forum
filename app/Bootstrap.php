<?php declare(strict_types=1);

namespace App;

use Nette\Configurator;

final class Bootstrap
{
    public static function boot(): Configurator
    {
        $configurator = new Configurator;

        if (getenv('NETTE_DEVEL') === '1') {
            $configurator->setDebugMode(TRUE);
        }

        $configurator->enableTracy(__DIR__ . '/../log');
        setlocale(LC_ALL, 'cs_CZ.utf8');
        $configurator->setTimeZone('Europe/Prague');
        $configurator->setTempDirectory(__DIR__ . '/../temp');

        $configurator->addConfig(__DIR__ . '/Config/common.neon');
        $configurator->addConfig(__DIR__ . '/Config/local.neon');

        return $configurator;
    }
}
