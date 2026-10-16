<?php

namespace Kanboard\Plugin\MatomoConnector\Helper;

use Kanboard\Core\Base;

/**
 * Provides validated Matomo settings to templates, JavaScript and CSP handling.
 *
 * Related tests: tests/TrackingBehaviourTest.php and tests/SecurityBoundaryTest.php.
 */
class MatomoHelper extends Base
{
    const MODE_DISABLED = 'disabled';
    const MODE_COOKIELESS = 'cookieless';
    const MODE_COOKIES = 'cookies';
    const TRANSPORT_DIRECT = 'direct';
    const TRANSPORT_FIRST_PARTY = 'first_party';
    const TRANSPORT_LEGACY_ALIAS = 'legacy_alias';

    /**
     * Return a credential-free, query-free Matomo administration URL.
     *
     * @return string
     */
    public function getAdminUrl()
    {
        return $this->normalizeAdminUrl($this->configModel->get('matomo_admin_url', ''));
    }

    /**
     * Normalise a Matomo URL while rejecting components that must not be exposed.
     *
     * @param  string $url
     * @return string
     */
    public function normalizeAdminUrl($url)
    {
        $url = trim((string) $url);
        $parts = parse_url($url);
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        if (
            $url === ''
            || filter_var($url, FILTER_VALIDATE_URL) === false
            || !in_array($scheme, array('http', 'https'), true)
            || !is_array($parts)
            || empty($parts['host'])
            || isset($parts['user'])
            || isset($parts['pass'])
            || isset($parts['query'])
            || isset($parts['fragment'])
        ) {
            return '';
        }

        return rtrim($url, '/') . '/';
    }

    /**
     * Return the effective tracking mode, preserving legacy configured installs.
     *
     * @return string
     */
    public function getTrackingMode()
    {
        $mode = $this->configModel->get('matomo_tracking_mode', '');

        if (in_array($mode, array(self::MODE_DISABLED, self::MODE_COOKIELESS, self::MODE_COOKIES), true)) {
            return $mode;
        }

        return $this->getAdminUrl() !== '' && $this->isValidSiteId($this->configModel->get('site_id', ''))
            ? self::MODE_COOKIES
            : self::MODE_DISABLED;
    }

    /**
     * Return the effective tracker transport, including the read-only legacy alias mode.
     *
     * @return string
     */
    public function getTransportMode()
    {
        $transport = $this->configModel->get('matomo_transport_mode', '');

        if (in_array($transport, array(self::TRANSPORT_DIRECT, self::TRANSPORT_FIRST_PARTY), true)) {
            return $transport;
        }

        return $this->configModel->get('apache_version', '') === 'cloak'
            ? self::TRANSPORT_LEGACY_ALIAS
            : self::TRANSPORT_DIRECT;
    }

    /**
     * Build the browser-safe tracking configuration or return an empty array.
     *
     * @return array
     */
    public function getTrackingConfiguration()
    {
        $mode = $this->getTrackingMode();

        if ($mode === self::MODE_DISABLED) {
            return array();
        }

        $siteId = (string) $this->configModel->get('site_id', '');
        if (!$this->isValidSiteId($siteId)) {
            return array();
        }

        $transport = $this->getTransportMode();
        $scriptUrl = '';
        $trackerUrl = '';

        if ($transport === self::TRANSPORT_FIRST_PARTY) {
            $scriptUrl = $this->getFirstPartyPath('matomo_first_party_script_path');
            $trackerUrl = $this->getFirstPartyPath('matomo_first_party_endpoint_path');
        } else {
            $adminUrl = $this->getAdminUrl();
            if ($adminUrl !== '') {
                $scriptUrl = $adminUrl . ($transport === self::TRANSPORT_LEGACY_ALIAS ? 'unicorn' : 'matomo.js');
                $trackerUrl = $adminUrl . ($transport === self::TRANSPORT_LEGACY_ALIAS ? 'rainbow' : 'matomo.php');
            }
        }

        if ($scriptUrl === '' || $trackerUrl === '') {
            return array();
        }

        $domain = trim((string) $this->configModel->get('domain', ''));

        return array(
            'mode' => $mode,
            'scriptUrl' => $scriptUrl,
            'trackerUrl' => $trackerUrl,
            'siteId' => $siteId,
            'cookieDomain' => $mode === self::MODE_COOKIES && $this->isValidDomain($domain) ? '*.' . $domain : '',
            'prependDocumentTitle' => $this->configModel->get('doc_title', '') === 'prepend',
        );
    }

    /**
     * Return the image fallback URL when the administrator enabled it.
     *
     * Legacy always-on image tracking is converted to a no-JavaScript fallback
     * so one page view cannot be counted twice.
     *
     * @return string
     */
    public function getImageFallbackUrl()
    {
        $imageMode = $this->configModel->get('image_tracking', '');
        if (!in_array($imageMode, array('enable_image_tracking', 'enable_image_tracking_js'), true)) {
            return '';
        }

        $configuration = $this->getTrackingConfiguration();
        if ($configuration === array()) {
            return '';
        }

        $parameters = array(
            'idsite' => $configuration['siteId'],
            'rec' => '1',
        );
        $pageName = trim((string) $this->configModel->get('website_page_name', ''));

        if ($pageName !== '') {
            $parameters['action_name'] = $pageName;
        }

        return $configuration['trackerUrl'] . '?' . http_build_query(
            $parameters,
            '',
            '&',
            PHP_QUERY_RFC3986
        );
    }

    /**
     * Return the exact external origin needed by Kanboard's CSP.
     *
     * @return string
     */
    public function getExternalTrackingOrigin()
    {
        $configuration = $this->getTrackingConfiguration();

        if ($configuration === array() || $this->getTransportMode() === self::TRANSPORT_FIRST_PARTY) {
            return '';
        }

        $parts = parse_url($configuration['scriptUrl']);
        if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return '';
        }

        $origin = strtolower($parts['scheme']) . '://' . $parts['host'];
        if (isset($parts['port'])) {
            $origin .= ':' . (int) $parts['port'];
        }

        return $origin;
    }

    /**
     * Validate a positive Matomo Site ID within the supported integer range.
     *
     * @param  mixed $siteId
     * @return bool
     */
    public function isValidSiteId($siteId)
    {
        $siteId = (string) $siteId;

        return $siteId !== ''
            && ctype_digit($siteId)
            && strlen($siteId) <= 10
            && (int) $siteId > 0
            && (int) $siteId <= 2147483647;
    }

    /**
     * Validate the hostname used for cross-subdomain Matomo cookies.
     *
     * @param  string $domain
     * @return bool
     */
    public function isValidDomain($domain)
    {
        $domain = trim((string) $domain);

        if ($domain === '' || strlen($domain) > 253 || strpos($domain, '..') !== false) {
            return false;
        }

        return (bool) preg_match(
            '/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)*[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/i',
            $domain
        );
    }

    /**
     * Validate a same-origin, root-relative first-party path.
     *
     * @param  string $path
     * @return bool
     */
    public function isValidFirstPartyPath($path)
    {
        $path = trim((string) $path);
        $parts = parse_url($path);

        return $path !== ''
            && strlen($path) <= 2048
            && strpos($path, '/') === 0
            && strpos($path, '//') !== 0
            && strpos($path, '\\') === false
            && !preg_match('/[\x00-\x1F\x7F]/', $path)
            && is_array($parts)
            && isset($parts['path'])
            && $parts['path'] === $path
            && !isset($parts['scheme'])
            && !isset($parts['host'])
            && !isset($parts['user'])
            && !isset($parts['pass'])
            && !isset($parts['query'])
            && !isset($parts['fragment']);
    }

    /**
     * Read one validated first-party path from configuration.
     *
     * @param  string $setting
     * @return string
     */
    private function getFirstPartyPath($setting)
    {
        $path = trim((string) $this->configModel->get($setting, ''));
        return $this->isValidFirstPartyPath($path) ? $path : '';
    }
}
