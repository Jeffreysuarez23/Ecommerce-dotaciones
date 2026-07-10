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
        Schema::create('lona_tallas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lona_id');
            $table->string('talla', 10);
            $table->integer('cantidad')->nullable()->default(0);

            $table->index(['lona_id', 'talla'], 'idx_lona_talla');
            $table->unique(['lona_id', 'talla'], 'lona_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lona_tallas');
    }
};
