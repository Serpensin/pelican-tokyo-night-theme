# Development Notes

## Repository and publication

- GitLab is the canonical repository: `https://gitlab.com/Serpensin/pelican-tokyo-night-theme`.
- GitHub is a GitLab-managed push mirror. Make source changes on `main`, push to GitLab, and verify the mirror ref afterwards.
- The project is source-only. Do not commit ZIP archives, generated bundles, or other release binaries.
- Release 1.1.1 corrects the compatibility constraint. Preserve all existing release tags, including v1.1.0; never replace a published tag for a metadata fix.

## Compatibility policy

The new integration targets the built-in theme switcher from Panel PR #2595 (currently on canary). It implements `App\Contracts\Plugins\HasTheme`, which extends Filament's plugin contract. Older Panel versions without this interface cannot load this revision.

Version 1.1.1 uses `"panel_version": "^1.0.0-rc.3"`. The installed Panel's `App\Models\Plugin::isCompatible()` supports exact versions and caret ranges, not generic Composer operators: a value beginning with `>=` is compared literally and rejects even the stated minimum. Validate every compatibility change against the real Panel model with unsaved instances, not only a manifest-string assertion. Require rc.2 rejection, rc.3 and stable 1.x acceptance, and 2.0.0 rejection. Do not replace this caret with an unbounded minimum without verifying upstream parser support.

The HasTheme implementation was installed on the GGC Panel, its theme discovery and public CSS were verified, and the maintainer confirmed that it works in the UI. This does not establish complete PelicanHub acceptance.

Do not publish an unrestricted update for older Panels. No update feed is created or changed as part of this local version preparation; verify the Panel/Hub update-selection behavior and preserve historical release descriptors when publication is explicitly requested.

## Theme architecture

- `src/TokyoNightThemePlugin.php` implements `HasTheme`; registration and boot are inert. Colors come from `getThemeColors()`, font and Vite entry return null, and `getThemeRenderHooks()` exposes the early head hook. Pelican gates its execution by the active theme.
- `css/tokyo-night.css` and `css/tokyo-night-light.css` define mode-specific tokens; both import `css/base.css` for shared component rules.
- `src/Providers/TokyoNightThemePluginProvider.php` exposes the CSS directory to Laravel’s publishing system.
- A MutationObserver follows Pelican’s `dark` HTML class and replaces the single theme stylesheet. Do not add a competing appearance toggle.
- Copy changed CSS files inside the active theme's head-hook closure, not during registration or hook collection. A destination-directory-exists check alone leaves old public CSS deployed after an API import/update.

## Console integration

Pelican’s upstream Console creates a local xterm instance with a hard-coded palette and has no stable theme-palette hook.

The theme therefore uses a best-effort early JavaScript integration: it intercepts the initial `window.Xterm` assignment, wraps `Xterm.Terminal`, and merges the Tokyo Night palette into the construction options before Pelican constructs the console. Existing terminal instances are refreshed when the Panel mode changes.

Keep the console dark in both Panel modes unless a real console screenshot demonstrates that a light terminal is usable. This preserves ANSI readability and distinguishes the console from the light Panel surface. Style `#terminal`, `.xterm`, `.xterm-viewport`, and `#send-command` together; xterm’s hidden helper textarea is not a visual target.

This relies on upstream implementation details. Re-test it after every Pelican update from a freshly opened Console page.

## Palette provenance

The primary palette values are adapted from `folke/tokyonight.nvim`, which is MIT licensed. Preserve attribution comments in the palette stylesheets and the plugin MIT license.

The dark terminal ANSI palette follows the local Kitty Tokyo Night profile. If adjusting the light terminal experimentally, take its complete terminal palette from the canonical Tokyo Night VS Code theme rather than mixing a few light-mode colors into the dark ANSI palette.

## Local verification

```sh
php -l src/TokyoNightThemePlugin.php
php -l src/Providers/TokyoNightThemePluginProvider.php
python3 tests/test_manifest.py
python3 tests/test_theme_css.py
php tests/test_theme_contract.php
# Requires a real Panel installation; uses unsaved model instances only:
php tests/test_panel_compatibility.php /path/to/panel
git diff --check
git archive --worktree-attributes --format=tar HEAD | tar -tf - | sort
```

For a Panel test, build the ZIP outside the repository, import it through the Application API, then read back the plugin status. Confirm that it is enabled, compatible, and has no status message. Open a newly navigated Console page to verify xterm styling; checking only the published CSS does not validate a running terminal instance.

## Future Hub submission

Before a Hub submission:

1. Keep `plugin.json.update_url` at the Hub-derived URL.
2. Add an append-only `update.json` release entry with an immutable source-archive URL.
3. Ensure `.gitattributes` excludes `DEVNOTES.md`, tests, and other maintainer-only metadata from runtime archives.
4. Verify that the generated archive contains `plugin.json`, `src/`, `config/`, `css/`, `README.md`, `CHANGELOG.md`, and `LICENSE`. Preview images use external URLs.
5. Scan the complete repository for Hub-restricted call patterns before submission.
