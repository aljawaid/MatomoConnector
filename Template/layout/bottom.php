<?php
$website_page_name = str_replace(' ', '+', $this->task->configModel->get('website_page_name', ''))
?>

<?php //// IF 'ENABLE IMAGE TRACKING' //// ?>
<?php if ($this->task->configModel->get('image_tracking', '') == 'enable_image_tracking'): ?>
<!-- MATOMO IMAGE TRACKER -->
    <?php //// IF 'ADMIN URL' & 'SITE ID' & 'PAGE NAME' & 'CLOAK' //// ?>
    <?php if (!empty($this->task->configModel->get('matomo_admin_url')) && ($this->task->configModel->get('site_id')) && ($this->task->configModel->get('website_page_name'))): ?>
        <img
        referrerpolicy="no-referrer-when-downgrade"
        src="<?= $this->task->configModel->get('matomo_admin_url', '') ?>&#47;rainbow&#63;idsite=<?= $this->task->configModel->get('site_id', '') ?>&amp;rec=1&amp;action_name=<?= $website_page_name ?>" style="border:0" alt="" />
    <?php //// IF 'ADMIN URL' & 'SITE ID' & 'CLOAK' //// ?>
    <?php elseif (!empty($this->task->configModel->get('matomo_admin_url')) && ($this->task->configModel->get('site_id')) && ($this->task->configModel->get('apache_version', '') == 'cloak')): ?>
        <img
        referrerpolicy="no-referrer-when-downgrade"
        src="<?= $this->task->configModel->get('matomo_admin_url', '') ?>&#47;rainbow&#63;idsite=<?= $this->task->configModel->get('site_id', '') ?>&amp;rec=1&amp;action_name=<?= $website_page_name ?>" style="border:0" alt="" />
    <?php //// IF 'ADMIN URL' & 'SITE ID' & 'PAGE NAME' //// ?>
    <?php elseif (!empty($this->task->configModel->get('matomo_admin_url')) && ($this->task->configModel->get('site_id')) && ($this->task->configModel->get('website_page_name'))): ?>
        <img
        referrerpolicy="no-referrer-when-downgrade"
        src="<?= $this->task->configModel->get('matomo_admin_url', '') ?>&#47;matomo.php&#63;idsite=<?= $this->task->configModel->get('site_id', '') ?>&amp;rec=1&amp;action_name=<?= $website_page_name ?>" style="border:0" alt="" />
    <?php //// ELSE IF 'ADMIN URL' & 'SITE ID' //// ?>
    <?php elseif (!empty($this->task->configModel->get('matomo_admin_url')) && ($this->task->configModel->get('site_id'))): ?>
        <img
        referrerpolicy="no-referrer-when-downgrade"
        src="<?= $this->task->configModel->get('matomo_admin_url', '') ?>&#47;matomo.php&#63;idsite=<?= $this->task->configModel->get('site_id', '') ?>&amp;rec=1" style="border:0" alt="" />
    <?php endif ?>
<!-- END OF MATOMO CODE -->
<?php endif ?>

<?php if ($this->task->configModel->get('image_tracking', '') == 'disable_image_tracking'): ?>
    <!-- IMAGE TRACKING DISABLED -->
<!-- END OF MATOMO CODE -->
<?php endif ?>

<?php // question mark ? = &#63; // slash / = &#47; // must be no whitespaces in the string ?>
