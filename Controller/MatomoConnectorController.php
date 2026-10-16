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
        'doc_title' => 'dont_prepend',
        'apache_version' => 'dont_cloak',
        'image_tracking' => 'disable_image_tracking',
    );

    const ALLOWED_SETTINGS = array(
        'admin_website_name',
        'matomo_admin_url',
        'site_id',
        'domain',
        'doc_title',
        'apache_version',
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
        $values = array_merge(self::DEFAULT_SETTINGS, $values);

        $this->response->html($this->helper->layout->config('matomoConnector:config/settings', array(
            'plugins' => $this->pluginLoader->getPlugins(),
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
        $urlParts = parse_url($url);
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        if (
            $url === ''
            || filter_var($url, FILTER_VALIDATE_URL) === false
            || !in_array($scheme, array('http', 'https'), true)
            || !is_array($urlParts)
            || empty($urlParts['host'])
            || isset($urlParts['user'])
            || isset($urlParts['pass'])
            || isset($urlParts['query'])
            || isset($urlParts['fragment'])
        ) {
            $errors['matomo_admin_url'][] = t('Enter a valid Matomo URL beginning with http:// or https://.');
        } else {
            $normalised['matomo_admin_url'] = rtrim($url, '/') . '/';
        }

        if (
            $normalised['site_id'] === ''
            || !ctype_digit($normalised['site_id'])
            || strlen($normalised['site_id']) > 10
            || (int) $normalised['site_id'] < 1
            || (int) $normalised['site_id'] > 2147483647
        ) {
            $errors['site_id'][] = t('Enter a positive numeric Matomo Site ID.');
        }

        if ($normalised['domain'] !== '' && !$this->isValidDomain($normalised['domain'])) {
            $errors['domain'][] = t('Enter a valid domain name without a scheme, path, port or wildcard.');
        }

        $allowedChoices = array(
            'doc_title' => array('prepend', 'dont_prepend'),
            'apache_version' => array('cloak', 'dont_cloak'),
            'image_tracking' => array(
                'enable_image_tracking',
                'enable_image_tracking_js',
                'disable_image_tracking',
            ),
        );

        foreach ($allowedChoices as $name => $choices) {
            if (!in_array($normalised[$name], $choices, true)) {
                $errors[$name][] = t('Select one of the available options.');
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
     * Accept a hostname only; Matomo adds the wildcard when configuring subdomain cookies.
     *
     * @param  string $domain
     * @return bool
     */
    private function isValidDomain($domain)
    {
        if (strlen($domain) > 253 || strpos($domain, '..') !== false) {
            return false;
        }

        return (bool) preg_match(
            '/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)*[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/i',
            $domain
        );
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
