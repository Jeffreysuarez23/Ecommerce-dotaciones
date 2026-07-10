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
        Schema::table('configuraciones_cms', function (Blueprint $table) {
            $table->foreign(['actualizado_por'], 'configuraciones_cms_ibfk_1')->references(['id'])->on('usuarios')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configuraciones_cms', function (Blueprint $table) {
            $table->dropForeign('configuraciones_cms_ibfk_1');
        });
    }
};
