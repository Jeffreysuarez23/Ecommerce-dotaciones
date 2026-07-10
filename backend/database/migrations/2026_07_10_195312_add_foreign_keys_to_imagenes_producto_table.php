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
        Schema::table('imagenes_producto', function (Blueprint $table) {
            $table->foreign(['producto_id'], 'imagenes_producto_ibfk_1')->references(['id'])->on('productos')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['variante_id'], 'imagenes_producto_ibfk_2')->references(['id'])->on('variantes_producto')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('imagenes_producto', function (Blueprint $table) {
            $table->dropForeign('imagenes_producto_ibfk_1');
            $table->dropForeign('imagenes_producto_ibfk_2');
        });
    }
};
