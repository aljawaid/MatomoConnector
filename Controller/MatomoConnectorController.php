<?php

namespace Kanboard\Plugin\MatomoConnector\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Core\Controller\AccessForbiddenException;

/**
 * Plugin MatomoConnector
 *
 * Class MatomoConnectorController
 * @package  Kanboard/Controller
 * @author   aljawaid
 */
class MatomoConnectorController extends BaseController
{
    const DEFAULT_SETTINGS = array(
        'matomo_tracking_mode' => 'disabled',
        'matomo_transport_mode' => 'direct',
        'doc_title' => 'dont_prepend',
        'image_tracking' => 'disable_image_tracking',
    );

    const ALLOWED_SETTINGS = array(
        'admin_website_name',
        'matomo_admin_url',
        'site_id',
        'matomo_tracking_mode',
        'matomo_transport_mode',
        'matomo_first_party_script_path',
        'matomo_first_party_endpoint_path',
        'domain',
        'doc_title',
        'image_tracking',
        'website_page_name',
    );

    /**
     * Display the Settings Page
     *
     * @access public
     */
    public function show(array $values = array(), array $errors = array())
    {
        $this->assertAdmin();

        if (empty($values)) {
            $values = $this->configModel->getAll();
        }

        if (empty($values['matomo_tracking_mode'])) {
            $values['matomo_tracking_mode'] = $this->helper->matomoConnector->getTrackingMode();
        }

        if (empty($values['matomo_transport_mode'])) {
            $values['matomo_transport_mode'] = 'direct';
        }

        if (isset($values['image_tracking']) && $values['image_tracking'] === 'enable_image_tracking') {
            $values['image_tracking'] = 'enable_image_tracking_js';
        }
        $values = array_merge(self::DEFAULT_SETTINGS, $values);

        $this->response->html($this->helper->layout->config('matomoConnector:config/settings', array(
            'plugin' => $this->pluginLoader->getPlugins()['MatomoConnector'],
            'title' => 'MatomoConnector &#10562; ' . t('Settings'),
            'values' => $values,
            'errors' => $errors,
        )));
    }

    /**
     * Save Settings
     *
     * @see     ConfigController.php   save()
     * @author  Frederic Guillot
     * @author  aljawaid
     */
    public function save()
    {
        $this->assertAdmin();

        if (!$this->request->isPost()) {
            throw new AccessForbiddenException();
        }

        $this->checkCSRFForm();

        $submitted = $this->request->getValues();
        if (array_diff(array_keys($submitted), self::ALLOWED_SETTINGS) !== array()) {
            throw new AccessForbiddenException();
        }

        $values = array_intersect_key($submitted, array_flip(self::ALLOWED_SETTINGS));
        list($valid, $values, $errors) = $this->validateSettings($values);

        if (!$valid) {
            return $this->show($values, $errors);
        }

        if ($this->configModel->save($values)) {
            $this->languageModel->loadCurrentLanguage();
            $this->flash->success(t('Settings saved successfully.'));
        } else {
            $this->flash->failure(t('Unable to save your settings.'));
        }

        $this->response->redirect($this->helper->url->to(
            'MatomoConnectorController',
            'show',
            array('plugin' => 'MatomoConnector')
        ));
    }

    /**
     * Validate and normalise the settings accepted by this plugin.
     *
     * @param  array $values
     * @return array
     */
    private function validateSettings(array $values)
    {
        $normalised = array();

        foreach (self::ALLOWED_SETTINGS as $name) {
            $default = isset(self::DEFAULT_SETTINGS[$name]) ? self::DEFAULT_SETTINGS[$name] : '';
            $value = isset($values[$name]) && is_scalar($values[$name]) ? trim((string) $values[$name]) : $default;
            $normalised[$name] = $value;
        }

        $errors = array();
        $url = $normalised['matomo_admin_url'];
        $normalisedUrl = $this->helper->matomoConnector->normalizeAdminUrl($url);
        $trackingEnabled = $normalised['matomo_tracking_mode'] !== 'disabled';
        $directTracking = $normalised['matomo_transport_mode'] === 'direct';

        if (($url !== '' || ($trackingEnabled && $directTracking)) && $normalisedUrl === '') {
            $errors['matomo_admin_url'][] = t('Enter a valid Matomo URL beginning with http:// or https://.');
        } else {
            $normalised['matomo_admin_url'] = $normalisedUrl;
        }

        if (($normalised['site_id'] !== '' || $trackingEnabled) && !$this->helper->matomoConnector->isValidSiteId($normalised['site_id'])) {
            $errors['site_id'][] = t('Enter a positive numeric Matomo Site ID.');
        }

        if ($normalised['domain'] !== '' && !$this->helper->matomoConnector->isValidDomain($normalised['domain'])) {
            $errors['domain'][] = t('Enter a valid domain name without a scheme, path, port or wildcard.');
        }

        $allowedChoices = array(
            'matomo_tracking_mode' => array('disabled', 'cookieless', 'cookies'),
            'matomo_transport_mode' => array('direct', 'first_party'),
            'doc_title' => array('prepend', 'dont_prepend'),
            'image_tracking' => array(
                'enable_image_tracking_js',
                'disable_image_tracking',
            ),
        );

        foreach ($allowedChoices as $name => $choices) {
            if (!in_array($normalised[$name], $choices, true)) {
                $errors[$name][] = t('Select one of the available options.');
            }
        }

        if ($normalised['matomo_transport_mode'] === 'first_party') {
            foreach (array('matomo_first_party_script_path', 'matomo_first_party_endpoint_path') as $name) {
                $pathRequired = $trackingEnabled || $normalised[$name] !== '';
                if ($pathRequired && !$this->helper->matomoConnector->isValidFirstPartyPath($normalised[$name])) {
                    $errors[$name][] = t('Enter a same-origin path beginning with one forward slash and without a query or fragment.');
                }
            }
        }

        foreach (array('admin_website_name', 'website_page_name') as $name) {
            if (strlen($normalised[$name]) > 120) {
                $errors[$name][] = t('Use no more than 120 characters.');
            }
        }

        return array(empty($errors), $normalised, $errors);
    }

    /**
     * Reject direct controller calls that bypass Kanboard's access map.
     *
     * @return void
     */
    private function assertAdmin()
    {
        if (!$this->userSession->isAdmin()) {
            throw new AccessForbiddenException();
        }
    }
}
