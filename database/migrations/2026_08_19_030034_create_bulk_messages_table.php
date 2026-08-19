<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bulk_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->enum('channel', ['email', 'whatsapp']);
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->string('whatsapp_template_name')->nullable();
            // Positional {{1}}, {{2}}... values for the WhatsApp template body.
            $table->json('whatsapp_template_params')->nullable();
            // What filters were applied on the user list when this campaign
            // was composed — an admin-facing record of "who did I target",
            // not re-evaluated at send time (see bulk_message_recipients,
            // which snapshots the actual resolved list).
            $table->json('filters_snapshot')->nullable();
            $table->enum('status', ['draft', 'sending', 'completed', 'failed'])->default('draft');
            $table->unsignedInteger('recipient_count')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulk_messages');
    }
};
