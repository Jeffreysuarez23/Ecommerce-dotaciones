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
        Schema::create('pagos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('orden_id')->index();
            $table->string('metodo', 50)->nullable();
            $table->string('referencia_pasarela', 100)->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'reembolsado'])->nullable()->default('pendiente')->index();
            $table->decimal('monto', 10);
            $table->timestamp('pagado_en')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
