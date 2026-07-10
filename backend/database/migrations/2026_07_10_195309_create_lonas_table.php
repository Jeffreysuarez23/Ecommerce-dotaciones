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
        Schema::create('lonas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dotacion_id')->index();
            $table->string('codigo', 50)->unique();
            $table->string('tipo_producto', 80)->nullable();
            $table->unsignedBigInteger('categoria_id')->nullable()->index();
            $table->enum('estado', ['nuevo', 'usado'])->nullable()->default('nuevo');
            $table->boolean('activa')->nullable()->default(true);
            $table->timestamp('creado_en')->nullable()->useCurrent();
            $table->integer('capacidad_maxima')->default(50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lonas');
    }
};
