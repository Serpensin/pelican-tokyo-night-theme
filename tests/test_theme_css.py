from pathlib import Path

root = Path('.')
plugin = (root / 'src/TokyoNightThemePlugin.php').read_text()
provider = (root / 'src/Providers/TokyoNightThemePluginProvider.php').read_text()
dark = (root / 'css/tokyo-night.css').read_text()
light = (root / 'css/tokyo-night-light.css').read_text()
base = (root / 'css/base.css').read_text()

for token in [
    "renderHook('panels::head.end'",
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
    "background: '#1a1b26'", "foreground: '#a9b1d6'", "green: '#73daca'", "selection: '#28344a'",
    "background: '#e0e0e6'", "foreground: '#3760bf'", "black: '#343b58'", "cyan: '#007197'",
]:
    assert token in plugin, f'missing terminal palette token: {token}'

for token in ['extends ServiceProvider', "tokyo-night-assets"]:
    assert token in provider, f'missing provider token: {token}'

for css, name, color in [(dark, 'dark', '#1a1b26'), (light, 'light', '#d5d6db')]:
    assert '@import url("./base.css");' in css, f'{name} stylesheet does not import the shared rules'
    assert color in css, f'{name} palette background missing'

for token in [
    '.fi-sidebar', '.fi-topbar', '.fi-section', '.fi-btn', '.fi-input-wrp', '.fi-ta-ctn', '.fi-modal-window',
    '#terminal .xterm-viewport', 'div[wire\\:snapshot].fi-sc-component', 'box-shadow: none !important;',
]:
    assert token in base, f'missing shared theme rule: {token}'

print('theme CSS assertions passed')
