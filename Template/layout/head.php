<?php
$website_page_name_js = str_replace(' ', '+', $this->task->configModel->get('website_page_name', ''))
?>

<?php //// IF 'TRACKING CODE' //// ?>
<?php if (!empty($this->task->configModel->get('matomo_admin_url')) && ($this->task->configModel->get('site_id'))): ?>
    <!-- MATOMO CODE -->
    <script>
        var _paq = window._paq = window._paq || [];
        /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
        <?php if ($this->task->configModel->get('doc_title', '') == 'prepend'): ?>
        _paq.push(["setDocumentTitle", document.domain + "/" + document.title]);
        <?php endif ?>
        <?php if (!empty($this->task->configModel->get('domain'))): ?>
        _paq.push(["setCookieDomain", "*.<?= $this->task->configModel->get('domain', '') ?>"]);
        <?php endif ?>
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function() {
            var u="<?= $this->task->configModel->get('matomo_admin_url', '') ?>";
            <?php if ($this->task->configModel->get('apache_version', '') == 'cloak'): ?>
            _paq.push(['setTrackerUrl', u+'rainbow']);
            <?php else: ?>
            _paq.push(['setTrackerUrl', u+'matomo.php']);
            <?php endif ?>
            _paq.push(['setSiteId', '<?= $this->task->configModel->get('site_id', '') ?>']);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            <?php if ($this->task->configModel->get('apache_version', '') == 'cloak'): ?>
            g.async=true; g.src=u+'unicorn'; s.parentNode.insertBefore(g,s);
            <?php else: ?>
            g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
            <?php endif ?>
        })();
    </script>
    <!-- END OF MATOMO CODE -->
<?php endif ?>

<?php //// IF 'ENABLE IMAGE TRACKING AS JS FALLBACK' //// ?>
<?php if ($this->task->configModel->get('image_tracking', '') == 'enable_image_tracking_js'): ?>
    <!-- MATOMO IMAGE TRACKER -->
    <?php //// ELSE IF 'ADMIN URL' & 'SITE ID' & 'CLOAK' //// ?>
    <?php if (!empty($this->task->configModel->get('matomo_admin_url')) && ($this->task->configModel->get('site_id')) && ($this->task->configModel->get('website_page_name')) && ($this->task->configModel->get('apache_version', '') == 'cloak')): ?>
        <noscript><p><img
        referrerpolicy="no-referrer-when-downgrade"
        src="<?= $this->task->configModel->get('matomo_admin_url', '') ?>&#47;rainbow&#63;idsite=<?= $this->task->configModel->get('site_id', '') ?>&amp;rec=1&amp;action_name=<?= $website_page_name_js ?>" style="border:0" alt="" /></p></noscript>
    <?php //// IF 'ADMIN URL' & 'SITE ID' & 'PAGE NAME' & 'CLOAK' //// ?>
    <?php elseif (!empty($this->task->configModel->get('matomo_admin_url')) && ($this->task->configModel->get('site_id')) && ($this->task->configModel->get('apache_version', '') == 'cloak')): ?>
        <noscript><p><img
        referrerpolicy="no-referrer-when-downgrade"
        src="<?= $this->task->configModel->get('matomo_admin_url', '') ?>&#47;rainbow&#63;idsite=<?= $this->task->configModel->get('site_id', '') ?>&amp;rec=1&amp;action_name=<?= $website_page_name_js ?>" style="border:0" alt="" /></p></noscript>
    <?php //// ELSE IF 'ADMIN URL' & 'SITE ID' & 'PAGE NAME' //// ?>
    <?php elseif (!empty($this->task->configModel->get('matomo_admin_url')) && ($this->task->configModel->get('site_id')) && ($this->task->configModel->get('website_page_name'))): ?>
        <noscript><p><img
        referrerpolicy="no-referrer-when-downgrade"
        src="<?= $this->task->configModel->get('matomo_admin_url', '') ?>&#47;matomo.php&#63;idsite=<?= $this->task->configModel->get('site_id', '') ?>&amp;rec=1&amp;action_name=<?= $website_page_name_js ?>" style="border:0" alt="" /></p></noscript>
    <?php //// ELSE IF 'ADMIN URL' & 'SITE ID' //// ?>
    <?php elseif (!empty($this->task->configModel->get('matomo_admin_url')) && ($this->task->configModel->get('site_id'))): ?>
        <noscript><p><img
        referrerpolicy="no-referrer-when-downgrade"
        src="<?= $this->task->configModel->get('matomo_admin_url', '') ?>&#47;matomo.php&#63;idsite=<?= $this->task->configModel->get('site_id', '') ?>&amp;rec=1" style="border:0" alt="" /></p></noscript>
    <?php endif ?>
<!-- END OF MATOMO CODE -->
<?php endif ?>

<?php if ($this->task->configModel->get('image_tracking', '') == 'disable_image_tracking'): ?>
<!-- MATOMO IMAGE TRACKER -->
    <!-- IMAGE TRACKING DISABLED -->
<!-- END OF MATOMO CODE -->
<?php endif ?>

<?php // question mark ? = &#63; // slash / = &#47; // must be no whitespaces in the string ?>
