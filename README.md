# Tokyo Night for Pelican

Tokyo Night for Pelican brings both official Tokyo Night color modes to the Panel:

- **Tokyo Night Light** follows Pelican’s light appearance mode.
- **Tokyo Night** follows Pelican’s dark appearance mode.

Use Pelican’s normal light/dark-mode control to switch between them. This theme does not add a separate toggle.

## Preview

### Tokyo Night Light

![Tokyo Night Light preview](https://cdn.serpensin.com/serpensin/pelican-tokyonight-theme/light.png)

### Tokyo Night

![Tokyo Night dark preview](https://cdn.serpensin.com/serpensin/pelican-tokyonight-theme/dark.png)

## What changes

The theme applies the Tokyo Night palette to the Panel interface while preserving Pelican’s normal layout and behavior:

- page backgrounds, sidebar, and topbar
- cards, tables, and forms
- dialogs, tabs, badges, and notifications
- buttons, links, hover states, and keyboard-focus states

## Compatibility

- Version 1.1.1 requires Pelican Panel **1.0.0-rc.3 or newer within 1.x**. The plugin manifest enforces `^1.0.0-rc.3`, excluding Panel 2.0.0 and newer.
- Integrates with Pelican's built-in theme switcher through `App\Contracts\Plugins\HasTheme` (Panel PR #2595).
- Older Panel versions are not supported. Keep version 1.0.2 on those installations.

## Install

1. Place the plugin directory at:

   ```text
   plugins/serpensin-tokyo-night-theme
   ```

2. From the Pelican Panel directory, run:

   ```sh
   php artisan p:plugin:install serpensin-tokyo-night-theme
   ```

3. Select **Tokyo Night** from the user-menu theme picker or your profile. Administrators can set it as the default theme or force it in Settings.
4. Select your preferred light or dark appearance mode as usual. Choosing another theme or no theme stops Pelican from loading this theme's styles and console integration on the next page load.
