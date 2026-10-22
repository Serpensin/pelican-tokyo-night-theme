import json
from pathlib import Path

plugin = json.loads(Path('plugin.json').read_text())

assert plugin['id'] == 'serpensin-tokyo-night-theme'
assert plugin['category'] == 'theme'
assert plugin['api_version'] == 1
assert plugin['class'] == 'TokyoNightThemePlugin'
assert plugin['namespace'] == 'Serpensin\\TokyoNightTheme'
assert plugin['panels'] == ['admin', 'server', 'app']
assert plugin['panel_version'] is None
assert plugin['composer_packages'] is None
assert plugin['update_url'] == 'https://hub.pelican.dev/plugins/serpensin-tokyo-night-theme/update.json'

source = Path('src/TokyoNightThemePlugin.php').read_text()
assert "return 'serpensin-tokyo-night-theme';" in source
assert "renderHook('panels::head.end'" in source
assert "publishStylesheets" in source

print('theme manifest assertions passed')
