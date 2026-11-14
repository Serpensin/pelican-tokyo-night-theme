# Changelog

## 1.1.0

### Added

- Support for Pelican's built-in theme switcher through the `HasTheme` contract.
- Theme selection from the user menu or profile, including Pelican's administrator-controlled default and forced theme settings.

### Changed

- Requires Pelican Panel `>=1.0.0-rc.3`. Earlier Panel versions must remain on theme version 1.0.2.
- Theme colors, stylesheets, and console integration are loaded only while Tokyo Night is the active theme.
- Static stylesheets are synchronized when the active theme's head hook executes, rather than during global plugin registration.

The existing light/dark appearance behavior and Tokyo Night console palette are preserved.
