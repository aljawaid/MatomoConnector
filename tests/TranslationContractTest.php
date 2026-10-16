<?php

/**
 * Verifies that production and starter translation catalogues remain aligned.
 *
 * Related source files: Locale/en_GB/translations.php and
 * Locale/translations-starter-template.php.
 */

$english = require __DIR__ . '/../Locale/en_GB/translations.php';
$starter = require __DIR__ . '/../Locale/translations-starter-template.php';

matomoConnectorAssertSame(
    array_keys($english),
    array_keys($starter),
    'The starter catalogue must contain the complete production key set in the same order.'
);

foreach ($english as $key => $translation) {
    matomoConnectorAssertTrue($translation !== '', 'The British English translation must not be empty for: ' . $key);
}

$starterSource = file_get_contents(__DIR__ . '/../Locale/translations-starter-template.php');
matomoConnectorAssertTrue(
    strpos($starterSource, '<?php') === 0,
    'The starter catalogue must not emit text before its PHP opening tag.'
);
