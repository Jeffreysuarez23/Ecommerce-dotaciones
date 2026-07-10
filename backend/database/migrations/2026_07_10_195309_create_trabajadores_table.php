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
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->string('documento')->unique();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('cargo');
            $table->date('fecha_ingreso');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->string('hoja_vida_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajadores');
    }
};
