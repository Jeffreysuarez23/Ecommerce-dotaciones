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
        Schema::create('productos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('categoria_id')->nullable()->index();
            $table->string('nombre', 150);
            $table->string('slug', 160)->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('precio_minorista', 10);
            $table->decimal('precio_mayorista', 10);
            $table->integer('min_cantidad_mayorista')->nullable()->default(12);
            $table->boolean('publicado')->nullable()->default(false);
            $table->boolean('permitir_sin_stock')->nullable()->default(true);
            $table->timestamp('eliminado_en')->nullable();
            $table->timestamp('creado_en')->nullable()->useCurrent();
            $table->boolean('destacado')->nullable()->default(false);

            $table->fullText(['nombre', 'descripcion'], 'nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
