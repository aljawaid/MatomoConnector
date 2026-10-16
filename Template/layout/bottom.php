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
$matomoPageName = trim($this->task->configModel->get('website_page_name', ''));

if ($matomoConfigured) {
    $matomoAdminUrl = rtrim($matomoAdminUrl, '/') . '/';
}
?>

<?php if ($matomoConfigured && $this->task->configModel->get('image_tracking', '') === 'enable_image_tracking'): ?>
    <?php
    $matomoTrackerPath = $this->task->configModel->get('apache_version', '') === 'cloak' ? 'rainbow' : 'matomo.php';
    $matomoImageParameters = array('idsite' => $matomoSiteId, 'rec' => '1');
    if ($matomoPageName !== '') {
        $matomoImageParameters['action_name'] = $matomoPageName;
    }
    $matomoImageUrl = $matomoAdminUrl . $matomoTrackerPath . '?' . http_build_query($matomoImageParameters, '', '&', PHP_QUERY_RFC3986);
    ?>
    <img referrerpolicy="no-referrer-when-downgrade" src="<?= $this->text->e($matomoImageUrl) ?>" class="matomo-tracking-pixel" alt="">
<?php endif ?>
