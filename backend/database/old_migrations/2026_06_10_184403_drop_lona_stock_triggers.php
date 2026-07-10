<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_stock_lona_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_stock_lona_update');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
