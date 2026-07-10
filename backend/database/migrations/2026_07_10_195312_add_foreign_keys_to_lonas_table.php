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
        Schema::table('lonas', function (Blueprint $table) {
            $table->foreign(['categoria_id'])->references(['id'])->on('categorias')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['dotacion_id'], 'lonas_ibfk_1')->references(['id'])->on('dotaciones')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lonas', function (Blueprint $table) {
            $table->dropForeign('lonas_categoria_id_foreign');
            $table->dropForeign('lonas_ibfk_1');
        });
    }
};
