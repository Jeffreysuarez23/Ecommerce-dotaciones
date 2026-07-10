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
        Schema::table('ordenes', function (Blueprint $table) {
            $table->foreign(['usuario_id'], 'ordenes_ibfk_1')->references(['id'])->on('usuarios')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['direccion_id'], 'ordenes_ibfk_2')->references(['id'])->on('direcciones')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['cupon_id'], 'ordenes_ibfk_3')->references(['id'])->on('cupones')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropForeign('ordenes_ibfk_1');
            $table->dropForeign('ordenes_ibfk_2');
            $table->dropForeign('ordenes_ibfk_3');
        });
    }
};
