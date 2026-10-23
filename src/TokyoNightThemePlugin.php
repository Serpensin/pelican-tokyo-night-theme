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
    // A console stays dark in both Panel modes. It preserves terminal semantics,
    // keeps ANSI output legible, and remains visibly separate in light mode.
    const terminalPalette = () => ({
        background: '#24283b', foreground: '#a9b1d6', cursor: '#c0caf5', cursorAccent: '#24283b', selection: '#28344a',
        black: '#414868', red: '#f7768e', green: '#73daca', yellow: '#e0af68', blue: '#7aa2f7', magenta: '#bb9af7', cyan: '#7dcfff', white: '#c0caf5',
        brightBlack: '#414868', brightRed: '#f7768e', brightGreen: '#73daca', brightYellow: '#e0af68', brightBlue: '#7aa2f7', brightMagenta: '#bb9af7', brightCyan: '#7dcfff', brightWhite: '#c0caf5',
    });
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
