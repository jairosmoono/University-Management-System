<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Expand enum to include both values so existing 'withdrawn' rows remain valid
        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('active','inactive','suspended','graduated','withdrawn','dropped_out','deferred') NOT NULL DEFAULT 'active'");
        // Migrate existing data
        DB::statement("UPDATE students SET status = 'dropped_out' WHERE status = 'withdrawn'");
        // Remove 'withdrawn' now that no rows use it
        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('active','inactive','suspended','graduated','dropped_out','deferred') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('active','inactive','suspended','graduated','withdrawn','dropped_out','deferred') NOT NULL DEFAULT 'active'");
        DB::statement("UPDATE students SET status = 'withdrawn' WHERE status = 'dropped_out'");
        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('active','inactive','suspended','graduated','withdrawn','deferred') NOT NULL DEFAULT 'active'");
    }
};
