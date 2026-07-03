<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_configurations', function (Blueprint $table) {
            $table->string('key', 60)->primary();
            $table->string('label', 150);
            $table->string('value', 255);
            $table->string('description', 255)->nullable();
            $table->string('group', 50)->default('general'); // paye, napsa, nhima, general
            $table->timestamps();
        });

        // Seed default values
        $now = now();
        DB::table('payroll_configurations')->insert([
            // PAYE bands (Zambia ZRA – monthly thresholds)
            ['key' => 'paye_band1_max',  'label' => 'PAYE Band 1 Upper Limit (Monthly)',  'value' => '4800',  'description' => 'Income up to this amount is tax-free',    'group' => 'paye',    'created_at' => $now, 'updated_at' => $now],
            ['key' => 'paye_band2_max',  'label' => 'PAYE Band 2 Upper Limit (Monthly)',  'value' => '6900',  'description' => 'Upper limit for 25% tax band',           'group' => 'paye',    'created_at' => $now, 'updated_at' => $now],
            ['key' => 'paye_band3_max',  'label' => 'PAYE Band 3 Upper Limit (Monthly)',  'value' => '9200',  'description' => 'Upper limit for 30% tax band',           'group' => 'paye',    'created_at' => $now, 'updated_at' => $now],
            ['key' => 'paye_band2_rate', 'label' => 'PAYE Band 2 Rate (%)',                'value' => '25',    'description' => 'Tax rate for band 2 income',             'group' => 'paye',    'created_at' => $now, 'updated_at' => $now],
            ['key' => 'paye_band3_rate', 'label' => 'PAYE Band 3 Rate (%)',                'value' => '30',    'description' => 'Tax rate for band 3 income',             'group' => 'paye',    'created_at' => $now, 'updated_at' => $now],
            ['key' => 'paye_band4_rate', 'label' => 'PAYE Band 4 Rate (%)',                'value' => '37.5',  'description' => 'Tax rate for income above band 3',       'group' => 'paye',    'created_at' => $now, 'updated_at' => $now],
            // NAPSA
            ['key' => 'napsa_rate',      'label' => 'NAPSA Employee Contribution (%)',     'value' => '5',     'description' => 'Employee share of NAPSA contribution',   'group' => 'napsa',   'created_at' => $now, 'updated_at' => $now],
            ['key' => 'napsa_cap',       'label' => 'NAPSA Monthly Cap (ZMW)',             'value' => '1073',  'description' => 'Maximum monthly NAPSA deduction',        'group' => 'napsa',   'created_at' => $now, 'updated_at' => $now],
            // NHIMA
            ['key' => 'nhima_rate',      'label' => 'NHIMA Contribution Rate (%)',         'value' => '1',     'description' => 'Employee share of NHIMA contribution',   'group' => 'nhima',   'created_at' => $now, 'updated_at' => $now],
            ['key' => 'nhima_cap',       'label' => 'NHIMA Monthly Cap (ZMW, 0=no cap)',   'value' => '0',     'description' => 'Maximum monthly NHIMA deduction (0=no cap)', 'group' => 'nhima', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_configurations');
    }
};
