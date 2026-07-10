<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('historial_lonas', function (Blueprint $table) {
            $table->foreign(['lona_id'], 'historial_lonas_ibfk_1')->references(['id'])->on('lonas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['creado_por'], 'historial_lonas_ibfk_2')->references(['id'])->on('usuarios')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historial_lonas', function (Blueprint $table) {
            $table->dropForeign('historial_lonas_ibfk_1');
            $table->dropForeign('historial_lonas_ibfk_2');
        });
    }
};
