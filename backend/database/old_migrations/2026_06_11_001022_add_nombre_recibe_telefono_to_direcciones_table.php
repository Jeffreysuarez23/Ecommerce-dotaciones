<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('direcciones', function (Blueprint $table) {
            $table->string('nombre_recibe', 150)->nullable()->after('usuario_id');
            $table->string('telefono', 20)->nullable()->after('nombre_recibe');
            $table->string('referencia', 250)->nullable()->after('direccion');
        });
    }

    public function down(): void
    {
        Schema::table('direcciones', function (Blueprint $table) {
            $table->dropColumn(['nombre_recibe', 'telefono', 'referencia']);
        });
    }
};
