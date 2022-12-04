<?php
$website_page_name = str_replace(' ', '+', $this->task->configModel->get('website_page_name', ''))
?>

<!-- MATOMO IMAGE TRACKER -->
<?php if ($this->task->configModel->get('image_tracking', '') == 'enable_image_tracking'): ?>
    <?php if (!empty($this->task->configModel->get('matomo_admin_url')) && ($this->task->configModel->get('site_id')) && ($this->task->configModel->get('website_page_name'))): ?>
        <img
        referrerpolicy="no-referrer-when-downgrade"
        src="<?= $this->task->configModel->get('matomo_admin_url', '') ?>&#47;matomo.php&#63;idsite=<?= $this->task->configModel->get('site_id', '') ?>&amp;rec=1&amp;action_name=<?= $website_page_name ?>" style="border:0" alt="" />
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
