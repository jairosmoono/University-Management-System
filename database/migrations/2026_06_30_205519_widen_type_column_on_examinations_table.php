<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE examinations MODIFY type VARCHAR(50) NOT NULL DEFAULT 'final'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE examinations MODIFY type ENUM('mid_term','final','supplementary','resit') NOT NULL DEFAULT 'final'");
    }
};
