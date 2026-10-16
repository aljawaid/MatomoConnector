<?php

/**
 * Provides assertion helpers for MatomoConnector's focused regression tests.
 *
 * Related test runner: tests/run.php.
 */

function matomoConnectorAssertTrue($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function matomoConnectorAssertFalse($condition, $message)
{
    matomoConnectorAssertTrue(!$condition, $message);
}

function matomoConnectorAssertSame($expected, $actual, $message)
{
    if ($expected !== $actual) {
        throw new RuntimeException(
            $message . ' Expected ' . var_export($expected, true) . ', got ' . var_export($actual, true) . '.'
        );
    }
}

function matomoConnectorAssertContains($needle, $haystack, $message)
{
    matomoConnectorAssertTrue(strpos($haystack, $needle) !== false, $message);
}

function matomoConnectorAssertNotContains($needle, $haystack, $message)
{
    matomoConnectorAssertTrue(strpos($haystack, $needle) === false, $message);
}
