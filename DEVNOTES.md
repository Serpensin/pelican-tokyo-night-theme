# Development Notes

## Repository and publication

- GitLab is the canonical repository: `https://gitlab.com/Serpensin/pelican-tokyo-night-theme`.
- GitHub is a GitLab-managed push mirror. Make source changes on `main`, push to GitLab, and verify the mirror ref afterwards.
- The project is source-only. Do not commit ZIP archives, generated bundles, or other release binaries.
- No release tag or Hub submission exists yet. Create either only when explicitly requested.

## Compatibility policy

The theme is tested with Pelican Panel `1.0.0-rc.2`.

`plugin.json` deliberately uses `"panel_version": null`; it does not claim a SemVer constraint that would exclude the currently tested release candidate. Update the README and this note after testing against a different Panel version.

## Theme architecture

- `src/TokyoNightThemePlugin.php` publishes the static `css/` directory to Pelican’s public plugin-assets path and injects the active stylesheet through a Filament head render hook.
- `css/tokyo-night.css` and `css/tokyo-night-light.css` define mode-specific tokens; both import `css/base.css` for shared component rules.
- `src/Providers/TokyoNightThemePluginProvider.php` exposes the CSS directory to Laravel’s publishing system.
- A MutationObserver follows Pelican’s `dark` HTML class and replaces the single theme stylesheet. Do not add a competing appearance toggle.
- Copy changed CSS files during plugin registration. A destination-directory-exists check alone leaves old public CSS deployed after an API import/update.

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
git diff --check
git archive --worktree-attributes --format=tar HEAD | tar -tf - | sort
```

For a Panel test, build the ZIP outside the repository, import it through the Application API, then read back the plugin status. Confirm that it is enabled, compatible, and has no status message. Open a newly navigated Console page to verify xterm styling; checking only the published CSS does not validate a running terminal instance.

## Future Hub submission

Before a Hub submission:

1. Keep `plugin.json.update_url` at the Hub-derived URL.
2. Add an append-only `update.json` release entry with an immutable source-archive URL.
3. Ensure `.gitattributes` excludes `DEVNOTES.md`, tests, and other maintainer-only metadata from runtime archives.
4. Verify that the generated archive contains `plugin.json`, `src/`, `config/`, `css/`, `images/`, `README.md`, and `LICENSE`.
5. Scan the complete repository for Hub-restricted call patterns before submission.
