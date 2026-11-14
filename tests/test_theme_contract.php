<?php

// Standalone lifecycle regression. Framework doubles are not a live Panel test.
namespace Filament\Contracts {
    interface Plugin
    {
        public function getId(): string;
        public function register(\Filament\Panel $panel): void;
        public function boot(\Filament\Panel $panel): void;
    }
}

namespace App\Contracts\Plugins {
    // HasTheme signatures from pelican-dev/panel PR #2595 (main).
    interface HasTheme extends \Filament\Contracts\Plugin
    {
        public function getThemeColors(): array;
        public function getThemeFont(): ?string;
        public function getThemeViteEntry(): string|array|null;
        public function getThemeRenderHooks(): array;
    }
}

namespace Filament {
    class Panel
    {
        public function __call(string $name, array $arguments): never
        {
            throw new \RuntimeException('Unexpected global panel mutation: ' . $name);
        }
    }
}

namespace Filament\Support\Colors {
    class Color
    {
        public static function hex(string $hex): array { return [500 => $hex]; }
    }
}

namespace Illuminate\Support\Facades {
    class File
    {
        public static int $publications = 0;
        public static function isDirectory(string $path): bool { return true; }
        public static function ensureDirectoryExists(string $path): void { self::$publications++; }
        public static function files(string $path): array { return []; }
    }
}

namespace {
    function asset(string $path): string { return 'https://panel.example/' . $path; }
    function public_path(string $path): string { return '/unused-public/' . $path; }
    function check(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new \RuntimeException($message);
        }
    }

    require __DIR__ . '/../src/TokyoNightThemePlugin.php';
    $theme = new \Serpensin\TokyoNightTheme\TokyoNightThemePlugin();
    check($theme instanceof \App\Contracts\Plugins\HasTheme, 'Theme must implement HasTheme');
    $panel = new \Filament\Panel();
    $theme->register($panel);
    $theme->boot($panel);
    check(\Illuminate\Support\Facades\File::$publications === 0, 'Inactive registration must not publish assets');
    check($theme->getThemeViteEntry() === null, 'Static CSS must not require a Vite build');
    check($theme->getThemeFont() === null, 'Keep the Panel font');
    check($theme->getThemeColors() === [
        'primary' => [500 => '#7aa2f7'],
        'info' => [500 => '#7dcfff'],
        'success' => [500 => '#9ece6a'],
        'warning' => [500 => '#e0af68'],
        'danger' => [500 => '#f7768e'],
    ], 'Preserve all semantic colors');
    $hooks = $theme->getThemeRenderHooks();
    check(array_keys($hooks) === ['panels::head.end'], 'Expose one early theme hook');
    check($hooks['panels::head.end'] instanceof \Closure, 'Theme hooks must be closures');
    check(\Illuminate\Support\Facades\File::$publications === 0, 'Collecting hooks must remain inert');
    $html = $hooks['panels::head.end']();
    check(\Illuminate\Support\Facades\File::$publications === 1, 'Publish only when the active hook executes');
    foreach (['tokyo-night.css', 'tokyo-night-light.css', 'TokyoNightTerminal', 'MutationObserver'] as $token) {
        check(str_contains($html, $token), 'Missing active theme feature: ' . $token);
    }
    print "theme contract lifecycle assertions passed\n";
}
