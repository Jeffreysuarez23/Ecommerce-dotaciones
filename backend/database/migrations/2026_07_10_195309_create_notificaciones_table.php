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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('usuario_id')->nullable()->index()->comment('NULL = Para todos los admins');
            $table->enum('tipo', ['stock_bajo', 'orden', 'sistema', 'marketing'])->nullable()->default('sistema');
            $table->string('titulo', 200)->nullable();
            $table->text('mensaje')->nullable();
            $table->timestamp('leido_en')->nullable();
            $table->unsignedBigInteger('confirmado_por')->nullable()->index()->comment('Doble check de la BD vieja');
            $table->timestamp('creado_en')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
