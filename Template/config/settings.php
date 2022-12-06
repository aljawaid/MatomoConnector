<div class="matomo-page-header">
    <h2 class="">
        <img src="/plugins/MatomoConnector/Assets/matomo-logo.svg" alt="<?= t('Matomo logo') ?>">
    </h2>
</div>
<div class="page-margin">
    <form class="matomo-form panel" method="post" action="<?= $this->url->href('MatomoConnectorController', 'save', array('redirect' => 'show', 'plugin' => 'MatomoConnector')) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
        <fieldset class="matomo-admin-interface">
            <legend><?= t('Admin Interface') ?></legend>
            <div class="">
                <?= $this->form->label(t('Website Name'), 'admin_website_name', array('class="login-btn-label"')) ?>
                <?= $this->form->text('admin_website_name', $values, $errors, array('placeholder="'. t('My Matomo Admin') .'"')) ?>
                <p class="form-help"><?= t('This label is used for the login button or leave blank for "Matomo Admin Login"') ?></p>
            </div>
            <div class="">
                <?= $this->form->label(t('Matomo Admin Interface'), 'matomo_admin_url', array('class="w-600"')) ?>
                <?= $this->form->input('url', 'matomo_admin_url', $values, $errors, array('placeholder="https://analytics.mydomain.com/"', 'required'), 'w-600') ?>
                <p class="form-help w-600"><?= t('Enter the complete URL to your Matomo interface with the trailing slash') ?></p>
            </div>
            <div class="matomo-buttons">
                <?php if (!empty($this->task->configModel->get('matomo_admin_url'))): ?>
                    <a href="<?= $this->task->configModel->get('matomo_admin_url', '') ?>" class="btn" rel="noreferrer noopener" target="_blank" title="<?= t('Opens in a new window') ?>">
                        <img src="/plugins/MatomoConnector/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>"> <?php if (!empty($this->task->configModel->get('admin_website_name'))): ?> <?= $this->task->configModel->get('admin_website_name') ?><?php else: ?> <?= t('Matomo Admin Login') ?> <?php endif ?>
                    </a>
                <?php endif ?>
                <a href="https://matomo.org/guides" class="btn" rel="noreferrer noopener" target="_blank" title="<?= t('Opens in a new window') ?>">
                    <img src="/plugins/MatomoConnector/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>"> <?= t('Knowledge Base') ?>
                </a>
                <a href="https://forum.matomo.org" class="btn" rel="noreferrer noopener" target="_blank" title="<?= t('Opens in a new window') ?>">
                    <img src="/plugins/MatomoConnector/Assets/matomo-icon.svg" alt="<?= t('Matomo icon') ?>"> <?= t('Community Forum') ?>
                </a>
            </div>
        </fieldset>
        <fieldset class="matomo-tracking">
            <legend><?= t('Tracking Code') ?></legend>
            <div class="">
                <?= $this->form->label(t('Site ID'), 'site_id', array('class=""')) ?>
                <?= $this->form->number('site_id', $values, $errors, array('placeholder="5"', 'required')) ?>
                <p class="form-help"><?= t('Bad requests in the browser console are usually the result of an incorrect Site ID') ?></p>
            </div>
            <div class="">
                <?= $this->form->label(t('Track Across All Subdomains'), 'domain', array('class="w-200"')) ?>
                <?= $this->form->text('domain', $values, $errors, array('placeholder="'. t('Enter your domain') .'"')) ?>
                <p class="form-help mt-4"><?= t('Your Kanboard domain is detected as') ?> <code><?= $_SERVER['HTTP_HOST'] ?></code></p>
            </div>
            <div class="">
                <ul class="">
                    <li class="">
                        <?= $this->form->radio('doc_title', t('Prepend the Kanboard site domain to the page title when tracking'), 'prepend', isset($values['doc_title']) && $values['doc_title'] == 'prepend') ?>
                    </li>
                    <li class="">
                        <?= $this->form->radio('doc_title', t('Don\'t prepend'), 'dont_prepend', isset($values['doc_title']) && $values['doc_title'] == 'dont_prepend') ?>
                    </li>
                </ul>
            </div>
            <h3 class="apache-title"><?= t('Apache Version') ?> <span class="optional"><?= t('Optional') ?></span></h3>
            <div class="">
                <ul class="">
                    <li class="">
                        <?= $this->form->radio('apache_version', t('Use Apache version'), 'cloak', false, 'follow-instructions', isset($values['apache_version']) && $values['apache_version'] == 'cloak') ?>
                    </li>
                    <li class="">
                        <?= $this->form->radio('apache_version', t('Don\'t use Apache version'), 'dont_cloak', true, isset($values['apache_version']) && $values['apache_version'] == 'dont_cloak') ?>
                    </li>
                </ul>
                <details class="bypass">
                    <summary class="">Instructions for Using Apache Version</summary>
                    <article>
                        <h4>Bypass Adblockers Using Apache</h4>
                        <p>Cloaking or masking is a safe way to avoid detection by the filter lists used by Adblockers. It is up to you to be data-privacy compliant with your local regulations directly through Matomo.</p>
                        <p><strong><code>HTTP_MOD_REWRITE</code> Apache module must be enabled on the server where your Matomo is installed.</strong></p>
                        <p>On your Matomo server, create or update your <code>.htaccess file</code> to include the following code:</p>
                        <code class="htaccess">
                            # .htaccess<br>RewriteEngine On<br>RewriteRule ^unicorn matomo.js<br>RewriteRule ^rainbow matomo.php
                        </code>
                        <p class="source-info">Source: <a href="https://nicco.io/blog/matomo-vs-ublock-origin" class="" rel="noreferrer noopener" target="_blank" title="Opens in a new window">Niccolò Borgioli Blog</a></p>
                    </article>
                </details>
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
                    <?= $this->form->radio('image_tracking', t('Disable Image Tracking'), 'disable_image_tracking', isset($values['image_tracking']) && $values['image_tracking'] == 'disable_image_tracking') ?>
                </li>
            </ul>
            <p class="form-help matomo-form-help"><?= t('When using this option as a fallback for JavaScript tracking, the same code will be surrounded in') ?> <code><?= htmlentities('<noscript></noscript>') ?></code> <?= t('HTML tags') ?></p>
            <div class="">
                <?= $this->form->label(t('Page Name'), 'website_page_name', array('class="optional-label"')) ?>
                <?= $this->form->text('website_page_name', $values, $errors, array('placeholder="'. t('My Kanboard Instance') .'"')) ?>
                <p class="form-help"><?= t('This is the title of the action being tracked') ?></p>
            </div>
        </fieldset>
        <div class="form-actions">
            <button type="submit" class="btn btn-blue"><?= t('Save Settings') ?></button>
            <button class="back-button btn">
            <a href="<?= $this->url->href('ConfigController', 'integrations') ?>">
            <?= t('Back to Integrations') ?></a></button>
        </div>
    </form>
    <footer class="matomo-plugin-version">
        <?php
        $pluginVersion = Kanboard\Plugin\MatomoConnector\Plugin::getPluginVersion($plugin);
        $pluginName = Kanboard\Plugin\MatomoConnector\Plugin::getPluginName($plugin);
        $pluginHomepage = Kanboard\Plugin\MatomoConnector\Plugin::getPluginHomepage($plugin);
        ?>
        <a href="<?= $pluginHomepage ?>" class="" target="_blank" rel="noreferrer noopener" title="<?= t('Opens in a new window') ?>">
            <?= $pluginName ?>
        </a> <?= t('Connector') ?>
        <span>v<?= $pluginVersion ?></span>
    </footer>
</div>
