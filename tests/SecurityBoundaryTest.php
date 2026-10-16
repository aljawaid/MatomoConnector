<?php

/**
 * Verifies the administrator, request-integrity and output-encoding boundaries.
 *
 * Related source files: Plugin.php, Controller/MatomoConnectorController.php,
 * Template/config/settings.php, Template/config/integrations.php,
 * Template/layout/head.php and Template/layout/bottom.php.
 */

use Kanboard\Controller\BaseController;
use Kanboard\Core\Security\Role;
use Kanboard\Plugin\MatomoConnector\Controller\MatomoConnectorController;

foreach (array('show', 'save') as $action) {
    foreach (array(Role::APP_USER, Role::APP_MANAGER) as $role) {
        matomoConnectorAssertFalse(
            $container['applicationAuthorization']->isAllowed('MatomoConnectorController', $action, $role),
            'Non-administrators must not access MatomoConnectorController::' . $action . '.'
        );
    }
    matomoConnectorAssertTrue(
        $container['applicationAuthorization']->isAllowed('MatomoConnectorController', $action, Role::APP_ADMIN),
        'Administrators must retain access to MatomoConnectorController::' . $action . '.'
    );
}

matomoConnectorAssertSame(
    BaseController::class,
    get_parent_class(MatomoConnectorController::class),
    'The plugin controller must not inherit unrelated ConfigController actions.'
);

foreach (array('api', 'webhook', 'downloadDb', 'saveUploadedDb', 'optimizeDb', 'token') as $inheritedAction) {
    matomoConnectorAssertFalse(
        method_exists(MatomoConnectorController::class, $inheritedAction),
        'The plugin controller must not expose ConfigController::' . $inheritedAction . '.'
    );
}

$controllerSource = file_get_contents(__DIR__ . '/../Controller/MatomoConnectorController.php');
matomoConnectorAssertContains('isPost()', $controllerSource, 'Settings writes must require POST requests.');
matomoConnectorAssertContains('checkCSRFForm()', $controllerSource, 'Settings writes must validate CSRF tokens.');
matomoConnectorAssertContains('ALLOWED_SETTINGS', $controllerSource, 'Settings writes must use an explicit field allowlist.');
matomoConnectorAssertContains('array_diff(array_keys($submitted)', $controllerSource, 'Unexpected global setting names must be rejected.');
matomoConnectorAssertNotContains("getStringParam('redirect'", $controllerSource, 'Settings redirects must not be user controlled.');

$controller = new MatomoConnectorController($container);
$validateSettings = new ReflectionMethod($controller, 'validateSettings');

list($valid, $normalised, $errors) = $validateSettings->invoke($controller, array(
    'admin_website_name' => 'Matomo Admin',
    'matomo_admin_url' => 'https://analytics.example.test/matomo',
    'site_id' => '7',
    'matomo_tracking_mode' => 'cookies',
    'matomo_transport_mode' => 'direct',
    'matomo_first_party_script_path' => '',
    'matomo_first_party_endpoint_path' => '',
    'domain' => 'example.test',
    'doc_title' => 'dont_prepend',
    'image_tracking' => 'disable_image_tracking',
    'website_page_name' => 'Kanboard',
));

matomoConnectorAssertTrue($valid, 'A legitimate administrator configuration must remain valid.');
matomoConnectorAssertSame(
    'https://analytics.example.test/matomo/',
    $normalised['matomo_admin_url'],
    'The administrator URL must be normalised to one trailing slash.'
);
matomoConnectorAssertSame(array(), $errors, 'A legitimate administrator configuration must have no errors.');

list($valid, $normalised, $errors) = $validateSettings->invoke($controller, array(
    'matomo_tracking_mode' => 'disabled',
    'matomo_transport_mode' => 'first_party',
    'doc_title' => 'dont_prepend',
    'image_tracking' => 'disable_image_tracking',
));
matomoConnectorAssertTrue($valid, 'Disabled tracking must allow first-party paths to be cleared for rollback.');
matomoConnectorAssertSame(array(), $errors, 'A disabled rollback configuration must have no path errors.');

list($valid, $normalised, $errors) = $validateSettings->invoke($controller, array(
    'matomo_tracking_mode' => 'cookieless',
    'matomo_transport_mode' => 'first_party',
    'doc_title' => 'dont_prepend',
    'image_tracking' => 'disable_image_tracking',
));
matomoConnectorAssertFalse($valid, 'Enabled first-party tracking must require both proxy paths.');
foreach (array('matomo_first_party_script_path', 'matomo_first_party_endpoint_path') as $field) {
    matomoConnectorAssertTrue(isset($errors[$field]), 'Missing ' . $field . ' must produce a field error.');
}

list($valid, $normalised, $errors) = $validateSettings->invoke($controller, array(
    'matomo_admin_url' => 'javascript:alert(1)',
    'site_id' => '-1',
    'matomo_tracking_mode' => 'unexpected',
    'matomo_transport_mode' => 'unexpected',
    'domain' => 'https://example.test/path',
    'doc_title' => 'unexpected',
    'image_tracking' => 'unexpected',
));

matomoConnectorAssertFalse($valid, 'Unsafe or unsupported settings must be rejected.');
foreach (array('matomo_admin_url', 'site_id', 'matomo_tracking_mode', 'matomo_transport_mode', 'domain', 'doc_title', 'image_tracking') as $field) {
    matomoConnectorAssertTrue(isset($errors[$field]), 'Invalid ' . $field . ' input must produce a field error.');
}

list($valid, $normalised, $errors) = $validateSettings->invoke($controller, array(
    'matomo_admin_url' => 'https://analytics.example.test/',
    'site_id' => str_repeat('9', 10000),
    'matomo_tracking_mode' => 'cookies',
    'matomo_transport_mode' => 'direct',
));
matomoConnectorAssertFalse($valid, 'Oversized Matomo Site IDs must be rejected.');
matomoConnectorAssertTrue(isset($errors['site_id']), 'Oversized Matomo Site IDs must produce a field error.');

list($valid, $normalised, $errors) = $validateSettings->invoke($controller, array(
    'matomo_admin_url' => 'https://user:password@analytics.example.test/?token=secret#fragment',
    'site_id' => '7',
    'matomo_tracking_mode' => 'cookies',
    'matomo_transport_mode' => 'direct',
    'doc_title' => 'dont_prepend',
    'image_tracking' => 'disable_image_tracking',
));
matomoConnectorAssertFalse($valid, 'Credential-bearing or token-bearing Matomo URLs must be rejected.');
matomoConnectorAssertTrue(isset($errors['matomo_admin_url']), 'Unsafe Matomo URL components must produce a field error.');

$unsafeMarkup = '</script><script>alert("matomo")</script>';
$unsafeLabel = '<img src=x onerror=alert("matomo")>';
$container['configModel']->save(array(
    'admin_website_name' => $unsafeLabel,
    'matomo_admin_url' => 'https://analytics.example.test/',
    'site_id' => '7',
    'matomo_tracking_mode' => 'cookies',
    'matomo_transport_mode' => 'direct',
    'domain' => 'example.test' . $unsafeMarkup,
    'doc_title' => 'dont_prepend',
    'image_tracking' => 'enable_image_tracking',
    'website_page_name' => 'Kanboard ' . $unsafeMarkup,
));
$container['memoryCache']->flush();

$headOutput = $container['template']->render('matomoConnector:layout/head');
$integrationOutput = $container['template']->render('matomoConnector:config/integrations');

matomoConnectorAssertNotContains($unsafeMarkup, $headOutput, 'Stored settings must not break out of the tracking script.');
matomoConnectorAssertContains('matomo-connector-config', $headOutput, 'Validated tracking data must be emitted for the local JavaScript asset.');
matomoConnectorAssertNotContains('<script>', $headOutput, 'Tracking configuration must not require unsafe inline JavaScript.');
matomoConnectorAssertContains('%3C%2Fscript%3E', $headOutput, 'Image tracking values must use RFC 3986 query encoding.');
matomoConnectorAssertNotContains($unsafeLabel, $integrationOutput, 'Stored labels must not render active markup.');
matomoConnectorAssertContains('&lt;img src=x onerror=alert(&quot;matomo&quot;)&gt;', $integrationOutput, 'Stored labels must be HTML escaped.');

$container['configModel']->save(array(
    'matomo_admin_url' => 'https://user:password@analytics.example.test/?token=secret',
));
$container['memoryCache']->flush();
$headOutput = $container['template']->render('matomoConnector:layout/head');
$integrationOutput = $container['template']->render('matomoConnector:config/integrations');
matomoConnectorAssertNotContains('password', $headOutput, 'Unsafe legacy URLs must not be emitted into tracking code.');
matomoConnectorAssertNotContains('password', $integrationOutput, 'Unsafe legacy URLs must not be emitted into links.');
