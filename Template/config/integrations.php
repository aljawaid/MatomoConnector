<div class="panel matomo-connector page-margin">
    <h4 class="">
        Matomo Analytics <?= t('Connector Installed') ?>
    </h4>
    <div class="connection-indicator">
        <?php if (!empty($this->task->configModel->get('matomo_admin_url'))): ?>
            <span class="indicator pp-dark-green">&#10004;</span> <?= t('Configured') ?>
        <?php else: ?>
            <span class="indicator pp-red-alt">&#10008;</span> <?= t('Not Configured') ?>
        <?php endif ?>
    </div>
    <div class="matomo-buttons">
        <a href="<?= $this->url->href('MatomoConnectorController', 'show', array('plugin' => 'MatomoConnector')) ?>" class="btn" title="">
        <img src="/plugins/MatomoConnector/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>">
        <?= t('View Settings') ?>
    </a>
    <?php if (!empty($this->task->configModel->get('matomo_admin_url'))): ?>
        <a href="<?= $this->task->configModel->get('matomo_admin_url', '') ?>" class="btn" rel="noreferrer noopener" target="_blank" title="<?= t('Opens in a new window') ?>">
            <img src="/plugins/MatomoConnector/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>"> <?= t('Matomo Admin Login') ?>
        </a>
    <?php endif ?>
    <a href="https://matomo.org/guides" class="btn" rel="noreferrer noopener" target="_blank" title="<?= t('Opens in a new window') ?>">
        <img src="/plugins/MatomoConnector/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>"> <?= t('Knowledge Base') ?>
    </a>
    <a href="https://forum.matomo.org" class="btn" rel="noreferrer noopener" target="_blank" title="<?= t('Opens in a new window') ?>">
        <img src="/plugins/MatomoConnector/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>"> <?= t('Community Forum') ?>
    </a>
    </div>
</div>
