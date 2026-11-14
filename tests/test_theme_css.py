from pathlib import Path

root = Path('.')
plugin = (root / 'src/TokyoNightThemePlugin.php').read_text()
provider = (root / 'src/Providers/TokyoNightThemePluginProvider.php').read_text()
dark = (root / 'css/tokyo-night.css').read_text()
light = (root / 'css/tokyo-night-light.css').read_text()
base = (root / 'css/base.css').read_text()

for token in [
    "return ['panels::head.end' => function (): string",
    "public_path('plugins/serpensin-tokyo-night-theme/css')",
    "__DIR__ . '/../css'",
    'File::files($source)',
    'File::copy($stylesheet->getPathname(), $published)',
    "tokyo-night.css",
    "tokyo-night-light.css",
    "MutationObserver",
    'terminalPalette',
    'class TokyoNightTerminal extends NativeTerminal',
    "Object.defineProperty(window, 'Xterm'",
]:
    assert token in plugin, f'missing theme-loader token: {token}'

for token in [
    "background: '#24283b'", "foreground: '#a9b1d6'", "green: '#73daca'", "selection: '#28344a'",
    "A console stays dark in both Panel modes",
]:
    assert token in plugin, f'missing terminal palette token: {token}'

for token in ['extends ServiceProvider', "tokyo-night-assets"]:
    assert token in provider, f'missing provider token: {token}'

for css, name, color in [(dark, 'dark', '#1a1b26'), (light, 'light', '#d5d6db')]:
    assert '@import url("./base.css");' in css, f'{name} stylesheet does not import the shared rules'
    assert color in css, f'{name} palette background missing'

for token in [
    '.fi-sidebar', '.fi-topbar', '.fi-section', '.fi-btn', '.fi-input-wrp', '.fi-ta-ctn', '.fi-modal-window',
    '#terminal .xterm-viewport', '#send-command', 'background-color: #24283b !important;',
    'div[wire\\:snapshot].fi-sc-component', 'box-shadow: none !important;',
    '.fi-fo-toggle-buttons .fi-btn[aria-pressed=\'true\']', '.fi-fo-toggle-buttons .fi-btn[aria-pressed=\'false\']',
    '.fi-btn.fi-color-primary[aria-pressed=\'true\']', '.fi-btn.fi-color-warning[aria-pressed=\'true\']', '.fi-btn.fi-color-danger[aria-pressed=\'true\']',
    'background: var(--tn-active-bg) !important;', 'background: var(--tn-raised) !important;',
    'background: var(--tn-green) !important;', 'background: var(--tn-orange) !important;',
    'color: var(--tn-active-fg) !important;', 'box-shadow: 0 0 0 1px var(--tn-active-bg) !important;',
]:
    assert token in base, f'missing shared theme rule: {token}'

for css, name in [(dark, 'dark'), (light, 'light')]:
    assert '--tn-active-bg:' in css, f'{name} palette active-tab background missing'
    assert '--tn-active-fg:' in css, f'{name} palette active-tab foreground missing'

print('theme CSS assertions passed')
