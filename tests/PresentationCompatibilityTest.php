<?php

/**
 * Verifies responsive, theme-aware and accessible MatomoConnector presentation.
 *
 * Related source files: Assets/css/matomo-connector.css,
 * Template/config/settings.php and Template/config/integrations.php.
 */

$stylesheet = file_get_contents(__DIR__ . '/../Assets/css/matomo-connector.css');
$settingsTemplate = file_get_contents(__DIR__ . '/../Template/config/settings.php');
$integrationTemplate = file_get_contents(__DIR__ . '/../Template/config/integrations.php');

foreach (array(
    'var(--panel-background-color',
    'var(--input-background-color',
    'var(--color-primary',
    '@media (width <= 700px)',
    '@media (width <= 480px)',
    '@media (prefers-reduced-motion: reduce)',
) as $presentationContract) {
    matomoConnectorAssertContains(
        $presentationContract,
        $stylesheet,
        'The stylesheet must include the presentation contract: ' . $presentationContract . '.'
    );
}

matomoConnectorAssertNotContains('transition: all', $stylesheet, 'Transitions must name the properties they animate.');
matomoConnectorAssertNotContains('.w-600', $stylesheet, 'Fixed-width legacy form utilities must not remain.');
matomoConnectorAssertNotContains('.pp-red', $stylesheet, 'Global colour utility classes must not leak from the plugin.');
matomoConnectorAssertNotContains(':root', $stylesheet, 'Plugin colour variables must remain scoped to plugin containers.');
matomoConnectorAssertContains('tooltipHTML', $settingsTemplate, 'Complex privacy and routing choices must have helper popovers.');
matomoConnectorAssertNotContains('<button class="back-button', $settingsTemplate, 'The Back action must not nest a link inside a button.');

foreach (array($settingsTemplate, $integrationTemplate) as $templateSource) {
    preg_match_all('/<a\b[^>]*target="_blank"[^>]*>/i', $templateSource, $externalLinks);
    foreach ($externalLinks[0] as $externalLink) {
        matomoConnectorAssertTrue(
            preg_match('/rel="[^"]*noopener[^"]*noreferrer[^"]*"/i', $externalLink) === 1,
            'Every new-window link must include noopener and noreferrer.'
        );
    }
}

$renderedSettings = $container['template']->render('matomoConnector:config/settings', array(
    'plugin' => $loadedPlugins['MatomoConnector'],
    'values' => array(
        'matomo_tracking_mode' => 'disabled',
        'matomo_transport_mode' => 'direct',
        'doc_title' => 'dont_prepend',
        'image_tracking' => 'disable_image_tracking',
    ),
    'errors' => array(),
));
matomoConnectorAssertContains('matomo-choice-list', $renderedSettings, 'The responsive settings choices must render.');
matomoConnectorAssertContains('fa-info-circle', $renderedSettings, 'Helper popovers must render an information icon.');
