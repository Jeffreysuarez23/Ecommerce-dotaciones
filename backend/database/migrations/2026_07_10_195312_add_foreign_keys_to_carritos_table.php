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
        Schema::table('carritos', function (Blueprint $table) {
            $table->foreign(['usuario_id'], 'carritos_ibfk_1')->references(['id'])->on('usuarios')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['cupon_id'], 'carritos_ibfk_2')->references(['id'])->on('cupones')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carritos', function (Blueprint $table) {
            $table->dropForeign('carritos_ibfk_1');
            $table->dropForeign('carritos_ibfk_2');
        });
    }
};
