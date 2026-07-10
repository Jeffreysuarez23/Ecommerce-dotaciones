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
        Schema::create('ordenes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('usuario_id')->index();
            $table->unsignedBigInteger('direccion_id')->index();
            $table->unsignedBigInteger('cupon_id')->nullable()->index();
            $table->string('numero', 30)->unique();
            $table->enum('estado', ['pendiente', 'pagado', 'confirmada', 'procesando', 'enviado', 'entregado', 'cancelada', 'devuelta'])->nullable()->default('pendiente');
            $table->enum('tipo_precio', ['minorista', 'mayorista'])->nullable()->default('minorista');
            $table->decimal('subtotal', 10);
            $table->decimal('descuento', 10)->nullable()->default(0);
            $table->decimal('envio_costo', 10)->nullable()->default(0);
            $table->decimal('total', 10);
            $table->text('notas_cliente')->nullable();
            $table->timestamp('creado_en')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};
