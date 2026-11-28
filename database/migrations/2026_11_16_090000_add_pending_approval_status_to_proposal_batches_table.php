<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Trainee-access addendum (15/16 Nov 2026): a Probation-stage counselor can
// now build and "send" a proposal batch, but it must wait for admin/senior
// approval before the client actually sees it — see
// ClientController::sendBatch()/dispatchBatchToClient() and the new
// Admin\ProposalBatchApprovalController. 'pending_approval' sits between
// 'ready' and 'sent' in spirit; approve() moves it on to 'sent', reject()
// drops it back to 'ready'.
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            // SQLite stores the original enum as a CHECK constraint, which
            // would reject 'pending_approval' in the test suite. Rebuilding
            // the column as a plain string drops that check.
            Schema::table('proposal_batches', fn (Blueprint $table) => $table->string('status')->default('draft')->change());

            return;
        }
        DB::statement("ALTER TABLE proposal_batches MODIFY status ENUM('draft', 'ready', 'pending_approval', 'sent', 'partially_responded', 'completed', 'expired', 'cancelled') DEFAULT 'draft'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }
        DB::table('proposal_batches')->where('status', 'pending_approval')->update(['status' => 'ready']);
        DB::statement("ALTER TABLE proposal_batches MODIFY status ENUM('draft', 'ready', 'sent', 'partially_responded', 'completed', 'expired', 'cancelled') DEFAULT 'draft'");
    }
};
