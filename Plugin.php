<?php

namespace Kanboard\Plugin\MatomoConnector;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;
use Kanboard\Helper;

class Plugin extends Base
{
    public function initialize()
    {
        // CSS - Asset Hook
        //  - Keep filename lowercase
        $this->hook->on('template:layout:css', array('template' => 'plugins/MatomoConnector/Assets/css/matomo-connector.css'));

        // Views - Template Hook
        //  - Override name should start lowercase e.g. pluginNameExampleCamelCase
        $this->template->hook->attach('template:layout:head', 'MatomoConnector:layout/head');
        $this->template->hook->attach('template:layout:bottom', 'MatomoConnector:layout/bottom');
        //$this->template->hook->attach('template:config:sidebar', 'MatomoConnector:config/sidebar');
        $this->template->hook->attach('template:config:integrations', 'MatomoConnector:config/integrations');

        // MATOMO SETTINGS Page - Routes
        $this->route->addRoute('/settings/matomo', 'MatomoConnectorController', 'show', 'MatomoConnector');
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
    }

    public function getPluginName()
    {
        // Plugin Name MUST be identical to namespace for Plugin Directory to detect updated versions
        return 'MatomoConnector';
    }

    public function getPluginDescription()
    {
        return t('Connect your Kanboard instance to Matomo Analytics to track visitors to your site. Set the standard tracking code or use the optional JavaScript fallback code with further options for image tracking.');
    }

    public function getPluginAuthor()
    {
        return 'aljawaid';
    }

    public function getPluginVersion()
    {
        return '2.1.0';
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
}
