<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateToSupabase extends Command
{
    protected $signature = 'app:migrate-to-supabase';
    protected $description = 'Migrate data from local MySQL to Supabase PostgreSQL';

    public function handle()
    {
        $this->info('Starting data migration...');

        // Fetch all tables from mysql
        $mysqlTables = DB::connection('mysql')->select('SHOW TABLES');
        $allTables = [];
        foreach ($mysqlTables as $tableObj) {
            $tableName = (array)$tableObj;
            $allTables[] = array_values($tableName)[0];
        }

        // Filter out tables that don't need data migration
        $allTables = array_diff($allTables, [
            'migrations', 'cache', 'cache_locks', 'jobs', 'job_batches', 
            'sessions', 'password_reset_tokens', 'personal_access_tokens', 
            'tokens_restablecimiento_password'
        ]);

        try {
            DB::connection('pgsql')->statement("SET session_replication_role = 'replica';");
        } catch (\Exception $e) {
            $this->warn("Could not set session_replication_role. Foreign key errors might occur if tables aren't ordered. Error: " . $e->getMessage());
        }

        foreach ($allTables as $table) {
            $this->info("Migrating table: {$table}");
            $rows = DB::connection('mysql')->table($table)->get();
            $data = json_decode(json_encode($rows), true);

            if (count($data) > 0) {
                // Clear existing data just in case
                // DB::connection('pgsql')->table($table)->truncate(); // Might fail without cascade, better to just ignore

                $chunks = array_chunk($data, 500);
                foreach ($chunks as $chunk) {
                    DB::connection('pgsql')->table($table)->insert($chunk);
                }
                $this->info("Migrated " . count($data) . " rows for {$table}.");
                
                // Update PostgreSQL sequence
                if (Schema::connection('pgsql')->hasColumn($table, 'id')) {
                    $maxId = DB::connection('pgsql')->table($table)->max('id') ?? 0;
                    if ($maxId > 0) {
                        try {
                            DB::connection('pgsql')->statement("SELECT setval(pg_get_serial_sequence('\"{$table}\"', 'id'), {$maxId})");
                        } catch (\Exception $e) {
                            $this->warn("Could not update sequence for {$table}: " . $e->getMessage());
                        }
                    }
                }
            } else {
                $this->info("Table {$table} is empty.");
            }
        }

        try {
            DB::connection('pgsql')->statement("SET session_replication_role = 'origin';");
        } catch (\Exception $e) {}
        
        $this->info('Migration completed successfully!');
    }
}
