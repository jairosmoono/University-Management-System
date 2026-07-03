<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE fee_structures MODIFY COLUMN student_type VARCHAR(20) NOT NULL DEFAULT 'full-time'");
        DB::statement("UPDATE fee_structures SET student_type = 'full-time'");
        DB::statement("ALTER TABLE fee_structures CHANGE COLUMN student_type admission_type ENUM('full-time','part-time','distance','online','all') NOT NULL DEFAULT 'full-time'");
    }

    public function down(): void
    {
        DB::statement("UPDATE fee_structures SET admission_type = 'local'");
        DB::statement("ALTER TABLE fee_structures CHANGE COLUMN admission_type student_type ENUM('local','international','both') NOT NULL DEFAULT 'local'");
    }
};
