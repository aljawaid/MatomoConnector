<li<?= $this->app->checkMenuSelection('MatomoAnalytics', 'show', 'MatomoAnalytics') ?>>
    <?= $this->url->link(t('Matomo Analytics'), 'MatomoAnalyticsController', 'show', ['plugin' => 'MatomoAnalytics']) ?>
</li>
