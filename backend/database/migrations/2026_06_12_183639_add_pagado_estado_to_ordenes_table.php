<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE ordenes MODIFY COLUMN estado ENUM('pendiente','pagado','confirmada','procesando','enviado','entregado','cancelada','devuelta') DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE ordenes MODIFY COLUMN estado ENUM('pendiente','confirmada','procesando','enviado','entregado','cancelada','devuelta') DEFAULT 'pendiente'");
    }
};
