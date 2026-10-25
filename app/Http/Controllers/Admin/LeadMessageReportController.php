<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeadMessageReport;

// Admin review for reports filed on the NC<->client chat channel (see
// LeadMessageReport's own class comment) — mirrors NikahSafetyController's
// adminReports()/dismissReport()/reportConversation() for the equivalent
// member-to-member reports, kept as its own small controller since the
// underlying model is a deliberate sibling, not a shared one. Admin-visible
// only — no suspend/automated-consequence action, per the project owner's
// decision.
class LeadMessageReportController extends Controller
{
    public function index()
    {
        $reports = LeadMessageReport::with(['lead', 'reporter', 'reported'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);

        return view('admin.leads.message-reports', compact('reports'));
    }

    public function dismiss(LeadMessageReport $report)
    {
        $report->update(['status' => 'dismissed']);

        return back()->with('status', 'Report dismissed.');
    }

    public function conversation(LeadMessageReport $report)
    {
        $messages = $report->lead->clientMessages()->with('sender')->orderBy('created_at')->get();

        return view('admin.leads.message-report-conversation', compact('report', 'messages'));
    }
}
