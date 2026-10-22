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
    "tokyo-night.css",
    "tokyo-night-light.css",
    "MutationObserver",
]:
    assert token in plugin, f'missing theme-loader token: {token}'

for token in ['extends ServiceProvider', "tokyo-night-assets"]:
    assert token in provider, f'missing provider token: {token}'

for css, name, color in [(dark, 'dark', '#1a1b26'), (light, 'light', '#d5d6db')]:
    assert '@import url("./base.css");' in css, f'{name} stylesheet does not import the shared rules'
    assert color in css, f'{name} palette background missing'

for token in ['.fi-sidebar', '.fi-topbar', '.fi-section', '.fi-btn', '.fi-input-wrp', '.fi-ta-ctn', '.fi-modal-window']:
    assert token in base, f'missing shared theme rule: {token}'

print('theme CSS assertions passed')
