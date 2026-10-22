from pathlib import Path

css = Path('resources/css/theme.css').read_text()

for token in [
    '@import',
    ':root',
    '.dark',
    '--tokyo-bg',
    '--tokyo-blue',
    '#1a1b26',
    '#d5d6db',
    '.fi-sidebar',
    '.fi-topbar',
    '.fi-section',
    '.fi-btn',
    '.fi-input-wrp',
    '.fi-ta-ctn',
    '.fi-modal-window',
]:
    assert token in css, f'missing theme token: {token}'

assert css.index(':root') < css.index('.dark {'), 'light tokens must be declared before dark overrides'
print('theme CSS assertions passed')
