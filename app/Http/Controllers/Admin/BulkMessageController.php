<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendBulkEmailJob;
use App\Jobs\SendBulkWhatsappJob;
use App\Models\BulkMessage;
use App\Models\BulkMessageRecipient;
use App\Models\SocialAccount;
use App\Models\Subscriber;
use App\Models\User;
use App\Support\HtmlSanitizer;
use App\Support\UserFilter;
use Illuminate\Http\Request;

class BulkMessageController extends Controller
{
    // A campaign of any size beyond this is better split or sent through a
    // dedicated bulk-mail provider — see the note on the broadcast page.
    // Also keeps the preview list on one page, no pagination needed.
    private const MAX_RECIPIENTS = 500;

    // Gmail's practical safe daily volume for a regular (non-Workspace)
    // sending account. Shared across every email campaign — Users-targeted
    // and Subscribers-targeted both send through the same Gmail account, so
    // the count has to be account-wide, not per-campaign.
    private const DAILY_EMAIL_LIMIT = 500;

    // WhatsApp Business API providers cap/throttle per-account daily sends
    // too, and an unbounded blast risks getting the number itself banned —
    // this had no cap at all before (security audit finding), unlike email.
    private const DAILY_WHATSAPP_LIMIT = 500;

    // How many emails have already been queued today (any campaign) —
    // counted at dispatch time, not send time, so a campaign started late
    // in the day can't oversubscribe today's remaining quota before the
    // queue worker catches up.
    private function emailsAlreadyQueuedToday(): int
    {
        return BulkMessageRecipient::whereHas('bulkMessage', fn ($q) => $q->where('channel', 'email'))
            ->whereDate('created_at', today())
            ->count();
    }

    // Splits a campaign larger than today's remaining Gmail quota into daily
    // batches instead of trying to send it all at once — the batch beyond
    // today is delayed to 9am on however many days out it takes to fit
    // everyone in, continuing automatically as long as the queue worker
    // keeps running (no extra scheduling step needed).
    private function emailDispatchDelayFor(int $emailIndex, int $remainingToday): ?\Illuminate\Support\Carbon
    {
        if ($emailIndex < $remainingToday) {
            return null;
        }

        $overflowIndex = $emailIndex - $remainingToday;
        $batchDay = intdiv($overflowIndex, self::DAILY_EMAIL_LIMIT) + 1;

        return now()->addDays($batchDay)->setTime(9, 0);
    }

    private function whatsappsAlreadyQueuedToday(): int
    {
        return BulkMessageRecipient::whereHas('bulkMessage', fn ($q) => $q->where('channel', 'whatsapp'))
            ->whereDate('created_at', today())
            ->count();
    }

    // Same daily-batching approach as emailDispatchDelayFor() above.
    private function whatsappDispatchDelayFor(int $whatsappIndex, int $remainingToday): ?\Illuminate\Support\Carbon
    {
        if ($whatsappIndex < $remainingToday) {
            return null;
        }

        $overflowIndex = $whatsappIndex - $remainingToday;
        $batchDay = intdiv($overflowIndex, self::DAILY_WHATSAPP_LIMIT) + 1;

        return now()->addDays($batchDay)->setTime(9, 0);
    }

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
        // Global (not filter-scoped) — so the page can tell the admin
        // "nobody's opted in yet" even when that's just true everywhere,
        // rather than looking like their current filter is the problem.
        $whatsappOptedInTotal = User::where('whatsapp_notify_opt_in', true)->whereNotNull('phone')->count();

        return view('admin.users.broadcast', [
            'matching' => $matching,
            'totalMatching' => $totalMatching,
            'truncated' => $totalMatching > self::MAX_RECIPIENTS,
            'maxRecipients' => self::MAX_RECIPIENTS,
            'whatsappConnected' => $whatsappConnected,
            'emailEligible' => $emailEligible,
            'whatsappEligible' => $whatsappEligible,
            'whatsappOptedInTotal' => $whatsappOptedInTotal,
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
            'body' => ['required_if:channel,email', 'nullable', 'string', 'max:50000'],
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
            'body' => HtmlSanitizer::cleanEmailHtml($validated['body'] ?? null),
            'whatsapp_template_name' => $validated['whatsapp_template_name'] ?? null,
            'whatsapp_template_params' => array_values($validated['whatsapp_template_params'] ?? []),
            'filters_snapshot' => $request->except(['channel', 'user_ids', 'subject', 'body', 'whatsapp_template_name', 'whatsapp_template_params', '_token']),
            'status' => 'sending',
            'recipient_count' => $recipients->count(),
        ]);

        $remainingEmailToday = self::DAILY_EMAIL_LIMIT - $this->emailsAlreadyQueuedToday();
        $remainingWhatsappToday = self::DAILY_WHATSAPP_LIMIT - $this->whatsappsAlreadyQueuedToday();
        $emailIndex = 0;
        $whatsappIndex = 0;

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

            if ($validated['channel'] === 'email') {
                $delay = $this->emailDispatchDelayFor($emailIndex, $remainingEmailToday);
                $delay ? SendBulkEmailJob::dispatch($recipient)->delay($delay) : SendBulkEmailJob::dispatch($recipient);
                $emailIndex++;
            } else {
                $delay = $this->whatsappDispatchDelayFor($whatsappIndex, $remainingWhatsappToday);
                $delay ? SendBulkWhatsappJob::dispatch($recipient)->delay($delay) : SendBulkWhatsappJob::dispatch($recipient);
                $whatsappIndex++;
            }
        }

        $status = "Sending to {$bulkMessage->recipient_count} " . ($validated['channel'] === 'email' ? 'email' : 'WhatsApp') . ' recipient(s) — this page updates as deliveries complete.';
        if ($validated['channel'] === 'email' && $emailIndex > max(0, $remainingEmailToday)) {
            $status .= ' This list is larger than today\'s remaining Gmail quota, so it\'s split into daily batches automatically — the rest will keep sending over the next few days.';
        }
        if ($validated['channel'] === 'whatsapp' && $whatsappIndex > max(0, $remainingWhatsappToday)) {
            $status .= ' This list is larger than today\'s WhatsApp sending limit, so it\'s split into daily batches automatically — the rest will keep sending over the next few days.';
        }

        return redirect()->route('admin.bulk-messages.show', $bulkMessage)->with('status', $status);
    }

    // Newsletter subscribers (App\Models\Subscriber) are a separate list
    // from registered Users — people who signed up via the public /subscribe
    // form without necessarily having an account. Email-only: there's no
    // phone/WhatsApp data on a subscriber, so this skips the channel tabs
    // the User-targeted composer has.
    public function createForSubscribers(Request $request)
    {
        $query = Subscriber::whereNotNull('verified_at')->where('is_active', true);

        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        $subscribers = (clone $query)->orderBy('email')->limit(self::MAX_RECIPIENTS)->get(['id', 'email']);
        $totalMatching = $query->count();

        return view('admin.subscribers.broadcast', [
            'subscribers' => $subscribers,
            'totalMatching' => $totalMatching,
            'truncated' => $totalMatching > self::MAX_RECIPIENTS,
            'maxRecipients' => self::MAX_RECIPIENTS,
            'filters' => $request->query(),
        ]);
    }

    public function storeForSubscribers(Request $request)
    {
        $validated = $request->validate([
            'subscriber_ids' => ['required', 'array', 'min:1'],
            'subscriber_ids.*' => ['integer', 'exists:subscribers,id'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:50000'],
        ]);

        // Re-filter server-side rather than trusting the checked ids alone —
        // a subscriber could unsubscribe or lose verification between the
        // compose page loading and this submit.
        $recipients = Subscriber::whereIn('id', $validated['subscriber_ids'])
            ->whereNotNull('verified_at')
            ->where('is_active', true)
            ->get();

        if ($recipients->isEmpty()) {
            return back()->with('error', 'None of the selected subscribers are still verified and active.');
        }

        $bulkMessage = BulkMessage::create([
            'created_by' => auth()->id(),
            'recipient_type' => 'subscriber',
            'channel' => 'email',
            'subject' => $validated['subject'],
            'body' => HtmlSanitizer::cleanEmailHtml($validated['body']),
            'filters_snapshot' => $request->except(['subscriber_ids', 'subject', 'body', '_token']),
            'status' => 'sending',
            'recipient_count' => $recipients->count(),
        ]);

        $remainingToday = self::DAILY_EMAIL_LIMIT - $this->emailsAlreadyQueuedToday();
        $emailIndex = 0;

        foreach ($recipients as $subscriber) {
            $recipient = BulkMessageRecipient::create([
                'bulk_message_id' => $bulkMessage->id,
                'subscriber_id' => $subscriber->id,
                'channel_address' => $subscriber->email,
                'status' => 'pending',
            ]);

            $delay = $this->emailDispatchDelayFor($emailIndex, $remainingToday);
            $delay ? SendBulkEmailJob::dispatch($recipient)->delay($delay) : SendBulkEmailJob::dispatch($recipient);
            $emailIndex++;
        }

        $status = "Sending to {$bulkMessage->recipient_count} subscriber(s) — this page updates as deliveries complete.";
        if ($emailIndex > max(0, $remainingToday)) {
            $status .= ' This list is larger than today\'s remaining Gmail quota, so it\'s split into daily batches automatically — the rest will keep sending over the next few days.';
        }

        return redirect()->route('admin.bulk-messages.show', $bulkMessage)->with('status', $status);
    }

    public function index()
    {
        $campaigns = BulkMessage::with('createdBy')->latest()->paginate(15);

        $stats = [
            'total_campaigns' => BulkMessage::count(),
            'total_recipients' => (int) BulkMessage::sum('recipient_count'),
            'total_delivered' => (int) BulkMessage::sum('sent_count'),
            'total_failed' => (int) BulkMessage::sum('failed_count'),
            'email_queued_today' => $this->emailsAlreadyQueuedToday(),
            'email_daily_limit' => self::DAILY_EMAIL_LIMIT,
            // Whether anything is still mid-flight — a multi-day batched
            // campaign shows as 'sending' the whole time, so this is what
            // tells the admin "there's more still going out" at a glance.
            'campaigns_in_progress' => BulkMessage::where('status', 'sending')->count(),
        ];
        $stats['delivery_rate'] = ($stats['total_delivered'] + $stats['total_failed']) > 0
            ? round($stats['total_delivered'] / ($stats['total_delivered'] + $stats['total_failed']) * 100)
            : null;

        return view('admin.bulk-messages.index', compact('campaigns', 'stats'));
    }

    public function show(BulkMessage $bulkMessage)
    {
        $bulkMessage->load('createdBy');
        $recipients = $bulkMessage->recipients()->with('user', 'subscriber')->latest()->paginate(50);

        return view('admin.bulk-messages.show', compact('bulkMessage', 'recipients'));
    }
}
