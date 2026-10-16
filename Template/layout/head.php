<?php

// The local JavaScript asset reads this validated data without requiring unsafe inline script execution.
$matomoTrackingConfiguration = $this->helper->matomoConnector->getTrackingConfiguration();
$matomoImageFallbackUrl = $this->helper->matomoConnector->getImageFallbackUrl();
$matomoJsonFlags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES;
?>

<?php if ($matomoTrackingConfiguration !== array()): ?>
    <meta
        name="matomo-connector-config"
        content="<?= $this->text->e(json_encode($matomoTrackingConfiguration, $matomoJsonFlags)) ?>"
    >
<?php endif ?>

<?php if ($matomoImageFallbackUrl !== ''): ?>
    <noscript>
        <p>
            <img
                referrerpolicy="no-referrer-when-downgrade"
                src="<?= $this->text->e($matomoImageFallbackUrl) ?>"
                class="matomo-tracking-pixel"
                alt=""
            >
        </p>
    </noscript>
<?php endif ?>
