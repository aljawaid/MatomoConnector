<li <?= $this->app->checkMenuSelection('MatomoAnalyticsController', 'show', 'MatomoAnalytics') ?>>
    <?= $this->url->link('MatomoAnalytics', 'MatomoAnalyticsController', 'show', ['plugin' => 'MatomoAnalytics']) ?>
</li>
