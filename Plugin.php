<?php

namespace Kanboard\Plugin\MatomoConnector;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Security\Role;
use Kanboard\Core\Translator;

class Plugin extends Base
{
    public function initialize()
    {
        // CSS - Asset Hook
        //  - Keep filename lowercase
        $this->hook->on('template:layout:css', array('template' => 'plugins/MatomoConnector/Assets/css/matomo-connector.css'));
        $this->hook->on('template:layout:js', array('template' => 'plugins/MatomoConnector/Assets/js/matomo-connector.js'));

        // Views - Template Hook
        //  - Override name should start lowercase e.g. pluginNameExampleCamelCase
        $this->template->hook->attach('template:layout:head', 'MatomoConnector:layout/head');
        //$this->template->hook->attach('template:config:sidebar', 'MatomoConnector:config/sidebar');
        $this->template->hook->attach('template:config:integrations', 'MatomoConnector:config/integrations');

        // MATOMO SETTINGS Page - Routes
        $this->route->addRoute('/settings/matomo', 'MatomoConnectorController', 'show', 'MatomoConnector');

        // Matomo connection details control site-wide tracking and are restricted to administrators.
        // The defence-in-depth controller checks are covered by tests/SecurityBoundaryTest.php.
        $this->applicationAccessMap->add('MatomoConnectorController', '*', Role::APP_ADMIN);

        // Centralise validated tracking configuration for templates, JavaScript and CSP handling.
        $this->helper->register(
            'matomoConnector',
            '\Kanboard\Plugin\MatomoConnector\Helper\MatomoHelper'
        );
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__ . '/Locale');
        $this->configureContentSecurityPolicy();
    }

    public function getPluginName()
    {
        // Plugin Name MUST be identical to namespace for Plugin Directory to detect updated versions
        return 'MatomoConnector';
    }

    public function getPluginDescription()
    {
        return t('Connect Kanboard to Matomo with disabled, privacy-enhanced cookieless or cookie-based tracking, optional first-party routing and a no-JavaScript image fallback.');
    }

    public function getPluginAuthor()
    {
        return 'aljawaid';
    }

    public function getPluginVersion()
    {
        return '2.5.0';
    }

    public function getCompatibleVersion()
    {
        // Examples:
        // >=1.0.37
        // <1.0.37
        // <=1.0.37
        return '>=1.2.20';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/aljawaid/MatomoConnector';
    }

    /**
     * Permit the exact validated Matomo origin when direct tracking is active.
     *
     * First-party routing remains covered by Kanboard's existing self policy.
     *
     * @return void
     */
    private function configureContentSecurityPolicy()
    {
        $origin = $this->helper->matomoConnector->getExternalTrackingOrigin();

        if ($origin === '') {
            return;
        }

        $rules = $this->container['cspRules'];
        $rules['script-src'] = $this->appendContentSecurityPolicySource(
            isset($rules['script-src']) ? $rules['script-src'] : "'self'",
            $origin
        );
        $rules['connect-src'] = $this->appendContentSecurityPolicySource(
            isset($rules['connect-src']) ? $rules['connect-src'] : "'self'",
            $origin
        );
        $this->container['cspRules'] = $rules;
    }

    /**
     * Append one exact CSP source without weakening or duplicating existing rules.
     *
     * @param  string $policy
     * @param  string $source
     * @return string
     */
    private function appendContentSecurityPolicySource($policy, $source)
    {
        $sources = preg_split('/\s+/', trim($policy), -1, PREG_SPLIT_NO_EMPTY);

        if (!in_array($source, $sources, true)) {
            $sources[] = $source;
        }

        return implode(' ', $sources);
    }
}
