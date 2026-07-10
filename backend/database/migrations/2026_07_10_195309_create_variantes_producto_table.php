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
        Schema::create('variantes_producto', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('lona_id')->nullable()->index();
            $table->string('sku', 100)->nullable();
            $table->string('color', 50)->nullable();
            $table->string('color_hex', 20)->nullable();
            $table->string('talla', 10)->nullable();
            $table->integer('stock')->nullable()->default(0);
            $table->decimal('precio_extra', 10)->nullable()->default(0);
            $table->integer('descuento')->nullable()->default(0);
            $table->timestamp('eliminado_en')->nullable();

            $table->index(['color', 'talla', 'stock'], 'idx_filtros');
            $table->unique(['producto_id', 'color', 'talla'], 'producto_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variantes_producto');
    }
};
