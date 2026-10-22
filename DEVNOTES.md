# Development Notes

## Scope

This repository is intentionally local-only until the theme is ready for publication. Do not add a remote, tag, or release artifact unless the user explicitly requests it.

## Theme architecture

- `src/TokyoNightThemePlugin.php` publishes the static `css/` directory to Pelican’s public plugin-assets path and injects the active stylesheet through a Filament head render hook.
- `css/tokyo-night.css` and `css/tokyo-night-light.css` provide the two palettes; both import `css/base.css` for shared component rules.
- `src/Providers/TokyoNightThemePluginProvider.php` exposes the CSS directory to Laravel’s publishing system.
- A MutationObserver follows Pelican’s existing `dark` HTML class. Do not add a second mode toggle.

## Palette provenance

The palette values are adapted from `folke/tokyonight.nvim`, which is MIT licensed. Preserve the attribution comments in the two palette stylesheets and the plugin MIT license.

## Asset delivery

The structure follows StarryNight’s static-asset pattern rather than using a Vite theme build. The theme class publishes the `css/` directory beneath Pelican’s public plugin assets and swaps between the dark and light stylesheets in the browser.

## Local verification

```sh
php -l src/TokyoNightThemePlugin.php
python3 tests/test_manifest.py
python3 tests/test_theme_css.py
git diff --check
git archive --worktree-attributes --format=tar HEAD | tar -tf - | sort
```

## Future Hub publication

Before a Hub submission:

1. Keep `plugin.json.update_url` at the Hub-derived URL.
2. Add an append-only release entry to `update.json` with the immutable source-archive URL.
3. Ensure `.gitattributes` excludes this file, tests, and other maintainer-only metadata from the runtime archive.
4. Verify the generated archive contains `plugin.json`, `src/`, `config/`, `css/`, `README.md`, and `LICENSE`.
