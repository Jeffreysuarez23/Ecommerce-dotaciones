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
        Schema::create('dotaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 120);
            $table->text('descripcion')->nullable();
            $table->unsignedTinyInteger('min_lonas')->nullable()->default(3);
            $table->unsignedTinyInteger('max_lonas')->nullable()->default(10);
            $table->integer('lonas_activas')->nullable()->default(0);
            $table->timestamp('alerta_enviada_en')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dotaciones');
    }
};
