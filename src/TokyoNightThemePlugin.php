<?php

namespace Serpensin\TokyoNightTheme;

use Filament\Contracts\Plugin;
use Filament\Panel;

class TokyoNightThemePlugin implements Plugin
{
    public function getId(): string
    {
        return 'serpensin-tokyo-night-theme';
    }

    public function register(Panel $panel): void
    {
        $panel->viteTheme('plugins/serpensin-tokyo-night-theme/resources/css/theme.css');
    }

    public function boot(Panel $panel): void {}
}
