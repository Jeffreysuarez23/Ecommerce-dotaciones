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
        Schema::create('orden_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('orden_id')->index();
            $table->unsignedBigInteger('variante_id')->index();
            $table->unsignedBigInteger('lona_id_snapshot')->nullable();
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10);
            $table->decimal('total_linea', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_items');
    }
};
