# MatomoConnector installation

> **Before enabling tracking:** confirm the organisation's consent, privacy,
> retention and access requirements. A cookieless configuration may still
> process personal data and may still require consent.

## Requirements

| Component | Requirement |
| --- | --- |
| Kanboard | 1.2.54 or later |
| PHP | 8.2 to 8.5 |
| Matomo | A reachable Matomo 5.14.x deployment and valid Site ID |
| Direct tracking | Browser access to the Matomo script and collection origin |
| First-party routing | Two HTTPS routes configured on the Kanboard web server |

## Install from the Kanboard Plugin Directory

1. Open **Settings → Plugins → Plugin Directory**.
2. Find **MatomoConnector**.
3. Install the plugin.
4. Open **Settings → Integrations → Matomo Analytics**.

## Install from a release archive

1. Download the intended release archive.
1. Verify its provenance and integrity using the release publication.
1. Extract it so the final directory is exactly:

```text
plugins/
└── MatomoConnector/
    ├── Plugin.php
    ├── Assets/
    ├── Controller/
    ├── Helper/
    ├── Locale/
    └── Template/
```

1. Ensure the web-server account can read the plugin files.
1. Reload Kanboard and confirm the plugin appears under
   **Settings → Integrations**.

The historical archives in `Releases/` are retained as release history. They
are not rewritten by this update; use the newly published release archive for
version 3.0.0.

## Install with Git

From Kanboard's `plugins` directory:

```console
git clone https://github.com/aljawaid/MatomoConnector.git MatomoConnector
```

For a production installation, deploy a reviewed release tag rather than
tracking a moving branch.

## Upgrade notes for 3.0.0

- Existing configured installations continue with cookie-based tracking until
  a tracking mode is explicitly saved.
- New installations start with tracking disabled.
- Legacy `unicorn` and `rainbow` Apache aliases remain readable until settings
  are saved.
- Saving selects either normal direct endpoints or the new first-party paths.
- Legacy always-on image tracking becomes a no-JavaScript fallback to prevent
  duplicate page views.
- No database migration is required; settings continue to use Kanboard's
  settings table.

## Acceptance checklist

- Sign in as an administrator and save the intended mode.
- Confirm an ordinary user cannot open or save the settings page.
- Inspect the browser Network panel for one script request and one collection
  request per page view.
- With cookieless mode, confirm no Matomo cookies are created.
- With Do Not Track enabled, confirm Matomo honours the browser preference.
- With JavaScript disabled and image fallback enabled, confirm one image request.
- Test a representative task page and project page without exposing sensitive
  titles beyond the intended Matomo configuration.
- Confirm the layout at desktop and mobile widths in the active light and dark themes.
- For first-party routing, complete the checks in the
  [first-party routing guide](docs/FIRST_PARTY_ROUTING.md).

Read the [changelog](changelog.md) before publishing a release.
