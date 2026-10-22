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
    const stylesheet = () => document.getElementById('tokyo-night-theme-stylesheet');
    const apply = () => {
        const link = stylesheet();
        if (!link) return;
        const source = document.documentElement.classList.contains('dark') ? dark : light;
        if (link.getAttribute('href') !== source) link.setAttribute('href', source);
    };
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

        if (File::isDirectory($source) && !File::isDirectory($destination)) {
            File::copyDirectory($source, $destination);
        }
    }
}
