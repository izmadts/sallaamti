<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->string('reactable_type');
            $table->unsignedBigInteger('reactable_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('ameen');
            $table->timestamps();

            // One active reaction per user per post — changing type updates
            // this row (a swap), it never stacks multiple reactions.
            $table->unique(['reactable_type', 'reactable_id', 'user_id']);
            $table->index(['reactable_type', 'reactable_id']);
        });

        // Carry existing dua_reactions ("Ameen" toggles) over as the first
        // reaction type, preserving created_at so reaction history/order
        // isn't disturbed by the migration itself.
        if (Schema::hasTable('dua_reactions')) {
            $reactions = DB::table('dua_reactions')->get();
            $now = now();

            foreach ($reactions as $reaction) {
                DB::table('reactions')->insert([
                    'reactable_type' => \App\Models\DuaRequest::class,
                    'reactable_id' => $reaction->dua_request_id,
                    'user_id' => $reaction->user_id,
                    'type' => 'ameen',
                    'created_at' => $reaction->created_at ?? $now,
                    'updated_at' => $reaction->updated_at ?? $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
