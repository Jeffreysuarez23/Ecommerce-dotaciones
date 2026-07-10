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
        Schema::table('orden_items', function (Blueprint $table) {
            $table->foreign(['orden_id'], 'orden_items_ibfk_1')->references(['id'])->on('ordenes')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['variante_id'], 'orden_items_ibfk_2')->references(['id'])->on('variantes_producto')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orden_items', function (Blueprint $table) {
            $table->dropForeign('orden_items_ibfk_1');
            $table->dropForeign('orden_items_ibfk_2');
        });
    }
};
