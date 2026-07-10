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
        Schema::create('cupones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo', 50)->unique();
            $table->enum('tipo', ['porcentaje', 'fijo']);
            $table->decimal('valor', 10);
            $table->decimal('monto_minimo_pedido', 10)->nullable()->default(0);
            $table->integer('limite_usos')->nullable();
            $table->integer('usos_actuales')->nullable()->default(0);
            $table->boolean('activo')->nullable()->default(true);
            $table->timestamp('expira_en')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupones');
    }
};
