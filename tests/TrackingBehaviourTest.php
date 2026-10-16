<?php

/**
 * Verifies privacy modes, first-party routing, CSP integration and image fallback behaviour.
 *
 * Related source files: Helper/MatomoHelper.php, Assets/js/matomo-connector.js,
 * Plugin.php, Template/layout/head.php and Template/layout/bottom.php.
 */

$matomoHelper = $container['helper']->matomoConnector;
$plugin = $loadedPlugins['MatomoConnector'];

$saveMatomoSettings = function (array $settings) use ($container) {
    $container['configModel']->save($settings);
    $container['memoryCache']->flush();
};

$baseSettings = array(
    'matomo_admin_url' => 'https://analytics.example.test/matomo/',
    'site_id' => '12',
    'matomo_transport_mode' => 'direct',
    'matomo_first_party_script_path' => '',
    'matomo_first_party_endpoint_path' => '',
    'domain' => 'example.test',
    'doc_title' => 'dont_prepend',
    'image_tracking' => 'disable_image_tracking',
    'website_page_name' => '',
    'apache_version' => 'dont_cloak',
);

$saveMatomoSettings(array_merge($baseSettings, array('matomo_tracking_mode' => 'disabled')));
matomoConnectorAssertSame(array(), $matomoHelper->getTrackingConfiguration(), 'Disabled mode must emit no tracking configuration.');
matomoConnectorAssertNotContains(
    'matomo-connector-config',
    $container['template']->render('matomoConnector:layout/head'),
    'Disabled mode must emit no tracking metadata.'
);

$saveMatomoSettings(array_merge($baseSettings, array('matomo_tracking_mode' => 'cookieless')));
$configuration = $matomoHelper->getTrackingConfiguration();
matomoConnectorAssertSame('cookieless', $configuration['mode'], 'Cookieless mode must be preserved for the browser tracker.');
matomoConnectorAssertSame('', $configuration['cookieDomain'], 'Cookieless mode must not configure a cookie domain.');
matomoConnectorAssertSame(
    'https://analytics.example.test/matomo/matomo.js',
    $configuration['scriptUrl'],
    'Direct tracking must use the normal Matomo JavaScript endpoint.'
);
matomoConnectorAssertSame(
    'https://analytics.example.test/matomo/matomo.php',
    $configuration['trackerUrl'],
    'Direct tracking must use the normal Matomo collection endpoint.'
);

$javascript = file_get_contents(__DIR__ . '/../Assets/js/matomo-connector.js');
foreach (array("['setDoNotTrack', true]", "['disableCookies']", "['disableBrowserFeatureDetection']") as $privacyCall) {
    matomoConnectorAssertContains($privacyCall, $javascript, 'Cookieless JavaScript must include ' . $privacyCall . '.');
}
matomoConnectorAssertTrue(
    strpos($javascript, "['disableCookies']") < strpos($javascript, "['trackPageView']"),
    'Cookie disabling must be queued before page-view tracking.'
);
matomoConnectorAssertTrue(
    strpos($javascript, "['setDoNotTrack', true]") < strpos($javascript, "['trackPageView']"),
    'Do Not Track handling must be queued before page-view tracking.'
);
matomoConnectorAssertContains("['setSecureCookie', true]", $javascript, 'Cookie tracking must request secure cookies on HTTPS pages.');
matomoConnectorAssertNotContains('innerHTML', $javascript, 'Tracking JavaScript must not construct HTML from configuration.');

$plugin->onStartup();
$cspRules = $container['cspRules'];
matomoConnectorAssertContains(
    'https://analytics.example.test',
    $cspRules['script-src'],
    'Direct tracking must permit only the validated Matomo origin for scripts.'
);
matomoConnectorAssertContains(
    'https://analytics.example.test',
    $cspRules['connect-src'],
    'Direct tracking must permit only the validated Matomo origin for collection requests.'
);

$saveMatomoSettings(array_merge($baseSettings, array(
    'matomo_tracking_mode' => 'cookies',
    'matomo_transport_mode' => 'first_party',
    'matomo_first_party_script_path' => '/analytics/matomo.js',
    'matomo_first_party_endpoint_path' => '/analytics/matomo.php',
)));
$configuration = $matomoHelper->getTrackingConfiguration();
matomoConnectorAssertSame('/analytics/matomo.js', $configuration['scriptUrl'], 'First-party mode must use the configured same-origin script path.');
matomoConnectorAssertSame('/analytics/matomo.php', $configuration['trackerUrl'], 'First-party mode must use the configured same-origin collection path.');
matomoConnectorAssertSame('*.example.test', $configuration['cookieDomain'], 'Cookie mode must preserve a validated cross-subdomain cookie scope.');
matomoConnectorAssertSame('', $matomoHelper->getExternalTrackingOrigin(), 'First-party mode must not broaden Kanboard CSP to an external origin.');

$saveMatomoSettings(array_merge($baseSettings, array(
    'matomo_tracking_mode' => 'cookieless',
    'matomo_transport_mode' => 'first_party',
    'matomo_first_party_script_path' => '/analytics/matomo.js',
    'matomo_first_party_endpoint_path' => '/analytics/matomo.php',
    'image_tracking' => 'enable_image_tracking_js',
    'website_page_name' => 'Kanboard & Operations',
)));
$imageUrl = $matomoHelper->getImageFallbackUrl();
matomoConnectorAssertContains('action_name=Kanboard%20%26%20Operations', $imageUrl, 'Fallback action names must use RFC 3986 encoding.');
$headOutput = $container['template']->render('matomoConnector:layout/head');
matomoConnectorAssertSame(1, substr_count($headOutput, '<noscript>'), 'Image fallback must be emitted once inside noscript markup.');
matomoConnectorAssertSame(1, substr_count($headOutput, 'matomo-tracking-pixel'), 'Image fallback must not duplicate page views.');
matomoConnectorAssertNotContains('<img', $container['template']->render('matomoConnector:layout/bottom'), 'The bottom hook must not emit a second tracking image.');

$saveMatomoSettings(array_merge($baseSettings, array(
    'matomo_tracking_mode' => '',
    'matomo_transport_mode' => '',
    'apache_version' => 'cloak',
)));
$configuration = $matomoHelper->getTrackingConfiguration();
matomoConnectorAssertSame('cookies', $configuration['mode'], 'Legacy configured installs must retain cookie tracking until explicitly changed.');
matomoConnectorAssertContains('/unicorn', $configuration['scriptUrl'], 'Legacy Apache script aliases must remain readable until settings are saved.');
matomoConnectorAssertContains('/rainbow', $configuration['trackerUrl'], 'Legacy Apache collection aliases must remain readable until settings are saved.');
