# Development Notes

## Scope

This repository is intentionally local-only until the theme is ready for publication. Do not add a remote, tag, or release artifact unless the user explicitly requests it.

## Theme architecture

- `src/TokyoNightThemePlugin.php` registers the Vite theme for every Pelican panel.
- `resources/css/theme.css` declares Tokyo Night Light tokens in `:root` and Tokyo Night tokens under `.dark`.
- Pelican’s existing appearance control selects the mode. Do not add a second mode toggle.

## Palette provenance

The palette values are adapted from `folke/tokyonight.nvim`, which is MIT licensed. Preserve the attribution comment in `resources/css/theme.css` and the plugin MIT license.

## Build requirements

Pelican builds theme assets during installation. Its execution environment must provide Node.js 22 and Yarn, either installed directly or through Corepack.

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
4. Verify the generated archive contains `plugin.json`, `src/`, `resources/`, `README.md`, and `LICENSE`.
