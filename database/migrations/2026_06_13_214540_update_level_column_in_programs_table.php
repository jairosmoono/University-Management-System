<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Change ENUM to VARCHAR so values aren't restricted at the DB level
        DB::statement("ALTER TABLE `programs` MODIFY `level` VARCHAR(50) NOT NULL DEFAULT 'degree'");

        // Map old values to new ones
        DB::table('programs')->where('level', 'undergraduate')->update(['level' => 'degree']);
        DB::table('programs')->where('level', 'postgraduate')->update(['level' => 'degree']);
    }

    public function down(): void
    {
        // Reverse new values back before restoring enum
        DB::table('programs')->where('level', 'degree')->update(['level' => 'undergraduate']);
        DB::table('programs')->where('level', 'craft_certificate')->update(['level' => 'certificate']);
        DB::table('programs')->where('level', 'trade_test_certificate')->update(['level' => 'certificate']);

        DB::statement("ALTER TABLE `programs` MODIFY `level` ENUM('undergraduate','postgraduate','diploma','certificate') NOT NULL DEFAULT 'undergraduate'");
    }
};
