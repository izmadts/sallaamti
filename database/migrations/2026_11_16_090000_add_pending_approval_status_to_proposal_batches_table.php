<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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
            return; // SQLite test DB: no ENUM to widen
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
