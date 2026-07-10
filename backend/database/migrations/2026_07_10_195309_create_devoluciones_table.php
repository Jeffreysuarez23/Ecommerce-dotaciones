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
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('orden_id')->index();
            $table->text('motivo');
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada', 'resuelta'])->nullable()->default('pendiente');
            $table->text('resolucion_admin')->nullable();
            $table->unsignedBigInteger('resuelto_por')->nullable()->index();
            $table->timestamp('creado_en')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devoluciones');
    }
};
