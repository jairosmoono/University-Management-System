<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_depreciation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('period_label', 20);          // e.g. "2025-Q1", "2025-01"
            $table->enum('method', ['straight_line', 'declining_balance', 'manual']);
            $table->decimal('opening_value', 12, 2);
            $table->decimal('depreciation_amount', 12, 2);
            $table->decimal('closing_value', 12, 2);
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_depreciation_logs');
    }
};
