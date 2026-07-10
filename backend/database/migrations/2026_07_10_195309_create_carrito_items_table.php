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
        Schema::create('carrito_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('carrito_id')->index();
            $table->unsignedBigInteger('variante_id')->index();
            $table->unsignedBigInteger('lona_id')->nullable();
            $table->integer('cantidad')->nullable()->default(1);

            $table->unique(['carrito_id', 'variante_id'], 'uq_carrito');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrito_items');
    }
};
