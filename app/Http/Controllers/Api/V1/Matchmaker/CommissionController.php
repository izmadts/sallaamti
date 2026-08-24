<?php

namespace App\Http\Controllers\Api\V1\Matchmaker;

use App\Http\Controllers\Controller;
use App\Models\CommissionLedgerEntry;
use Illuminate\Http\JsonResponse;

// Read-only mirror of Matchmaker\CommissionController (web) — own ledger
// entries + totals only. No approve/pay/flag actions here; those stay
// admin-only (Admin\CommissionController) and are never exposed to the app.
class CommissionController extends Controller
{
    public function index(): JsonResponse
    {
        $entries = CommissionLedgerEntry::with('nikahPackage', 'source')
            ->where('matchmaker_id', auth()->id())
            ->latest()
            ->paginate(25);

        $totals = [
            'pending' => CommissionLedgerEntry::where('matchmaker_id', auth()->id())->where('status', 'pending')->sum('commission_amount'),
            'approved' => CommissionLedgerEntry::where('matchmaker_id', auth()->id())->where('status', 'approved')->sum('commission_amount'),
            'paid' => CommissionLedgerEntry::where('matchmaker_id', auth()->id())->where('status', 'paid')->sum('commission_amount'),
        ];

        return response()->json([
            'entries' => collect($entries->items())->map(fn ($e) => [
                'id' => $e->id,
                'rule_type' => $e->rule_type,
                'package' => $e->nikahPackage?->name,
                'is_renewal' => $e->is_renewal,
                'tier_at_time' => $e->tier_at_time,
                'commission_amount' => $e->commission_amount,
                'status' => $e->status,
                'is_flagged' => $e->isFlagged(),
                'flag_reason' => $e->flag_reason,
                'notes' => $e->notes,
                'eligible_at' => $e->eligible_at?->toIso8601String(),
                'created_at' => $e->created_at->toIso8601String(),
            ]),
            'current_page' => $entries->currentPage(),
            'last_page' => $entries->lastPage(),
            'total' => $entries->total(),
            'totals' => $totals,
        ]);
    }
}
