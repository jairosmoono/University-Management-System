<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_notification_logs', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('body');
            $table->json('sent_user_ids')->nullable()->after('sent_count');
            $table->json('failed_user_ids')->nullable()->after('sent_user_ids');
        });
    }

    public function down(): void
    {
        Schema::table('email_notification_logs', function (Blueprint $table) {
            $table->dropColumn(['attachments', 'sent_user_ids', 'failed_user_ids']);
        });
    }
};
