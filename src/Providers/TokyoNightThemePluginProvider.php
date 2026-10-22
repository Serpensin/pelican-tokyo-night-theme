<?php

namespace Serpensin\TokyoNightTheme\Providers;

use Illuminate\Support\ServiceProvider;

class TokyoNightThemePluginProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../css' => public_path('plugins/serpensin-tokyo-night-theme/css'),
        ], 'tokyo-night-assets');
    }
}
