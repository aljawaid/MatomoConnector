<div class="matomo-page-header">
    <h2 class="">
        <img src="/plugins/MatomoAnalytics/Assets/matomo-logo.svg" alt="Matomo logo">
    </h2>
</div>
<div class="page-margin">
    <form class="matomo-form panel" method="post" action="<?= $this->url->href('MatomoAnalyticsController', 'save', array('redirect' => 'show', 'plugin' => 'MatomoAnalytics')) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
        <fieldset class="matomo-admin-interface">
            <legend><?= t('Admin Interface') ?></legend>
            <div class="">
                <?= $this->form->label(t('Website Name'), 'website_name', array('class=""')) ?>
                <?= $this->form->text('website_name', $values, $errors, array('placeholder="'. t('My Kanboard Instance') .'"')) ?>
                <p class="form-help"><?= t('This label is not used in the Matomo script') ?></p>
            </div>
            <div class="">
                <?= $this->form->label(t('Matomo Admin Interface'), 'matomo_admin_url', array('class="w-600"')) ?>
                <?= $this->form->input('url', 'matomo_admin_url', $values, $errors, array('placeholder="https://analytics.mydomain.com"', 'required'), 'w-600') ?>
                <p class="form-help w-600"><?= t('Enter the complete URL to your Matomo interface. A link to your interface will show after saving this page.') ?></p>
            </div>
            <div class="matomo-buttons">
                <?php if (!empty($this->task->configModel->get('matomo_admin_url'))): ?>
                    <a href="<?= $this->task->configModel->get('matomo_admin_url', '') ?>" class="btn" rel="noreferrer noopener" target="_blank" title="<?= t('Opens in a new window') ?>">
                        <img src="/plugins/MatomoAnalytics/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>"> <?= t('Matomo Admin Login') ?>
                    </a>
                <?php endif ?>
                <a href="https://matomo.org/guides" class="btn" rel="noreferrer noopener" target="_blank" title="<?= t('Opens in a new window') ?>">
                    <img src="/plugins/MatomoAnalytics/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>"> <?= t('Knowledge Base') ?>
                </a>
                <a href="https://forum.matomo.org" class="btn" rel="noreferrer noopener" target="_blank" title="<?= t('Opens in a new window') ?>">
                    <img src="/plugins/MatomoAnalytics/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>"> <?= t('Community Forum') ?>
                </a>
            </div>
        </fieldset>
        <fieldset class="matomo-tracking">
            <legend><?= t('Tracking Code') ?></legend>
            <div class="">
                <?= $this->form->label(t('Track Across All Subdomains'), 'domain', array('class="w-200"')) ?>
                <?= $this->form->text('domain', $values, $errors, array('placeholder="'. t('Enter your domain') .'"')) ?>
                <p class="form-help"><?= t('Your Kanboard domain should be') ?> <code><?= $_SERVER['HTTP_HOST'] ?></code></p>
            </div>
            <div class="">
                <?= $this->form->label(t('Site ID'), 'site_id', array('class=""')) ?>
                <?= $this->form->number('site_id', $values, $errors, array('placeholder="5"', 'required')) ?>
                <p class="form-help"><?= t('This label is needed to identifiy your site. Bad requests in the browser console are usually the result of an incorrect Site ID') ?></p>
            </div>
        </fieldset>

        <fieldset class="matomo-image-tracking">
            <legend><?= t('Fallback Code') ?></legend>
            <h3 class=""><?= t('Image Tracking Code') ?> <span class="optional"><?= t('Optional') ?></span></h3>
            <ul class="">
                <li class="">
                    <?= $this->form->radio('image_tracking', t('Enable Image Tracking'), 'enable_image_tracking', isset($values['image_tracking']) && $values['image_tracking'] == 'enable_image_tracking') ?>
                </li>
                <li>
                    <?= $this->form->radio('image_tracking', t('Enable Image Tracking as JavaScript Fallback'), 'enable_image_tracking_js', isset($values['image_tracking']) && $values['image_tracking'] == 'enable_image_tracking_js') ?>
                </li>
                <li>
                    <?= $this->form->radio('image_tracking', t('Disable image tracking'), 'disable_image_tracking', isset($values['image_tracking']) && $values['image_tracking'] == 'disable_image_tracking') ?>
                </li>
            </ul>
            <p class="form-help matomo-form-help"><?= t('When using this option as a fallback for JavaScript tracking, the same code is surrounded in') ?> <code><?= htmlentities('<noscript></noscript>') ?></code> <?= t('HTML tags.') ?></p>
            <div class="">
                <?= $this->form->label(t('Page Name'), 'website_page_name', array('class="optional-label"')) ?>
                <?= $this->form->text('website_page_name', $values, $errors, array('placeholder="'. t('My Kanboard Instance') .'"')) ?>
                <p class="form-help"><?= t('This is the title of the action being tracked. It is possible to use slashes / to set one or several categories for this action. For example, Help / Feedback will create the Action Feedback in the category Help.') ?></p>
            </div>
        </fieldset>
        <div class="form-actions">
            <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
        </div>
    </form>
    <footer class="matomo-plugin-version">
        <?php
        $pluginVersion = Kanboard\Plugin\MatomoAnalytics\Plugin::getPluginVersion($plugin);
        $pluginName = Kanboard\Plugin\MatomoAnalytics\Plugin::getPluginName($plugin);
        $pluginHomepage = Kanboard\Plugin\MatomoAnalytics\Plugin::getPluginHomepage($plugin);
        ?>
        <a href="<?= $pluginHomepage ?>" class="" target="_blank" rel="noreferrer noopener" title="<?= t('Opens in a new window') ?>">
            <?= $pluginName ?>
        </a> <?= t('Plugin') ?>
        <span>v<?= $pluginVersion ?></span>
    </footer>
</div>
