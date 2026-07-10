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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100);
            $table->string('email', 180)->unique();
            $table->string('password')->nullable()->comment('NULL si usa Google OAuth');
            $table->string('google_id', 100)->nullable()->unique();
            $table->enum('rol', ['cliente', 'admin', 'super_admin'])->nullable()->default('cliente')->index();
            $table->string('telefono', 20)->nullable();
            $table->string('avatar_url', 500)->nullable();
            $table->timestamp('email_verificado_en')->nullable();
            $table->rememberToken();
            $table->timestamp('eliminado_en')->nullable();
            $table->timestamp('creado_en')->nullable()->useCurrent();
            $table->timestamp('actualizado_en')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
