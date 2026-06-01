<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (!Schema::hasColumn('direcciones', 'nombre_recibe')) {
    DB::statement("ALTER TABLE direcciones ADD COLUMN nombre_recibe VARCHAR(150) NULL AFTER usuario_id");
    echo "Added nombre_recibe\n";
}
if (!Schema::hasColumn('direcciones', 'telefono')) {
    DB::statement("ALTER TABLE direcciones ADD COLUMN telefono VARCHAR(20) NULL AFTER nombre_recibe");
    echo "Added telefono\n";
}
if (!Schema::hasColumn('direcciones', 'referencia')) {
    DB::statement("ALTER TABLE direcciones ADD COLUMN referencia VARCHAR(250) NULL AFTER direccion");
    echo "Added referencia\n";
}

echo "Migration complete!\n";
