# MatomoConnector

MatomoConnector connects Kanboard to Matomo with explicit privacy modes,
validated tracker routing and an optional image fallback.

> **Privacy and production limit:** Cookieless tracking does not automatically
> remove consent or privacy obligations. First-party routing can improve request
> delivery, but it must not override Do Not Track, consent choices or other
> deliberate visitor controls.

## Features

- Administrator-only configuration with server-side validation.
- Tracking disabled by default for a new installation.
- Privacy-enhanced cookieless tracking.
- Cookie-based tracking with secure cookies requested on HTTPS pages.
- Browser Do Not Track respected in both active tracking modes.
- Direct Matomo delivery or same-origin first-party tracker paths.
- Optional cross-subdomain cookie scope.
- Optional page-title prefix.
- Optional no-JavaScript image fallback without duplicate page views.
- Responsive, theme-aware settings and integration views.

## Tracking modes

| Mode | Browser requests | Matomo cookies | Browser feature detection |
| --- | --- | --- | --- |
| Disabled | None | None | None |
| Privacy-enhanced cookieless | Sent when allowed | Disabled | Disabled |
| Cookie-based | Sent when Do Not Track allows | Enabled | Matomo default |

The plugin does not provide a consent banner or make a legal determination.
Configure consent and retention controls for the relevant jurisdiction and use
case.

## Request flow

### Direct tracking

```text
Kanboard page
    |
    +-- local MatomoConnector bootstrap
            |
            +-- Matomo script URL
            +-- Matomo collection URL
```

Kanboard's Content Security Policy is extended only with the validated HTTP(S)
Matomo origin while direct tracking is active.

### First-party routing

```text
Kanboard page
    |
    +-- local MatomoConnector bootstrap
            |
            +-- /same-origin/matomo.js  ----+
            +-- /same-origin/matomo.php ---+--> web-server reverse proxy --> Matomo
```

The plugin accepts root-relative script and collection paths. It does not create
a PHP proxy or change Apache configuration. See the
[first-party tracker routing guide](docs/FIRST_PARTY_ROUTING.md).

## Configuration

1. Open **Settings → Integrations → Matomo Analytics**.
2. Choose the tracking mode.
3. Enter the Matomo Site ID.
4. For direct tracking, enter the credential-free Matomo base URL.
5. For first-party routing, configure the web server and enter the two
   same-origin paths.
6. Review the optional page, cookie-domain and image-fallback settings.
7. Save and complete the browser acceptance checks documented for the selected
   routing mode.

Existing configured installations without an explicit tracking mode continue
in cookie-based mode until an administrator saves a new choice. Legacy Apache
aliases remain readable until the settings page is saved. Legacy always-on
image tracking is treated as a no-JavaScript fallback so a page view is not
counted twice.

## Compatibility

| Component | Supported or validated target |
| --- | --- |
| Kanboard | 1.2.54 or later |
| PHP | 8.2, 8.3, 8.4 and 8.5 |
| Matomo | 5.14.x tracking API; release acceptance targets 5.14.1 |
| Database changes | None |

Automated checks validate controller boundaries, generated tracking
configuration, output encoding, translations, responsive presentation and the
Kanboard 1.2.54 plugin boot. They do not replace authenticated browser
acceptance against the intended Matomo deployment. Matomo 6 compatibility is
not claimed while Matomo 6 remains outside the stable target.

## Installation

See [INSTALL.md](INSTALL.md).

## Translation

- British English is complete.
- [The starter catalogue](Locale/translations-starter-template.php) contains
  the same ordered key set without emitting output before PHP starts.

## Security and privacy behaviour

- Only administrators can view or save settings.
- Writes require POST and a valid Kanboard CSRF token.
- Unknown configuration fields are rejected.
- Stored values are validated and encoded for their JavaScript, HTML or URL
  context.
- Direct-mode CSP sources are derived from a validated, credential-free
  HTTP(S) URL.
- External links opened in a new window use `noopener` and `noreferrer`.
- Do Not Track is respected before page-view tracking is queued.

Please report vulnerabilities privately rather than including secrets or live
analytics URLs in a public issue.

## Licence

MatomoConnector is distributed under the [MIT Licence](LICENSE).
