# Tokyo Night for Pelican

A Pelican theme plugin with both official Tokyo Night color modes:

- Tokyo Night Light is used while Pelican is in light mode.
- Tokyo Night is used while Pelican is in dark mode.

The user’s existing Pelican light/dark-mode control chooses the palette; the plugin does not add a second, competing toggle.

## Design

The theme ports the Tokyo Night palette to Pelican/Filament surfaces: page backgrounds, sidebar, topbar, cards, tables, forms, modals, tabs, badges, alerts, and focus states. It keeps the original layout and behavior intact.

Palette values are adapted from [folke/tokyonight.nvim](https://github.com/folke/tokyonight.nvim), licensed under MIT. See `LICENSE` for this plugin’s license.

## Requirements

- Pelican Panel `^1.0.0`
- Node.js 22 and Yarn/Corepack available to the Panel asset-build process

## Install

1. Put the directory in `plugins/serpensin-tokyo-night-theme`.
2. From the Panel directory, run:

   ```sh
   php artisan p:plugin:install serpensin-tokyo-night-theme
   ```

3. Select light or dark mode in Pelican’s normal appearance control.

## Local checks

```sh
php -l src/TokyoNightThemePlugin.php
python3 tests/test_manifest.py
python3 tests/test_theme_css.py
```

## Development status

This repository is intentionally local-only for now. It has no remote and no release tag.
