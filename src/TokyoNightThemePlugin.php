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
    // Dark mode mirrors the user's Kitty Tokyo Night palette. Light mode uses the
    // corresponding Tokyo Night Day ANSI palette so console output stays legible.
    const terminalPalette = () => document.documentElement.classList.contains('dark')
        ? {
            background: '#1a1b26', foreground: '#a9b1d6', cursor: '#c0caf5', cursorAccent: '#1a1b26', selection: '#28344a',
            black: '#414868', red: '#f7768e', green: '#73daca', yellow: '#e0af68', blue: '#7aa2f7', magenta: '#bb9af7', cyan: '#7dcfff', white: '#c0caf5',
            brightBlack: '#414868', brightRed: '#f7768e', brightGreen: '#73daca', brightYellow: '#e0af68', brightBlue: '#7aa2f7', brightMagenta: '#bb9af7', brightCyan: '#7dcfff', brightWhite: '#c0caf5',
        }
        : {
            background: '#e0e0e6', foreground: '#3760bf', cursor: '#3760bf', cursorAccent: '#e0e0e6', selection: '#bcc7e8',
            black: '#343b58', red: '#f52a65', green: '#587539', yellow: '#8c6c3e', blue: '#2e7de9', magenta: '#7847bd', cyan: '#007197', white: '#3760bf',
            brightBlack: '#6172b0', brightRed: '#f52a65', brightGreen: '#587539', brightYellow: '#8c6c3e', brightBlue: '#2e7de9', brightMagenta: '#7847bd', brightCyan: '#007197', brightWhite: '#3760bf',
        };
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
