<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Widen to VARCHAR first so UPDATE is unrestricted, then finalize as ENUM
        DB::statement("ALTER TABLE students MODIFY COLUMN student_type VARCHAR(20) NOT NULL DEFAULT 'full-time'");
        DB::statement("UPDATE students SET student_type = 'full-time'");
        DB::statement("ALTER TABLE students CHANGE COLUMN student_type admission_type ENUM('full-time','part-time','distance','online') NOT NULL DEFAULT 'full-time'");
    }

    public function down(): void
    {
        DB::statement("UPDATE students SET admission_type = 'local'");
        DB::statement("ALTER TABLE students CHANGE COLUMN admission_type student_type ENUM('local','international') NOT NULL DEFAULT 'local'");
    }
};
