<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    App\Models\Dotacion::create(['nombre'=>'Test Dotacion', 'min_lonas'=>3, 'max_lonas'=>10, 'lonas_activas'=>0]);
    echo "OK\n";
} catch (\Exception $e) {
    echo $e->getMessage() . "\n";
}
