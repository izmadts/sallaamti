<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bulk_messages', function (Blueprint $table) {
            $table->enum('recipient_type', ['user', 'subscriber'])->default('user')->after('created_by');
        });

        Schema::table('bulk_message_recipients', function (Blueprint $table) {
            // A campaign now targets either registered Users or newsletter
            // Subscribers (never both), so exactly one of these two is set
            // per recipient row.
            $table->foreignId('user_id')->nullable()->change();
            $table->foreignId('subscriber_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bulk_message_recipients', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subscriber_id');
            $table->foreignId('user_id')->nullable(false)->change();
        });

        Schema::table('bulk_messages', function (Blueprint $table) {
            $table->dropColumn('recipient_type');
        });
    }
};
