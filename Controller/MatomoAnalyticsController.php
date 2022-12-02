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

class MatomoAnalyticsController extends \Kanboard\Controller\ConfigController
{
    /**
     * Display the Settings Page
     *
     * @access public
     */

    public function show()
    {
        $this->response->html($this->helper->layout->config('matomoAnalytics:config/settings', array(
            'title' => 'MatomoAnalytics &#10562; '.t('Settings'),
        )));
    }
}
