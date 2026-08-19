<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendBulkEmailJob;
use App\Jobs\SendBulkWhatsappJob;
use App\Models\BulkMessage;
use App\Models\BulkMessageRecipient;
use App\Models\SocialAccount;
use App\Models\User;
use App\Support\UserFilter;
use Illuminate\Http\Request;

class BulkMessageController extends Controller
{
    // A campaign of any size beyond this is better split or sent through a
    // dedicated bulk-mail provider — see the note on the broadcast page.
    // Also keeps the preview list on one page, no pagination needed.
    private const MAX_RECIPIENTS = 500;

    public function create(Request $request)
    {
        $matching = UserFilter::apply(User::query(), $request)
            ->orderBy('name')
            ->limit(self::MAX_RECIPIENTS)
            ->get(['id', 'name', 'email', 'phone', 'whatsapp_notify_opt_in']);

        $totalMatching = UserFilter::apply(User::query(), $request)->count();

        $whatsappConnected = SocialAccount::active('whatsapp') !== null;
        // Mirrors emailEligible below — the checkbox list per channel should
        // only ever offer/pre-select users that channel can actually reach,
        // rather than relying on the admin to have filtered for it first.
        $emailEligible = $matching->whereNotNull('email')->where('email', '!=', '');
        $whatsappEligible = $matching->where('whatsapp_notify_opt_in', true)->whereNotNull('phone');

        return view('admin.users.broadcast', [
            'matching' => $matching,
            'totalMatching' => $totalMatching,
            'truncated' => $totalMatching > self::MAX_RECIPIENTS,
            'maxRecipients' => self::MAX_RECIPIENTS,
            'whatsappConnected' => $whatsappConnected,
            'emailEligible' => $emailEligible,
            'whatsappEligible' => $whatsappEligible,
            'filters' => $request->query(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'channel' => ['required', 'in:email,whatsapp'],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'subject' => ['required_if:channel,email', 'nullable', 'string', 'max:255'],
            'body' => ['required_if:channel,email', 'nullable', 'string', 'max:10000'],
            'whatsapp_template_name' => ['required_if:channel,whatsapp', 'nullable', 'string', 'max:255'],
            'whatsapp_template_params' => ['nullable', 'array'],
            'whatsapp_template_params.*' => ['nullable', 'string', 'max:500'],
        ]);

        $recipients = User::whereIn('id', $validated['user_ids'])->get();

        if ($validated['channel'] === 'whatsapp') {
            $recipients = $recipients->where('whatsapp_notify_opt_in', true)->whereNotNull('phone');

            if ($recipients->isEmpty()) {
                return back()->with('error', 'None of the selected users have opted in to WhatsApp notifications.');
            }
        } else {
            // Respect a prior unsubscribe click even if the admin's filter
            // still matched this user on other criteria.
            $recipients = $recipients->where('newsletter_opt_out', false);

            if ($recipients->isEmpty()) {
                return back()->with('error', 'All the selected users have unsubscribed from email newsletters.');
            }
        }

        $bulkMessage = BulkMessage::create([
            'created_by' => auth()->id(),
            'channel' => $validated['channel'],
            'subject' => $validated['subject'] ?? null,
            'body' => $validated['body'] ?? null,
            'whatsapp_template_name' => $validated['whatsapp_template_name'] ?? null,
            'whatsapp_template_params' => array_values($validated['whatsapp_template_params'] ?? []),
            'filters_snapshot' => $request->except(['channel', 'user_ids', 'subject', 'body', 'whatsapp_template_name', 'whatsapp_template_params', '_token']),
            'status' => 'sending',
            'recipient_count' => $recipients->count(),
        ]);

        foreach ($recipients as $user) {
            $address = $validated['channel'] === 'email' ? $user->email : $user->phone;

            if (!$address) {
                continue;
            }

            $recipient = BulkMessageRecipient::create([
                'bulk_message_id' => $bulkMessage->id,
                'user_id' => $user->id,
                'channel_address' => $address,
                'status' => 'pending',
            ]);

            $validated['channel'] === 'email'
                ? SendBulkEmailJob::dispatch($recipient)
                : SendBulkWhatsappJob::dispatch($recipient);
        }

        return redirect()->route('admin.bulk-messages.show', $bulkMessage)
            ->with('status', "Sending to {$bulkMessage->recipient_count} " . ($validated['channel'] === 'email' ? 'email' : 'WhatsApp') . ' recipient(s) — this page updates as deliveries complete.');
    }

    public function index()
    {
        $campaigns = BulkMessage::with('createdBy')->latest()->paginate(15);

        return view('admin.bulk-messages.index', compact('campaigns'));
    }

    public function show(BulkMessage $bulkMessage)
    {
        $bulkMessage->load('createdBy');
        $recipients = $bulkMessage->recipients()->with('user')->latest()->paginate(50);

        return view('admin.bulk-messages.show', compact('bulkMessage', 'recipients'));
    }
}
