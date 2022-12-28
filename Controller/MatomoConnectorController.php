<?php

namespace Kanboard\Plugin\MatomoConnector\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Core\Plugin\Directory;
use Kanboard\Core\Plugin\Installer;

/**
 * Class MatomoConnector
 * 
 * @author aljawaid
 * 
 */

class MatomoConnectorController extends \Kanboard\Controller\ConfigController
{
    /**
     * Display the Settings Page
     *
     * @access public
     */

    public function show()
    {
        $this->response->html($this->helper->layout->config('matomoConnector:config/settings', array(
            'plugins' => $this->pluginLoader->getPlugins(),
            'title' => 'MatomoConnector &#10562; '.t('Settings'),
        )));
    }

    /**
     * Save settings
     *
     */
    public function save()
    {
        $values =  $this->request->getValues();
        $redirect = $this->request->getStringParam('redirect', 'matomo');

        if ($this->configModel->save($values)) {
            $this->languageModel->loadCurrentLanguage();
            $this->flash->success(t('Settings saved successfully.'));
        } else {
            $this->flash->failure(t('Unable to save your settings.'));
        }

        $this->response->redirect($this->helper->url->to('MatomoConnectorController', $redirect, ['plugin' => 'MatomoConnector']));
    }
}
