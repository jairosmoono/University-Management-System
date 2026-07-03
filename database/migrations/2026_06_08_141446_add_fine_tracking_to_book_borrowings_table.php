<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_borrowings', function (Blueprint $table) {
            $table->timestamp('fine_paid_at')->nullable()->after('fine_paid');
            $table->foreignId('fine_collected_by')->nullable()->constrained('users')->nullOnDelete()->after('fine_paid_at');
            $table->boolean('fine_waived')->default(false)->after('fine_collected_by');
            $table->string('fine_waive_reason')->nullable()->after('fine_waived');
        });
    }

    public function down(): void
    {
        Schema::table('book_borrowings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fine_collected_by');
            $table->dropColumn(['fine_paid_at', 'fine_waived', 'fine_waive_reason']);
        });
    }
};
