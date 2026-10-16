<?php

// phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols -- The runner must define Kanboard constants.

/**
 * Runs MatomoConnector's focused regression checks against Kanboard.
 *
 * Related test files: TestAssertions.php and every *Test.php file in this directory.
 */

$kanboardRoot = isset($argv[1]) ? realpath($argv[1]) : false;

if ($kanboardRoot === false || !is_file($kanboardRoot . '/app/common.php')) {
    fwrite(STDERR, "Usage: php tests/run.php <kanboard-root>\n");
    exit(2);
}

$pluginsRoot = getenv('KANBOARD_PLUGINS_DIR') ?: $kanboardRoot . '/plugins';

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/?controller=MatomoConnectorController&action=show&plugin=MatomoConnector';
$_SERVER['HTTP_HOST'] = 'kanboard.example.test';
$_SERVER['HTTPS'] = 'on';

define('PLUGINS_DIR', $pluginsRoot);
define('DB_FILENAME', ':memory:');

require $kanboardRoot . '/app/common.php';
require __DIR__ . '/TestAssertions.php';

$pluginName = 'MatomoConnector';
$loadedPlugins = $container['pluginLoader']->getPlugins();
$incompatiblePlugins = $container['pluginLoader']->getIncompatiblePlugins();

matomoConnectorAssertFalse(
    isset($incompatiblePlugins[$pluginName]),
    'MatomoConnector must be compatible with the tested Kanboard release.'
);
matomoConnectorAssertTrue(
    isset($loadedPlugins[$pluginName]),
    'MatomoConnector must load before its regression checks run.'
);

$testFiles = glob(__DIR__ . '/*Test.php');
sort($testFiles);

foreach ($testFiles as $testFile) {
    require $testFile;
}

echo 'MatomoConnector regression checks passed on Kanboard ' . APP_VERSION
    . ' with PHP ' . PHP_VERSION . PHP_EOL;
