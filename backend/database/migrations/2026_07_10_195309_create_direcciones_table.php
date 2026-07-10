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
        Schema::create('direcciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('usuario_id')->index();
            $table->string('nombre_recibe', 150)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('etiqueta', 50)->nullable()->default('Casa');
            $table->string('departamento', 80);
            $table->string('ciudad', 80);
            $table->string('direccion', 250);
            $table->string('referencia', 250)->nullable();
            $table->string('codigo_postal', 10)->nullable();
            $table->boolean('es_principal')->nullable()->default(false);
            $table->timestamp('eliminado_en')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direcciones');
    }
};
