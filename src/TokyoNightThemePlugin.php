<?php

namespace Serpensin\TokyoNightTheme;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\File;

class TokyoNightThemePlugin implements Plugin
{
    public function getId(): string
    {
        return 'serpensin-tokyo-night-theme';
    }

    public function register(Panel $panel): void
    {
        $this->publishStylesheets();

        $panel->colors([
            'primary' => Color::hex('#7aa2f7'),
            'info' => Color::hex('#7dcfff'),
            'success' => Color::hex('#9ece6a'),
            'warning' => Color::hex('#e0af68'),
            'danger' => Color::hex('#f7768e'),
        ]);

        $dark = asset('plugins/serpensin-tokyo-night-theme/css/tokyo-night.css');
        $light = asset('plugins/serpensin-tokyo-night-theme/css/tokyo-night-light.css');

        $panel->renderHook('panels::head.end', fn (): string => <<<HTML
<link id="tokyo-night-theme-stylesheet" rel="stylesheet">
<script>
(() => {
    const dark = '{$dark}';
    const light = '{$light}';
    const terminals = new Set();
    const stylesheet = () => document.getElementById('tokyo-night-theme-stylesheet');
    const terminalPalette = () => document.documentElement.classList.contains('dark')
        ? { background: '#292c3d', foreground: '#c0caf5', cursor: '#c0caf5', selection: '#33467c' }
        : { background: '#e0e0e6', foreground: '#3760bf', cursor: '#3760bf', selection: '#bcc7e8' };
    const refreshTerminals = () => terminals.forEach((terminal) => {
        terminal.options.theme = { ...terminal.options.theme, ...terminalPalette() };
    });
    const wrapTerminal = (xterm) => {
        if (!xterm?.Terminal || xterm.__tokyoNightTerminalWrapped) return;
        const NativeTerminal = xterm.Terminal;
        xterm.Terminal = class TokyoNightTerminal extends NativeTerminal {
            constructor(options = {}) {
                super({ ...options, theme: { ...options.theme, ...terminalPalette() } });
                terminals.add(this);
            }
        };
        Object.defineProperty(xterm, '__tokyoNightTerminalWrapped', { value: true });
    };
    const installTerminalWrapper = () => {
        if (window.Xterm) {
            wrapTerminal(window.Xterm);
            return;
        }
        Object.defineProperty(window, 'Xterm', {
            configurable: true,
            get: () => undefined,
            set: (xterm) => {
                wrapTerminal(xterm);
                Object.defineProperty(window, 'Xterm', { configurable: true, writable: true, value: xterm });
            },
        });
    };
    const apply = () => {
        const link = stylesheet();
        if (!link) return;
        const source = document.documentElement.classList.contains('dark') ? dark : light;
        if (link.getAttribute('href') !== source) link.setAttribute('href', source);
        refreshTerminals();
    };
    installTerminalWrapper();
    new MutationObserver(apply).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    document.addEventListener('livewire:navigated', apply);
    window.addEventListener('theme-changed', apply);
    apply();
})();
</script>
HTML);
    }

    public function boot(Panel $panel): void {}

    private function publishStylesheets(): void
    {
        $source = __DIR__ . '/../css';
        $destination = public_path('plugins/serpensin-tokyo-night-theme/css');

        if (!File::isDirectory($source)) {
            return;
        }

        File::ensureDirectoryExists($destination);

        foreach (File::files($source) as $stylesheet) {
            $published = $destination . '/' . $stylesheet->getFilename();

            if (!File::exists($published) || File::get($stylesheet->getPathname()) !== File::get($published)) {
                File::copy($stylesheet->getPathname(), $published);
            }
        }
    }
}
