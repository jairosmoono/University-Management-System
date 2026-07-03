<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'depreciation_rate')) {
                $table->decimal('depreciation_rate', 5, 2)->nullable()->after('status')
                    ->comment('Annual depreciation rate as percentage (declining balance method)');
            }
            if (!Schema::hasColumn('assets', 'depreciation_method')) {
                $table->enum('depreciation_method', ['straight_line', 'declining_balance'])
                    ->nullable()->after('depreciation_rate');
            }
            if (!Schema::hasColumn('assets', 'useful_life_years')) {
                $table->unsignedTinyInteger('useful_life_years')->nullable()->after('depreciation_method');
            }
            if (!Schema::hasColumn('assets', 'salvage_value')) {
                $table->decimal('salvage_value', 12, 2)->default(0)->after('useful_life_years');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $cols = ['salvage_value', 'useful_life_years', 'depreciation_method'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('assets', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
