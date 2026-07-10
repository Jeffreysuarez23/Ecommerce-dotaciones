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
        Schema::table('devoluciones', function (Blueprint $table) {
            $table->foreign(['orden_id'], 'devoluciones_ibfk_1')->references(['id'])->on('ordenes')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['resuelto_por'], 'devoluciones_ibfk_2')->references(['id'])->on('usuarios')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devoluciones', function (Blueprint $table) {
            $table->dropForeign('devoluciones_ibfk_1');
            $table->dropForeign('devoluciones_ibfk_2');
        });
    }
};
