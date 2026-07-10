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
        Schema::create('configuraciones_cms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('clave', 100)->unique();
            $table->text('valor')->nullable();
            $table->enum('tipo', ['texto', 'color', 'imagen', 'json', 'booleano'])->nullable()->default('texto');
            $table->string('grupo', 50)->nullable()->default('branding');
            $table->unsignedBigInteger('actualizado_por')->nullable()->index();
            $table->timestamp('actualizado_en')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuraciones_cms');
    }
};
