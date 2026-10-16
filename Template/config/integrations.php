<?php
$matomoAdminUrl = trim($this->task->configModel->get('matomo_admin_url', ''));
$matomoUrlParts = parse_url($matomoAdminUrl);
$matomoScheme = strtolower((string) parse_url($matomoAdminUrl, PHP_URL_SCHEME));
$matomoAdminUrl = filter_var($matomoAdminUrl, FILTER_VALIDATE_URL) !== false
    && in_array($matomoScheme, array('http', 'https'), true)
    && is_array($matomoUrlParts)
    && !empty($matomoUrlParts['host'])
    && !isset($matomoUrlParts['user'])
    && !isset($matomoUrlParts['pass'])
    && !isset($matomoUrlParts['query'])
    && !isset($matomoUrlParts['fragment'])
    ? $matomoAdminUrl
    : '';
$matomoAdminName = $this->task->configModel->get('admin_website_name', '');
?>
<div class="panel matomo-connector page-margin">
    <h4 class="">
        Matomo Analytics <?= t('Connector Installed') ?>
    </h4>
    <div class="connection-indicator">
        <?php if ($matomoAdminUrl !== ''): ?>
            <span class="indicator pp-dark-green">&#10004;</span> <?= t('Configured') ?>
        <?php else: ?>
            <span class="indicator pp-red-alt">&#10008;</span> <?= t('Not Configured') ?>
        <?php endif ?>
    </div>
    <div class="matomo-buttons">
        <a href="<?= $this->url->href('MatomoConnectorController', 'show', array('plugin' => 'MatomoConnector')) ?>" class="btn" title="">
        <img src="<?= $this->url->dir() ?>plugins/MatomoConnector/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>">
        <?= t('View Settings') ?>
    </a>
    <?php if ($matomoAdminUrl !== ''): ?>
        <a href="<?= $this->text->e($matomoAdminUrl) ?>" class="btn" rel="noreferrer noopener" target="_blank" title="<?= t('Opens in a new window') ?>">
            <img src="<?= $this->url->dir() ?>plugins/MatomoConnector/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>"> <?php if ($matomoAdminName !== ''): ?> <?= $this->text->e($matomoAdminName) ?><?php else: ?> <?= t('Matomo Admin Login') ?> <?php endif ?>
        </a>
    <?php endif ?>
    <a href="https://matomo.org/guides" class="btn" rel="noreferrer noopener" target="_blank" title="<?= t('Opens in a new window') ?>">
        <img src="<?= $this->url->dir() ?>plugins/MatomoConnector/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>"> <?= t('Knowledge Base') ?>
    </a>
    <a href="https://forum.matomo.org" class="btn" rel="noreferrer noopener" target="_blank" title="<?= t('Opens in a new window') ?>">
        <img src="<?= $this->url->dir() ?>plugins/MatomoConnector/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>"> <?= t('Community Forum') ?>
    </a>
    </div>
</div>
