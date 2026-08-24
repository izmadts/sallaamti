<?php

namespace App\Http\Controllers\Matchmaker;

use App\Http\Controllers\Controller;
use App\Models\CommissionLedgerEntry;
use App\Models\MatchmakerApplication;
use Illuminate\Support\Facades\Auth;

// A matchmaker's own transparent view of what they've earned — the
// hiring document's "just a few clicks" requirement, mirrored from the
// admin ledger but scoped to their own entries only, no rule-management
// or approve/pay actions (those stay admin-only).
class CommissionController extends Controller
{
    public function index()
    {
        $entries = CommissionLedgerEntry::with('nikahPackage', 'source')
            ->where('matchmaker_id', Auth::id())
            ->latest()
            ->paginate(25);

        $totals = [
            'pending' => CommissionLedgerEntry::where('matchmaker_id', Auth::id())->where('status', 'pending')->sum('commission_amount'),
            'approved' => CommissionLedgerEntry::where('matchmaker_id', Auth::id())->where('status', 'approved')->sum('commission_amount'),
            'paid' => CommissionLedgerEntry::where('matchmaker_id', Auth::id())->where('status', 'paid')->sum('commission_amount'),
        ];

        $application = MatchmakerApplication::where('user_id', Auth::id())->where('status', 'certified')->first();

        return view('matchmaker.commissions.index', compact('entries', 'totals', 'application'));
    }
}
