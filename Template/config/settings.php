<?php

// The administrator-only settings boundary is covered by tests/SecurityBoundaryTest.php.
$matomoAdminUrl = $this->helper->matomoConnector->getAdminUrl();
$matomoAdminName = $this->task->configModel->get('admin_website_name', '');
$legacyAliasesActive = $this->task->configModel->get('apache_version', '') === 'cloak'
    && $this->task->configModel->get('matomo_transport_mode', '') === '';
?>
<div class="matomo-page-header">
    <h2>
        <img src="<?= $this->url->dir() ?>plugins/MatomoConnector/Assets/matomo-logo.svg" alt="<?= t('Matomo logo') ?>">
    </h2>
</div>
<div class="matomo-page">
    <form class="matomo-form panel" method="post" action="<?= $this->url->href('MatomoConnectorController', 'save', array('plugin' => 'MatomoConnector')) ?>" autocomplete="off">
        <?= $this->form->csrf() ?>

        <fieldset class="matomo-section">
            <legend>
                <?= t('Tracking and Privacy') ?>
                <?= $this->app->tooltipHTML($this->text->e(t('Choose whether tracking is disabled, cookieless or cookie-based. Cookieless tracking can still require consent under applicable law.'))) ?>
            </legend>
            <ul class="matomo-choice-list">
                <li>
                    <?= $this->form->radio('matomo_tracking_mode', t('Disabled'), 'disabled', isset($values['matomo_tracking_mode']) && $values['matomo_tracking_mode'] === 'disabled') ?>
                    <p class="form-help"><?= t('No Matomo requests are sent.') ?></p>
                </li>
                <li>
                    <?= $this->form->radio('matomo_tracking_mode', t('Privacy-enhanced cookieless'), 'cookieless', isset($values['matomo_tracking_mode']) && $values['matomo_tracking_mode'] === 'cookieless') ?>
                    <p class="form-help"><?= t('Disables Matomo cookies and browser feature detection. Browser Do Not Track is respected.') ?></p>
                </li>
                <li>
                    <?= $this->form->radio('matomo_tracking_mode', t('Cookie-based'), 'cookies', isset($values['matomo_tracking_mode']) && $values['matomo_tracking_mode'] === 'cookies') ?>
                    <p class="form-help"><?= t('Uses Matomo cookies. Browser Do Not Track is respected and secure cookies are requested on HTTPS pages.') ?></p>
                </li>
            </ul>
            <p class="matomo-notice">
                <strong><?= t('Privacy note:') ?></strong>
                <?= t('Cookieless tracking is not automatically exempt from consent or privacy requirements. Confirm the lawful basis and Matomo privacy settings for your deployment.') ?>
            </p>
        </fieldset>

        <fieldset class="matomo-section matomo-admin-interface">
            <legend><?= t('Matomo Connection') ?></legend>
            <div class="matomo-field">
                <?= $this->form->label(t('Website Name'), 'admin_website_name', array('class="login-btn-label"')) ?>
                <?= $this->form->text('admin_website_name', $values, $errors, array('placeholder="' . t('My Matomo Admin') . '"'), 'matomo-input') ?>
                <p class="form-help"><?= t('This label is used for the login button or leave blank for "Matomo Admin Login"') ?></p>
            </div>
            <div class="matomo-field">
                <?= $this->form->label(t('Matomo Admin Interface'), 'matomo_admin_url') ?>
                <?= $this->form->input('url', 'matomo_admin_url', $values, $errors, array('placeholder="https://analytics.example.com/"'), 'matomo-input') ?>
                <p class="form-help"><?= t('Enter the Matomo base URL. It is required for direct tracking and optional for first-party routing.') ?></p>
            </div>
            <div class="matomo-field">
                <?= $this->form->label(t('Site ID'), 'site_id') ?>
                <?= $this->form->number('site_id', $values, $errors, array('placeholder="1"', 'min="1"', 'max="2147483647"'), 'matomo-input matomo-input-small') ?>
                <p class="form-help"><?= t('Use the positive numeric Site ID assigned by Matomo.') ?></p>
            </div>
            <div class="matomo-buttons">
                <?php if ($matomoAdminUrl !== ''): ?>
                    <a href="<?= $this->text->e($matomoAdminUrl) ?>" class="btn" rel="noopener noreferrer" target="_blank" title="<?= t('Opens in a new window') ?>">
                        <img src="<?= $this->url->dir() ?>plugins/MatomoConnector/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>">
                        <?= $matomoAdminName !== '' ? $this->text->e($matomoAdminName) : t('Matomo Admin Login') ?>
                    </a>
                <?php endif ?>
                <a href="https://matomo.org/guides" class="btn" rel="noopener noreferrer" target="_blank" title="<?= t('Opens in a new window') ?>">
                    <img src="<?= $this->url->dir() ?>plugins/MatomoConnector/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>"> <?= t('Knowledge Base') ?>
                </a>
                <a href="https://forum.matomo.org" class="btn" rel="noopener noreferrer" target="_blank" title="<?= t('Opens in a new window') ?>">
                    <img src="<?= $this->url->dir() ?>plugins/MatomoConnector/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>"> <?= t('Community Forum') ?>
                </a>
            </div>
        </fieldset>

        <fieldset class="matomo-section matomo-transport">
            <legend>
                <?= t('Tracker Routing') ?>
                <?= $this->app->tooltipHTML($this->text->e(t('First-party routing uses paths on the Kanboard origin. It can improve delivery reliability but must not override consent or browser privacy choices.'))) ?>
            </legend>
            <ul class="matomo-choice-list matomo-choice-list-inline">
                <li>
                    <?= $this->form->radio('matomo_transport_mode', t('Direct to Matomo'), 'direct', isset($values['matomo_transport_mode']) && $values['matomo_transport_mode'] === 'direct') ?>
                </li>
                <li>
                    <?= $this->form->radio('matomo_transport_mode', t('First-party tracker routing'), 'first_party', isset($values['matomo_transport_mode']) && $values['matomo_transport_mode'] === 'first_party') ?>
                </li>
            </ul>

            <?php if ($legacyAliasesActive): ?>
                <p class="matomo-warning">
                    <strong><?= t('Legacy Apache aliases are active.') ?></strong>
                    <?= t('Saving these settings will replace them with the selected routing mode. Configure first-party paths before selecting first-party routing.') ?>
                </p>
            <?php endif ?>

            <div class="matomo-routing-grid">
                <div class="matomo-field">
                    <?= $this->form->label(t('First-Party Script Path'), 'matomo_first_party_script_path') ?>
                    <span class="matomo-optional"><?= t('Optional') ?></span>
                    <?= $this->form->text('matomo_first_party_script_path', $values, $errors, array('placeholder="/analytics/matomo.js"'), 'matomo-input') ?>
                    <p class="form-help"><?= t('Root-relative path served by your web server for the Matomo JavaScript file.') ?></p>
                </div>
                <div class="matomo-field">
                    <?= $this->form->label(t('First-Party Collection Path'), 'matomo_first_party_endpoint_path') ?>
                    <span class="matomo-optional"><?= t('Optional') ?></span>
                    <?= $this->form->text('matomo_first_party_endpoint_path', $values, $errors, array('placeholder="/analytics/matomo.php"'), 'matomo-input') ?>
                    <p class="form-help"><?= t('Root-relative path served by your web server for Matomo collection requests.') ?></p>
                </div>
            </div>

            <details class="matomo-guidance">
                <summary><?= t('How first-party routing works') ?></summary>
                <p><?= t('Configure your web server to reverse proxy both paths to the matching Matomo script and collection endpoints, then enter those same-origin paths above. This plugin does not change server configuration.') ?></p>
                <p><?= t('First-party routing may reduce blocking based only on a third-party hostname. It does not guarantee delivery and should not be used to defeat an explicit visitor choice.') ?></p>
                <p>
                    <a href="https://matomo.org/faq/how-to-install/what-is-the-matomo-tracker-proxy/" rel="noopener noreferrer" target="_blank" title="<?= t('Opens in a new window') ?>">
                        <?= t('Read the Matomo tracker proxy guidance') ?>
                    </a>
                </p>
            </details>
        </fieldset>

        <fieldset class="matomo-section matomo-page-options">
            <legend><?= t('Page and Fallback Options') ?></legend>
            <div class="matomo-field">
                <?= $this->form->label(t('Track Across All Subdomains'), 'domain') ?>
                <span class="matomo-optional"><?= t('Optional') ?></span>
                <?= $this->form->text('domain', $values, $errors, array('placeholder="' . t('Enter your domain') . '"'), 'matomo-input') ?>
                <p class="form-help"><?= t('Used only by cookie-based tracking. Enter a hostname without a scheme, path, port or wildcard.') ?></p>
                <p class="form-help"><?= t('Your Kanboard domain is detected as') ?> <code><?= $this->text->e(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') ?></code></p>
            </div>

            <ul class="matomo-choice-list matomo-choice-list-inline">
                <li>
                    <?= $this->form->radio('doc_title', t('Prepend the Kanboard site domain to the page title when tracking'), 'prepend', isset($values['doc_title']) && $values['doc_title'] === 'prepend') ?>
                </li>
                <li>
                    <?= $this->form->radio('doc_title', t('Keep the normal page title'), 'dont_prepend', isset($values['doc_title']) && $values['doc_title'] === 'dont_prepend') ?>
                </li>
            </ul>

            <h3><?= t('Image Tracking Fallback') ?> <span class="optional"><?= t('Optional') ?></span></h3>
            <ul class="matomo-choice-list matomo-choice-list-inline">
                <li>
                    <?= $this->form->radio('image_tracking', t('Enable when JavaScript is unavailable'), 'enable_image_tracking_js', isset($values['image_tracking']) && $values['image_tracking'] === 'enable_image_tracking_js') ?>
                </li>
                <li>
                    <?= $this->form->radio('image_tracking', t('Disable image fallback'), 'disable_image_tracking', isset($values['image_tracking']) && $values['image_tracking'] === 'disable_image_tracking') ?>
                </li>
            </ul>
            <p class="form-help"><?= t('The fallback is emitted only inside noscript markup, preventing duplicate page views when JavaScript runs.') ?></p>

            <div class="matomo-field">
                <?= $this->form->label(t('Fallback Page Name'), 'website_page_name') ?>
                <span class="matomo-optional"><?= t('Optional') ?></span>
                <?= $this->form->text('website_page_name', $values, $errors, array('placeholder="' . t('My Kanboard Instance') . '"'), 'matomo-input') ?>
                <p class="form-help"><?= t('Optional action name used only by the image fallback request.') ?></p>
            </div>
        </fieldset>

        <div class="form-actions matomo-actions">
            <button type="submit" class="btn btn-blue"><?= t('Save Settings') ?></button>
            <a class="back-button btn" href="<?= $this->url->href('ConfigController', 'integrations') ?>">
                <?= t('Back to Integrations') ?>
            </a>
        </div>
    </form>

    <footer class="matomo-plugin-version">
        <a href="<?= $this->text->e($plugin->getPluginHomepage()) ?>" target="_blank" rel="noopener noreferrer" title="<?= t('Opens in a new window') ?>">
            <?= $this->text->e($plugin->getPluginName()) ?>
        </a> <?= t('Connector') ?>
        <span>v<?= $this->text->e($plugin->getPluginVersion()) ?></span>
    </footer>
</div>
