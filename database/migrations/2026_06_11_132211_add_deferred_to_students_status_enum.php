<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('active','inactive','suspended','graduated','withdrawn','deferred') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        DB::statement("UPDATE students SET status = 'inactive' WHERE status = 'deferred'");
        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('active','inactive','suspended','graduated','withdrawn') NOT NULL DEFAULT 'active'");
    }
};
