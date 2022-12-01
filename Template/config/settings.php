<div class="matomo-page-header">
    <h2>
        <?= t('Matomo Analytics') ?>
    </h2>
</div>
<div class="page-margin">
    <h3>MatomoAnalytics</h3>
    <fieldset>
        <legend>Admin Interface</legend>
        <div class="">
            <?= $this->form->label(t('Matomo Admin Interface'), 'matomo_admin_url', array('class="w-600"')) ?>
            <p class="form-help w-600"><?= t('Enter the complete website URL to your Matomo Interface') ?></p>
        </div>
    </fieldset>
    <fieldset>
        <legend>Tracking Code</legend>
        <div class="">
            <?= $this->form->label(t('Domain'), 'domain', array('class="w-200"')) ?>
            <p class="form-help w-200"><?= t('This label is not used on the icon') ?></p>
        </div>
        <div class="">
            <?= $this->form->label(t('Site ID'), 'site_id', array('class="w-200"')) ?>
            <?= $this->form->text('site_id', $values, $errors, array('placeholder="5"')) ?>
            <p class="form-help w-200"><?= t('This label is not used on the icon') ?></p>
        </div>
        <div class="">
            <?= $this->form->label(t('Website Name'), 'website_name', array('class="w-200"')) ?>
            <?= $this->form->text('website_name', $values, $errors, array('placeholder="My Kanboard Instance"')) ?>
            <p class="form-help w-200"><?= t('This label is not used on the icon') ?></p>
        </div>
    </fieldset>
    <fieldset>
        <legend>JavaScript Disabled Fallback Code</legend>
    </fieldset>
    <fieldset>
        <legend>Image Tracking Code</legend>
        <div class="">
            <?= $this->form->label(t('Page Name'), 'website_page_name', array('class="w-200"')) ?>
            <?= $this->form->text('website_page_name', $values, $errors, array('placeholder="My Kanboard Instance"')) ?>
            <p class="form-help w-200"><?= t('This label is not used on the icon') ?></p>
        </div>
    </fieldset>
</div>
