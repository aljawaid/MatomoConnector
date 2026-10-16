<?php

$matomoAdminUrl = $this->helper->matomoConnector->getAdminUrl();
$matomoAdminName = $this->task->configModel->get('admin_website_name', '');
$matomoTrackingMode = $this->helper->matomoConnector->getTrackingMode();
$matomoConfigured = $this->helper->matomoConnector->getTrackingConfiguration() !== array();
?>
<div class="panel matomo-connector">
    <div class="matomo-connector-summary">
        <div>
            <h4>Matomo Analytics <?= t('Connector Installed') ?></h4>
            <p class="connection-indicator">
                <?php if ($matomoTrackingMode === 'disabled'): ?>
                    <span class="indicator matomo-status-neutral" aria-hidden="true">&#8212;</span> <?= t('Tracking Disabled') ?>
                <?php elseif ($matomoConfigured): ?>
                    <span class="indicator matomo-status-success" aria-hidden="true">&#10004;</span> <?= t('Configured') ?>
                <?php else: ?>
                    <span class="indicator matomo-status-warning" aria-hidden="true">!</span> <?= t('Configuration Incomplete') ?>
                <?php endif ?>
            </p>
        </div>
        <div class="matomo-buttons">
            <a href="<?= $this->url->href('MatomoConnectorController', 'show', array('plugin' => 'MatomoConnector')) ?>" class="btn">
                <img src="<?= $this->url->dir() ?>plugins/MatomoConnector/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>"> <?= t('View Settings') ?>
            </a>
            <?php if ($matomoAdminUrl !== ''): ?>
                <a href="<?= $this->text->e($matomoAdminUrl) ?>" class="btn" rel="noopener noreferrer" target="_blank" title="<?= t('Opens in a new window') ?>">
                    <img src="<?= $this->url->dir() ?>plugins/MatomoConnector/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>">
                    <?= $matomoAdminName !== '' ? $this->text->e($matomoAdminName) : t('Matomo Admin Login') ?>
                </a>
            <?php endif ?>
            <a href="https://matomo.org/guides" class="btn" rel="noopener noreferrer" target="_blank" title="<?= t('Opens in a new window') ?>">
                <img src="<?= $this->url->dir() ?>plugins/MatomoConnector/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>"> <?= t('Knowledge Base') ?>
            </a>
        </div>
    </div>
</div>
