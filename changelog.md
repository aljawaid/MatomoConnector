# Changelog

## 3.0.0

- SECURITY FIX: Restricted every MatomoConnector controller action to
  administrators and removed inherited core configuration actions.
- SECURITY FIX: Added POST, CSRF, exact field allowlisting, server-side
  validation and context-safe output encoding.
- NEW: Added disabled, privacy-enhanced cookieless and cookie-based tracking modes.
- NEW: Added CSP-compatible local JavaScript bootstrap and validated
  first-party tracker routing.
- FIX: Respected browser Do Not Track and requested secure Matomo cookies on
  HTTPS pages.
- FIX: Converted legacy always-on image tracking to a no-JavaScript fallback
  to prevent duplicate page views.
- FIX: Added responsive, theme-aware settings with explicit smooth transitions
  and reduced-motion support.
- FIX: Completed and aligned the British English and starter translation
  catalogues.
- FIX: Raised the supported baseline to Kanboard 1.2.54 and added PHP 8.2 to
  8.5 regression coverage.
- DOCS: Added privacy limits, first-party routing, upgrade guidance and
  production acceptance checks.
- No database changes.
- Historical release archives are unchanged.

## 2.5

- FIX: Changelog.
- FIX: CSS code syntax.
- FIX: Code syntax.
- NEW: Added footer badges and README navigation.
- Created INSTALL.md.

## 2.4

- FIX: Normalised Unix line endings and spaces.

## 2.3

- FIX: Removed an empty ruleset.
- FIX: Corrected relative image URLs.
- FIX: Added the standard `appearance` property.

## 2.2

- FIX: Removed CSS global selectors.

## 2.1

- FIX: Corrected non-static method and undefined-variable errors.
- FIX: Corrected CSS variables, transitions and menu movement.
- Resolves historical issues #1 and #2.

## 2.0

- NEW: Added legacy Apache aliases for tracker endpoints.
- Added Matomo privacy guidance.

## 1.0

- Initial release with JavaScript and image tracking.
- Added British English translations.
