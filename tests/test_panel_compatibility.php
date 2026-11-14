<?php

// Run against the real Panel model; no database records are saved.
// php tests/test_panel_compatibility.php /path/to/panel [plugin.json]
$panelRoot = $argv[1] ?? throw new RuntimeException('Supply the Panel root directory');
$manifestPath = $argv[2] ?? __DIR__ . '/../plugin.json';
$manifest = json_decode(file_get_contents($manifestPath), true, flags: JSON_THROW_ON_ERROR);
require $panelRoot . '/vendor/autoload.php';
$app = require $panelRoot . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$plugin = new App\Models\Plugin();
$plugin->panel_version = $manifest['panel_version'];
foreach ([
    '1.0.0-rc.2' => false,
    '1.0.0-rc.3' => true,
    '1.0.0-rc.4' => true,
    '1.0.0' => true,
    '1.1.0' => true,
    '2.0.0' => false,
] as $version => $expected) {
    $actual = $plugin->isCompatible($version);
    if ($actual !== $expected) {
        throw new RuntimeException('Unexpected Panel compatibility for ' . $version);
    }
    print $version . ': ' . ($actual ? 'allowed' : 'blocked') . PHP_EOL;
}
print "real Panel compatibility assertions passed\n";
