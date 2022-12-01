<?php

namespace Kanboard\Plugin\MatomoAnalytics\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Core\Plugin\Directory;

/**
 * Class MatomoAnalytics
 * 
 * @author aljawaid
 * 
 */

class MatomoAnalyticsController extends \Kanboard\Controller\PluginController
{
    /**
     * Display the Settings Page
     *
     * @access public
     */

    public function show()
    {
        $this->response->html($this->helper->layout->plugin('matomoAnalytics:config/settings', array(
            'title' => t('Matomo Analytics').' &#10562; '.t('Settings'),
        )));
    }
}
