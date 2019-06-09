<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

setlocale(LC_ALL, 'cs_CZ.utf8');

$configurator = new Nette\Configurator;

if (getenv('NETTE_DEVEL') === '1') {
    $configurator->setDebugMode(TRUE);
}
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->addConfig(__DIR__ . '/Config/config.neon');
$configurator->addConfig(__DIR__ . '/Config/config.local.neon');

return $configurator->createContainer();
