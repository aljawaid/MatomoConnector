# First-party tracker routing

> **Critical limits:** First-party routing changes the delivery path, not the
> privacy purpose of tracking. It does not remove consent duties, anonymise
> page data or justify bypassing an explicit browser or visitor choice.
> MatomoConnector always asks Matomo to respect Do Not Track.

First-party routing lets the browser request the Matomo JavaScript and
collection endpoint from paths on the Kanboard origin. Apache then forwards
those requests to Matomo. This can improve reliability when a filter blocks
only the third-party analytics hostname, but filters may still block
recognisable paths or payloads.

## Responsibilities

| Layer | Responsibility |
| --- | --- |
| MatomoConnector | Validates and emits root-relative paths. |
| Kanboard Apache | Proxies both paths to Matomo. |
| Matomo | Applies privacy and trusted-proxy settings. |
| Administrator | Verifies governance, behaviour and rollback. |

MatomoConnector does not edit Apache, store proxy credentials or relay tracker
traffic through PHP.

## Request layout

```text
Browser on https://kanboard.example.com
    |
    +-- GET  /matomo-proxy/matomo.js
    +-- POST /matomo-proxy/matomo.php
              |
              +-- Apache reverse proxy
                        |
                        +-- https://analytics.example.com/matomo.js
                        +-- https://analytics.example.com/matomo.php
```

## Apache outline

The exact virtual-host layout, certificate paths and upstream restrictions are
deployment-specific. Review them with the server configuration before applying
changes.

Required Apache modules normally include `proxy`, `proxy_http`, `ssl` and
`headers`. Inside the HTTPS Kanboard virtual host, a minimal routing outline is:

```apache
SSLProxyEngine On
ProxyAddHeaders On

ProxyPass "/matomo-proxy/matomo.js" \
    "https://analytics.example.com/matomo.js" retry=0 timeout=15
ProxyPassReverse "/matomo-proxy/matomo.js" \
    "https://analytics.example.com/matomo.js"

ProxyPass "/matomo-proxy/matomo.php" \
    "https://analytics.example.com/matomo.php" retry=0 timeout=15
ProxyPassReverse "/matomo-proxy/matomo.php" \
    "https://analytics.example.com/matomo.php"
```

Then configure:

| MatomoConnector field | Example |
| --- | --- |
| First-Party Script Path | `/matomo-proxy/matomo.js` |
| First-Party Collection Path | `/matomo-proxy/matomo.php` |

Use HTTPS for both the public Kanboard origin and the Matomo upstream. Do not
disable upstream certificate verification to make the proxy work.

## Trusted proxy and logs

- Apache normally adds forwarding headers when `ProxyAddHeaders On` is active.
- Configure Matomo to trust only the exact proxy source that is expected to
  forward tracker requests.
- Do not broadly trust public address ranges or arbitrary forwarding headers.
- Decide whether Apache access logs for the proxy paths are necessary, and keep
  their retention proportionate.
- Never place Matomo credentials, auth tokens or secret query parameters in the
  configured paths.

## Production acceptance

1. Verify Apache configuration syntax before reloading it.
2. Request the script path directly and confirm JavaScript is returned with the
   expected content type.
3. Submit a collection request and confirm Matomo returns a successful response.
4. Open Kanboard in an authenticated browser and confirm exactly one script
   request and one collection request per page view.
5. In cookieless mode, confirm the page creates no Matomo cookies.
6. Enable browser Do Not Track and confirm Matomo stops tracking.
7. Disable JavaScript with the image fallback enabled and confirm one
   collection request.
8. Confirm the Kanboard Content Security Policy remains self-only for tracker
   delivery in first-party mode.
9. Review Matomo for the expected site, URL and title data without exposing more
   Kanboard information than intended.
10. Test the rollback below.

## Rollback

1. Set MatomoConnector tracking to **Disabled** and save.
2. Confirm the browser sends no Matomo requests.
3. Remove the two Apache proxy routes.
4. Verify Apache configuration syntax and reload Apache.
5. Confirm the former proxy paths return `404` and normal Kanboard pages remain
   healthy.

If direct tracking is the intended fallback, configure and test the validated
Matomo base URL before switching away from first-party routing.
