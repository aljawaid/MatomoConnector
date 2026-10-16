<?php

$matomoAdminUrl = trim($this->task->configModel->get('matomo_admin_url', ''));
$matomoUrlParts = parse_url($matomoAdminUrl);
$matomoScheme = strtolower((string) parse_url($matomoAdminUrl, PHP_URL_SCHEME));
$matomoSiteId = (string) $this->task->configModel->get('site_id', '');
$matomoConfigured = filter_var($matomoAdminUrl, FILTER_VALIDATE_URL) !== false
    && in_array($matomoScheme, array('http', 'https'), true)
    && is_array($matomoUrlParts)
    && !empty($matomoUrlParts['host'])
    && !isset($matomoUrlParts['user'])
    && !isset($matomoUrlParts['pass'])
    && !isset($matomoUrlParts['query'])
    && !isset($matomoUrlParts['fragment'])
    && ctype_digit($matomoSiteId)
    && (int) $matomoSiteId > 0;

if ($matomoConfigured) {
    $matomoAdminUrl = rtrim($matomoAdminUrl, '/') . '/';
}

$matomoUsesApacheAliases = $this->task->configModel->get('apache_version', '') === 'cloak';
$matomoTrackerPath = $matomoUsesApacheAliases ? 'rainbow' : 'matomo.php';
$matomoScriptPath = $matomoUsesApacheAliases ? 'unicorn' : 'matomo.js';
$matomoPageName = trim($this->task->configModel->get('website_page_name', ''));
$matomoJsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES;
?>

<?php if ($matomoConfigured): ?>
    <!-- Matomo tracking code. -->
    <script>
        var _paq = window._paq = window._paq || [];
        <?php if ($this->task->configModel->get('doc_title', '') === 'prepend'): ?>
        _paq.push(['setDocumentTitle', document.domain + '/' + document.title]);
        <?php endif ?>
        <?php if ($this->task->configModel->get('domain', '') !== ''): ?>
        _paq.push(['setCookieDomain', <?= json_encode('*.' . $this->task->configModel->get('domain', ''), $matomoJsonFlags) ?>]);
        <?php endif ?>
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function () {
            var u = <?= json_encode($matomoAdminUrl, $matomoJsonFlags) ?>;
            _paq.push(['setTrackerUrl', u + <?= json_encode($matomoTrackerPath, $matomoJsonFlags) ?>]);
            _paq.push(['setSiteId', <?= json_encode($matomoSiteId, $matomoJsonFlags) ?>]);
            var d = document;
            var g = d.createElement('script');
            var s = d.getElementsByTagName('script')[0];
            g.async = true;
            g.src = u + <?= json_encode($matomoScriptPath, $matomoJsonFlags) ?>;
            s.parentNode.insertBefore(g, s);
        })();
    </script>
    <!-- End Matomo tracking code. -->
<?php endif ?>

<?php if ($matomoConfigured && $this->task->configModel->get('image_tracking', '') === 'enable_image_tracking_js'): ?>
    <?php
    $matomoImageParameters = array('idsite' => $matomoSiteId, 'rec' => '1');
    if ($matomoPageName !== '') {
        $matomoImageParameters['action_name'] = $matomoPageName;
    }
    $matomoImageUrl = $matomoAdminUrl . $matomoTrackerPath . '?' . http_build_query($matomoImageParameters, '', '&', PHP_QUERY_RFC3986);
    ?>
    <noscript>
        <p><img referrerpolicy="no-referrer-when-downgrade" src="<?= $this->text->e($matomoImageUrl) ?>" class="matomo-tracking-pixel" alt=""></p>
    </noscript>
<?php endif ?>
