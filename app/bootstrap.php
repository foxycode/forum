<?php

require __DIR__ . '/../vendor/autoload.php';

setlocale(LC_ALL, "Czech");

$configurator = new Nette\Configurator;

if (getenv('NETTE_DEVEL') == 1)
{
    $configurator->setDebugMode(TRUE);
}
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
    ->addDirectory(__DIR__)
    ->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

return $container;
