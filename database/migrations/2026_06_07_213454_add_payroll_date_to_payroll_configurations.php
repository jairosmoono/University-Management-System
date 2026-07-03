<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('payroll_configurations')->insertOrIgnore([
            'key'         => 'payroll_date',
            'label'       => 'Monthly Payroll Date',
            'value'       => '25',
            'description' => 'Day of the month on which payroll is processed (1–31)',
            'group'       => 'general',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('payroll_configurations')->where('key', 'payroll_date')->delete();
    }
};
