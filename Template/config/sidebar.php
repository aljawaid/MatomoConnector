<li <?= $this->app->checkMenuSelection('MatomoAnalyticsController', 'show', 'MatomoAnalytics') ?>>
    <?= $this->url->link(t('MatomoAnalytics'), 'MatomoAnalyticsController', 'show', ['plugin' => 'MatomoAnalytics']) ?>
</li>
