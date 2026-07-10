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
        Schema::create('envios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('orden_id')->index();
            $table->string('transportadora', 100)->nullable();
            $table->string('guia', 100)->nullable();
            $table->enum('estado', ['preparando', 'enviado', 'en_ruta', 'entregado', 'fallido'])->nullable()->default('preparando')->index();
            $table->date('fecha_entrega_estimada')->nullable();
            $table->timestamp('entregado_en')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envios');
    }
};
