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
        Schema::create('historial_lonas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lona_id')->index();
            $table->unsignedBigInteger('orden_item_id')->nullable();
            $table->enum('accion', ['descuento', 'ajuste_manual', 'ingreso', 'agotado']);
            $table->string('talla', 10)->nullable();
            $table->integer('cantidad_cambio')->nullable();
            $table->integer('cantidad_restante')->nullable();
            $table->json('snapshot_json')->nullable()->comment('Estado de tallas al momento del evento');
            $table->text('notas')->nullable();
            $table->unsignedBigInteger('creado_por')->nullable()->index();
            $table->timestamp('creado_en')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_lonas');
    }
};
