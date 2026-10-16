/*
 * Loads validated Matomo tracking configuration emitted by Template/layout/head.php.
 * Tracking behaviour is covered by tests/TrackingBehaviourTest.php.
 */

(function () {
    'use strict';

    var configElement = document.querySelector('meta[name="matomo-connector-config"]');

    if (!configElement || window.__matomoConnectorLoaded) {
        return;
    }

    var config;

    try {
        config = JSON.parse(configElement.getAttribute('content'));
    } catch (error) {
        return;
    }

    if (!config || !config.scriptUrl || !config.trackerUrl || !config.siteId) {
        return;
    }

    window.__matomoConnectorLoaded = true;
    var queue = window._paq = window._paq || [];

    queue.push(['setTrackerUrl', config.trackerUrl]);
    queue.push(['setSiteId', config.siteId]);
    queue.push(['setDoNotTrack', true]);

    if (config.mode === 'cookieless') {
        queue.push(['disableCookies']);
        queue.push(['disableBrowserFeatureDetection']);
    } else if (config.mode === 'cookies') {
        if (window.location.protocol === 'https:') {
            queue.push(['setSecureCookie', true]);
        }

        if (config.cookieDomain) {
            queue.push(['setCookieDomain', config.cookieDomain]);
        }
    } else {
        return;
    }

    if (config.prependDocumentTitle) {
        queue.push(['setDocumentTitle', document.domain + '/' + document.title]);
    }

    queue.push(['trackPageView']);
    queue.push(['enableLinkTracking']);

    var script = document.createElement('script');
    var firstScript = document.getElementsByTagName('script')[0];
    script.async = true;
    script.src = config.scriptUrl;
    firstScript.parentNode.insertBefore(script, firstScript);
})();
