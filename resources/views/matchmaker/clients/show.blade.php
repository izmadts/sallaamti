<x-matchmaker-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('matchmaker.clients.index') }}" class="text-gray-400 hover:text-gray-600">My Clients</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">{{ $lead->name }}</span>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6" x-data="{ tab: 'overview' }">

        @if (session('status'))
        <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
        @endif
        @if (session('error'))
        <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Header card --}}
        <div class="bg-white rounded-xl shadow-sm p-6 flex flex-wrap justify-between items-start gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $lead->name }}</h2>
                <p class="text-sm text-gray-500">{{ $lead->maskedPhone() ?: '— no phone —' }} · {{ $lead->email ?: '— no email —' }}</p>
                <span class="inline-block mt-2 text-xs px-2 py-0.5 rounded-full
                    {{ match($lead->status) {
                        'new' => 'bg-blue-100 text-blue-800',
                        'contacted' => 'bg-amber-100 text-amber-800',
                        'interested' => 'bg-purple-100 text-purple-800',
                        'registered' => 'bg-green-100 text-green-800',
                        default => 'bg-gray-100 text-gray-600',
                    } }}">
                    {{ ucfirst(str_replace('_', ' ', $lead->status)) }}
                </span>
            </div>
            <div class="flex gap-2">
                @if ($lead->isConverted())
                <a href="{{ route(auth()->user()->can('nikah.view') ? 'admin.nikah.show' : 'matchmaker.nikah.show', $lead->nikah_profile_id) }}" class="text-sm font-semibold px-3 py-2 rounded-lg text-white hover:opacity-90" style="background: var(--mm-plum);">View Nikah Profile →</a>
                @else
                <form method="POST" action="{{ route('matchmaker.clients.convert', $lead) }}">
                    @csrf
                    <button class="text-sm font-semibold px-3 py-2 rounded-lg text-white hover:opacity-90" style="background: #be185d"
                        onclick="return confirm('Start registering {{ $lead->name }} as a real Sallaamti account? You\'ll continue in the walk-in wizard with their details pre-filled.')">
                        Convert to Registered Profile →
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Tabs --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="flex flex-wrap border-b overflow-x-auto">
                <button @click="tab = 'overview'" :class="tab === 'overview' ? 'border-b-2 font-semibold' : 'text-gray-500'" style="border-color: var(--mm-plum);" class="px-4 py-3 text-sm whitespace-nowrap">Overview</button>
                <button @click="tab = 'requirements'" :class="tab === 'requirements' ? 'border-b-2 font-semibold' : 'text-gray-500'" style="border-color: var(--mm-plum);" class="px-4 py-3 text-sm whitespace-nowrap">Requirements</button>
                <button @click="tab = 'shortlist'" :class="tab === 'shortlist' ? 'border-b-2 font-semibold' : 'text-gray-500'" style="border-color: var(--mm-plum);" class="px-4 py-3 text-sm whitespace-nowrap">Shortlist ({{ $lead->shortlistItems->count() }})</button>
                <button @click="tab = 'batches'" :class="tab === 'batches' ? 'border-b-2 font-semibold' : 'text-gray-500'" style="border-color: var(--mm-plum);" class="px-4 py-3 text-sm whitespace-nowrap">Proposal Batches ({{ $lead->proposalBatches->count() }})</button>
                <button @click="tab = 'timeline'" :class="tab === 'timeline' ? 'border-b-2 font-semibold' : 'text-gray-500'" style="border-color: var(--mm-plum);" class="px-4 py-3 text-sm whitespace-nowrap">Timeline ({{ $lead->timelineEvents->count() }})</button>
            </div>

            {{-- === OVERVIEW === --}}
            <div x-show="tab === 'overview'" class="p-6 space-y-4">
                @unless ($lead->isConverted())
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
                    Not registered yet. Already have a profile for them (started separately)?
                    <form method="POST" action="{{ route('matchmaker.clients.link-profile', $lead) }}" class="inline-flex items-center gap-2 mt-1">
                        @csrf
                        <input type="number" name="nikah_profile_id" placeholder="Profile ID" class="border-gray-300 rounded text-xs w-24 py-1" required>
                        <button class="text-xs text-white px-2 py-1 rounded" style="background: var(--mm-plum);">Link it</button>
                    </form>
                </div>
                @endunless

                <form method="POST" action="{{ route('matchmaker.clients.update', $lead) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="name" value="Name" />
                            <x-text-input id="name" name="name" type="text" class="w-full mt-1" :value="$lead->name" required />
                        </div>
                        <div>
                            <x-input-label for="status" value="Status" />
                            <select id="status" name="status" required class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                @foreach (['new' => 'New', 'contacted' => 'Contacted', 'interested' => 'Interested', 'registered' => 'Registered', 'not_interested' => 'Not Interested', 'closed' => 'Closed'] as $value => $label)
                                <option value="{{ $value }}" {{ $lead->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if ($canManageTeam)
                    <div>
                        <x-input-label for="assigned_to" value="Assigned To" />
                        <select id="assigned_to" name="assigned_to" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                            @foreach ($matchmakers as $mm)
                            <option value="{{ $mm->id }}" {{ $lead->assigned_to === $mm->id ? 'selected' : '' }}>{{ $mm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="phone" value="Phone / WhatsApp" />
                            <x-text-input id="phone" name="phone" type="text" class="w-full mt-1" value="" placeholder="{{ $lead->maskedPhone() ?: 'Not on file yet' }} — leave blank to keep" />
                            <p class="text-xs text-gray-400 mt-1">Hidden for privacy. Type a new number here only to correct it — use Send via WhatsApp/SMS below to actually reach {{ $lead->name }}.</p>
                        </div>
                        <div>
                            <x-input-label for="email" value="Email" />
                            <x-text-input id="email" name="email" type="email" class="w-full mt-1" :value="$lead->email" />
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="gender" value="Gender" />
                            <select id="gender" name="gender" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                <option value="">Unknown</option>
                                <option value="male" {{ $lead->gender === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ $lead->gender === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="source" value="Source" />
                            <select id="source" name="source" required class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                @foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'whatsapp' => 'WhatsApp', 'website' => 'Website', 'phone' => 'Phone', 'referral' => 'Referral', 'manual' => 'Manual', 'other' => 'Other'] as $value => $label)
                                <option value="{{ $value }}" {{ $lead->source === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="next_follow_up_at" value="Next Follow-up" />
                            <x-text-input id="next_follow_up_at" name="next_follow_up_at" type="date" class="w-full mt-1" :value="$lead->next_follow_up_at?->toDateString()" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="notes" value="Notes" />
                        <textarea id="notes" name="notes" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1">{{ $lead->notes }}</textarea>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <p class="text-xs text-gray-400">Added {{ $lead->created_at->format('d M Y') }} by {{ $lead->createdBy?->name ?? '—' }}</p>
                        <button class="text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:opacity-90 hover:-translate-y-0.5 transition shadow-sm" style="background: var(--mm-plum);">Save Changes</button>
                    </div>
                </form>

                @if ($lead->nikah_profile_id)
                <div class="mt-6 pt-6 border-t">
                    @include('matchmaker.nikah._payment-form', ['profile' => $lead->nikahProfile])
                </div>

                @php $pendingReceivedInterests = $lead->nikahProfile->receivedInterests()->where('status', 'pending')->with('sender.user')->latest()->get(); @endphp
                @if ($pendingReceivedInterests->isNotEmpty())
                <div class="mt-6 pt-6 border-t">
                    @include('matchmaker.nikah._interest-inbox', ['interests' => $pendingReceivedInterests])
                </div>
                @endif
                @endif

                {{-- Progress page link — a standing link the client can revisit to see their own status/timeline/proposals, gated by their WhatsApp number's last 7 digits each visit --}}
                <div class="mt-6 pt-6 border-t">
                    <h4 class="font-semibold text-gray-700 mb-1">🔗 Client Progress Page</h4>
                    <p class="text-xs text-gray-500 mb-3">A standing link {{ $lead->name }} can revisit any time to see their status, timeline, and proposal history. They'll enter the last 7 digits of the phone number above to unlock it — every time, nothing is remembered.</p>

                    @php
                        $progressLink = \App\Http\Controllers\Matchmaker\ClientController::progressLink($lead);
                        $initials = collect(preg_split('/\s+/', trim($lead->name)))->filter()->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->take(2)->implode('');
                        $linkTitle = $initials . ' — Link Generated by ' . ($lead->assignedTo->name ?? auth()->user()->name);
                    @endphp
                    @if ($progressLink)
                    <p class="text-xs font-semibold text-gray-600 mb-2">🔗 {{ $linkTitle }}</p>
                    <p class="text-xs text-gray-400 mb-2">The link itself isn't shown here — use the actions below. Only admin can see the raw link.</p>
                    @endif
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($progressLink)
                        <button type="button" data-link="{{ $progressLink }}" onclick="navigator.clipboard.writeText(this.dataset.link); this.textContent = '✅ Copied!'; setTimeout(() => this.textContent = '📋 Copy Link', 1500);" class="text-xs font-semibold px-3 py-1.5 rounded-lg text-white hover:opacity-90" style="background: var(--mm-plum);">📋 Copy Link</button>
                        <a href="{{ route('matchmaker.clients.progress-link.send', [$lead, 'whatsapp']) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg text-white hover:opacity-90 bg-green-600">💬 Send via WhatsApp</a>
                        <a href="{{ route('matchmaker.clients.progress-link.send', [$lead, 'sms']) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg text-white hover:opacity-90 bg-blue-500">✉️ Send via SMS</a>
                        @endif
                        <form method="POST" action="{{ route('matchmaker.clients.progress-link.regenerate', $lead) }}" @if($progressLink) onsubmit="return confirm('Generate a new progress link? The old one will stop working immediately.')" @endif>
                            @csrf
                            <button class="text-xs font-semibold px-2 py-1.5 rounded-lg border" style="border-color: var(--mm-plum); color: var(--mm-plum);">{{ $progressLink ? '↻ Regenerate' : '+ Generate Link' }}</button>
                        </form>
                    </div>
                    @unless ($lead->phone)
                    <p class="text-xs text-amber-700 mt-2">Add a phone number above first — that's what {{ $lead->name }} will enter to unlock the page.</p>
                    @endunless
                </div>

                {{-- Consent — see App\Models\MatchmakingConsent. Matchmaking Participation gates sending proposal batches. --}}
                <div class="mt-6 pt-6 border-t">
                    <h4 class="font-semibold text-gray-700 mb-1">✅ Consent</h4>
                    <p class="text-xs text-gray-500 mb-3">Best way: ask them to confirm it themselves through their secure link — the system asks them directly, no guesswork about what they actually agreed to. If that's not possible, you can still record consent you got verbally, by phone, or in person. An active <strong>Matchmaking Participation</strong> consent is required before you can send this client any proposals.</p>

                    @if ($lead->consentRequests->where('status', 'pending')->isNotEmpty())
                    <div class="space-y-1.5 mb-3">
                        @foreach ($lead->consentRequests->where('status', 'pending') as $req)
                        <div class="text-xs bg-blue-50 text-blue-800 rounded-lg px-3 py-2">
                            ⏳ Waiting on {{ $lead->name }} to confirm: <strong>{{ explode(' — ', \App\Models\MatchmakingConsent::TYPES[$req->consent_type])[0] }}</strong>
                            <span class="text-blue-500"> · requested {{ $req->requested_at->diffForHumans() }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if ($lead->consents->isNotEmpty())
                    <div class="space-y-1.5 mb-3">
                        @foreach ($lead->consents as $consent)
                        <div class="flex items-center justify-between text-xs bg-gray-50 rounded-lg px-3 py-2">
                            <div>
                                <span class="font-medium {{ $consent->isActive() ? 'text-gray-800' : 'text-gray-400 line-through' }}">{{ explode(' — ', \App\Models\MatchmakingConsent::TYPES[$consent->consent_type])[0] }}</span>
                                <span class="text-gray-400"> · {{ \App\Models\MatchmakingConsent::METHODS[$consent->method] ?? $consent->method }} · {{ $consent->granted_at->format('d M Y') }} · by {{ $consent->recordedBy?->name ?? '—' }}</span>
                                @if (!$consent->isActive())
                                <span class="text-red-500"> · revoked {{ $consent->revoked_at->format('d M Y') }} by {{ $consent->revokedBy?->name ?? '—' }}</span>
                                @endif
                            </div>
                            @if ($consent->isActive())
                            <form method="POST" action="{{ route('matchmaker.clients.consents.revoke', [$lead, $consent]) }}" onsubmit="return confirm('Revoke this consent?')">
                                @csrf
                                <button class="text-red-500 hover:underline">Revoke</button>
                            </form>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <form method="POST" action="{{ route('matchmaker.clients.consents.request', $lead) }}" class="flex flex-wrap gap-2 items-end mb-3">
                        @csrf
                        <div>
                            <label class="text-xs text-gray-500">Type</label>
                            <select name="consent_type" required class="border-gray-300 rounded text-sm block">
                                @foreach (\App\Models\MatchmakingConsent::TYPES as $value => $label)
                                <option value="{{ $value }}" title="{{ $label }}">{{ explode(' — ', $label)[0] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="text-xs font-semibold px-3 py-1.5 rounded-lg border" style="border-color: var(--mm-plum); color: var(--mm-plum);">🔗 Ask Them to Confirm via Link</button>
                        @unless ($lead->phone)
                        <span class="text-xs text-amber-700">Add a phone number above first.</span>
                        @endunless
                    </form>

                    <p class="text-xs text-gray-400 mb-2">— or, if you already got it verbally, by phone, or in person —</p>

                    <form method="POST" action="{{ route('matchmaker.clients.consents.record', $lead) }}" class="flex flex-wrap gap-2 items-end">
                        @csrf
                        <div>
                            <label class="text-xs text-gray-500">Type</label>
                            <select name="consent_type" required class="border-gray-300 rounded text-sm block">
                                @foreach (\App\Models\MatchmakingConsent::TYPES as $value => $label)
                                <option value="{{ $value }}" title="{{ $label }}">{{ explode(' — ', $label)[0] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">How obtained</label>
                            <select name="method" required class="border-gray-300 rounded text-sm block">
                                @foreach (\App\Models\MatchmakingConsent::METHODS as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 min-w-[10rem]">
                            <label class="text-xs text-gray-500">Notes (optional)</label>
                            <input type="text" name="notes" class="border-gray-300 rounded text-sm block w-full">
                        </div>
                        <button class="text-xs font-semibold px-3 py-1.5 rounded-lg text-white hover:opacity-90" style="background: var(--mm-plum);">Record Consent</button>
                    </form>
                </div>
            </div>

            {{-- === REQUIREMENTS === --}}
            <div x-show="tab === 'requirements'" class="p-6"
                x-data="{
                    items: {{ Js::from($lead->requirement?->items->map(fn($i) => ['requirement_type' => $i->requirement_type, 'requirement_value' => $i->requirement_value, 'priority' => $i->priority, 'notes' => $i->notes])->values() ?? []) }},
                    addRow() { this.items.push({ requirement_type: 'city', requirement_value: '', priority: 'preferred', notes: '' }) },
                    removeRow(i) { this.items.splice(i, 1) },
                }">
                <p class="text-sm text-gray-500 mb-4">What this client is looking for in a match — used to guide candidate search. Saving replaces the full list below.</p>

                <form method="POST" action="{{ route('matchmaker.clients.requirements.save', $lead) }}" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <template x-for="(item, i) in items" :key="i">
                            <div class="flex flex-wrap items-start gap-2 bg-gray-50 rounded-lg p-3">
                                <select :name="'items[' + i + '][requirement_type]'" x-model="item.requirement_type" class="border-gray-300 rounded text-sm">
                                    @foreach (\App\Models\MatchmakingRequirementItem::TYPES as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <input type="text" :name="'items[' + i + '][requirement_value]'" x-model="item.requirement_value" placeholder="Value (e.g. Lahore, 25-32 yrs)" class="border-gray-300 rounded text-sm flex-1 min-w-[10rem]" required>
                                <select :name="'items[' + i + '][priority]'" x-model="item.priority" class="border-gray-300 rounded text-sm">
                                    <option value="must_have">Must Have</option>
                                    <option value="preferred">Preferred</option>
                                    <option value="flexible">Flexible</option>
                                </select>
                                <input type="text" :name="'items[' + i + '][notes]'" x-model="item.notes" placeholder="Notes (optional)" class="border-gray-300 rounded text-sm flex-1 min-w-[8rem]">
                                <button type="button" @click="removeRow(i)" class="text-red-500 text-xs px-2 py-1.5">Remove</button>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addRow()" class="text-sm font-semibold px-3 py-1.5 rounded-lg border" style="border-color: var(--mm-plum); color: var(--mm-plum);">+ Add Requirement</button>

                    <div>
                        <x-input-label for="req_notes" value="General Notes" />
                        <textarea id="req_notes" name="notes" rows="2" class="border-gray-300 rounded-md shadow-sm w-full mt-1">{{ $lead->requirement?->notes }}</textarea>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button class="text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:opacity-90 hover:-translate-y-0.5 transition shadow-sm" style="background: var(--mm-plum);">Save Requirements</button>
                    </div>
                </form>
            </div>

            {{-- === SHORTLIST === --}}
            <div x-show="tab === 'shortlist'" class="p-6">

                {{-- Suggested matches — ranked against the saved Requirements, see App\Services\Matchmaking\CompatibilityScorer --}}
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">🎯 Suggested Matches</h4>
                    @if (!$lead->requirement || $lead->requirement->items->isEmpty())
                    <p class="text-sm text-gray-400">Save <a href="#" @click.prevent="tab = 'requirements'" class="hover:underline" style="color: var(--mm-plum);">Requirements</a> first — suggestions are ranked against what's saved there.</p>
                    @elseif ($suggestions->isEmpty())
                    <p class="text-sm text-gray-400">No strong matches against the saved requirements right now — try widening them, or search manually below.</p>
                    @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ($suggestions as $row)
                        @php $profile = $row['profile']; $result = $row['result']; @endphp
                        <div class="border border-gray-100 rounded-lg p-3">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-medium text-gray-800">{{ $profile->user?->name ?? 'Deleted account' }}</p>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full shrink-0 whitespace-nowrap
                                    {{ $result['score'] >= 70 ? 'bg-green-100 text-green-800' : ($result['score'] >= 40 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600') }}">
                                    {{ $result['score'] >= 70 ? '🟢 High' : ($result['score'] >= 40 ? '🟡 Medium' : '⚪ Low') }} · {{ $result['score'] }}%
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">{{ $profile->user?->gender ? ucfirst($profile->user->gender) . ', ' : '' }}{{ $profile->age }} yrs, {{ $profile->city }} @if($profile->sect) · {{ $profile->sect }} @endif</p>
                            @if ($result['matched']->isNotEmpty())
                            <p class="text-xs text-green-700 mt-1">✓ Matches: {{ $result['matched']->pluck('requirement_type')->map(fn($t) => \App\Models\MatchmakingRequirementItem::TYPES[$t] ?? $t)->implode(', ') }}</p>
                            @endif
                            <form method="POST" action="{{ route('matchmaker.clients.shortlist.add', $lead) }}" class="mt-2">
                                @csrf
                                <input type="hidden" name="nikah_profile_id" value="{{ $profile->id }}">
                                <button class="text-xs text-white px-2 py-1 rounded" style="background: var(--mm-plum);">+ Add to Shortlist</button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                @if ($lead->shortlistItems->isEmpty())
                <p class="text-sm text-gray-400 mb-4">Nothing shortlisted yet — search below to add candidates.</p>
                @else
                <div class="space-y-2 mb-6">
                    @foreach ($lead->shortlistItems as $item)
                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                        <div>
                            <p class="font-medium text-gray-800">
                                <a href="{{ route('matchmaker.nikah.show', $item->nikah_profile_id) }}" class="hover:underline">{{ $item->nikahProfile->user?->name ?? 'Deleted account' }}</a>
                                <span class="text-xs text-gray-400 ml-1">{{ $item->nikahProfile->age }} yrs, {{ $item->nikahProfile->city }}</span>
                            </p>
                            @if ($item->note)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $item->note }}</p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('matchmaker.clients.shortlist.remove', [$lead, $item]) }}" onsubmit="return confirm('Remove from shortlist?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs text-red-500 px-2 py-1">Remove</button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif

                <details class="mt-2" {{ request()->hasAny(['search_city', 'search_gender', 'search_sect']) ? 'open' : '' }}>
                    <summary class="text-sm font-medium cursor-pointer" style="color: var(--mm-plum);">+ Search verified profiles to add</summary>
                    <form method="GET" action="{{ route('matchmaker.clients.show', $lead) }}#shortlist" class="flex flex-wrap gap-3 items-end mt-3 mb-3">
                        <div>
                            <label class="text-xs text-gray-500">Gender</label>
                            <select name="search_gender" class="border-gray-300 rounded text-sm block">
                                <option value="">Any</option>
                                <option value="male" {{ request('search_gender') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ request('search_gender') === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">City</label>
                            <input type="text" name="search_city" value="{{ request('search_city') }}" class="border-gray-300 rounded text-sm block w-40">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Sect</label>
                            <input type="text" name="search_sect" value="{{ request('search_sect') }}" class="border-gray-300 rounded text-sm block w-40">
                        </div>
                        <button class="text-white text-sm px-4 py-2 rounded" style="background: var(--mm-plum-dark);">Search</button>
                    </form>

                    @if ($searchResults->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ($searchResults as $profile)
                        <div class="flex items-center justify-between border border-gray-100 rounded-lg p-3">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $profile->user?->name ?? 'Deleted account' }}</p>
                                <p class="text-xs text-gray-500">{{ $profile->age }} yrs, {{ $profile->city }} @if($profile->sect) · {{ $profile->sect }} @endif</p>
                            </div>
                            <form method="POST" action="{{ route('matchmaker.clients.shortlist.add', $lead) }}">
                                @csrf
                                <input type="hidden" name="nikah_profile_id" value="{{ $profile->id }}">
                                <button class="text-xs text-white px-2 py-1 rounded" style="background: var(--mm-plum);">+ Add</button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    @elseif (request()->hasAny(['search_city', 'search_gender', 'search_sect']))
                    <p class="text-sm text-gray-400">No matching profiles found.</p>
                    @endif
                </details>
            </div>

            {{-- === PROPOSAL BATCHES === --}}
            <div x-show="tab === 'batches'" class="p-6 space-y-6">
                @if ($lead->nikah_package_id)
                <div class="text-xs px-3 py-2 rounded-lg {{ $lead->packageExpired() ? 'bg-red-50 text-red-700' : 'bg-gray-50 text-gray-600' }}">
                    📦 {{ $lead->nikahPackage->name }}
                    @if ($lead->packageExpired())
                    — <strong>expired</strong> {{ $lead->package_expires_at->format('d M Y') }}
                    @elseif ($lead->package_expires_at)
                    — active until {{ $lead->package_expires_at->format('d M Y') }}
                    @endif
                    @if ($lead->nikahPackage->proposal_limit)
                    · {{ $lead->remainingProposalAllowance() }} of {{ $lead->nikahPackage->proposal_limit }} proposals remaining
                    @endif
                </div>
                @endif

                @unless ($lead->nikah_profile_id)
                <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3">Link or register this client's Nikah profile before you can send them a proposal batch.</p>
                @elseif (!$lead->hasActiveConsent('matchmaking_participation'))
                <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3">Record this client's <strong>Matchmaking Participation</strong> consent (see the Consent section on Overview) before you can send them a proposal batch.</p>
                @else
                <form method="POST" action="{{ route('matchmaker.clients.batches.create', $lead) }}">
                    @csrf
                    <button class="text-sm font-semibold px-4 py-2.5 rounded-lg text-white hover:opacity-90 hover:-translate-y-0.5 transition shadow-sm" style="background: var(--mm-plum);">➕ Start New Proposal Batch</button>
                </form>
                @endunless

                @forelse ($lead->proposalBatches as $batch)
                <div class="border border-gray-100 rounded-xl overflow-hidden">
                    <div class="flex flex-wrap justify-between items-center gap-2 px-4 py-3 bg-gray-50">
                        <div>
                            <span class="font-semibold text-gray-800">Batch #{{ $batch->batch_number }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full ml-2
                                {{ match($batch->status) {
                                    'draft' => 'bg-gray-100 text-gray-600',
                                    'ready' => 'bg-blue-100 text-blue-800',
                                    'sent' => 'bg-amber-100 text-amber-800',
                                    'partially_responded' => 'bg-purple-100 text-purple-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'expired' => 'bg-gray-100 text-gray-500',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-600',
                                } }}">
                                {{ ucfirst(str_replace('_', ' ', $batch->status)) }}
                            </span>
                        </div>
                        @if ($batch->status === 'draft' && $batch->proposals->isNotEmpty())
                        <form method="POST" action="{{ route('matchmaker.clients.batches.send', [$lead, $batch]) }}" onsubmit="return confirm('Mark this batch as sent? You\'ll get a link per candidate to copy and send to {{ $lead->name }} yourself.')">
                            @csrf
                            <button class="text-xs font-semibold px-3 py-1.5 rounded-lg text-white hover:opacity-90" style="background: var(--mm-plum-dark);">Mark as Sent →</button>
                        </form>
                        @endif
                    </div>

                    <div class="p-4 space-y-3">
                        @forelse ($batch->proposals as $proposal)
                        <div class="flex flex-wrap items-start justify-between gap-3 border-b last:border-0 pb-3 last:pb-0">
                            <div class="flex-1 min-w-[12rem]">
                                <p class="font-medium text-gray-800 text-sm">{{ $proposal->candidate->user?->name ?? 'Deleted account' }}</p>
                                <p class="text-xs text-gray-400">{{ $proposal->candidate->age }} yrs, {{ $proposal->candidate->city }}</p>
                                @if ($proposal->match_reasons)
                                <p class="text-xs text-gray-500 mt-1">{{ implode(' · ', $proposal->match_reasons) }}</p>
                                @endif
                                <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full
                                    {{ match($proposal->response) {
                                        'interested' => 'bg-green-100 text-green-800',
                                        'not_interested' => 'bg-red-100 text-red-700',
                                        'maybe' => 'bg-amber-100 text-amber-800',
                                        'need_more_information' => 'bg-blue-100 text-blue-800',
                                        default => 'bg-gray-100 text-gray-600',
                                    } }}">
                                    {{ $proposal->response ? ucfirst(str_replace('_', ' ', $proposal->response)) : ucfirst($proposal->status) }}
                                </span>
                                @if ($proposal->nikahInterest)
                                <span class="inline-block mt-1 ml-1 text-xs px-2 py-0.5 rounded-full
                                    {{ match($proposal->nikahInterest->status) {
                                        'accepted' => 'bg-pink-100 text-pink-800',
                                        'declined' => 'bg-red-100 text-red-700',
                                        default => 'bg-blue-100 text-blue-800',
                                    } }}">
                                    {{ match($proposal->nikahInterest->status) {
                                        'accepted' => '💞 Mutual Interest!',
                                        'declined' => 'Candidate Declined',
                                        default => 'Awaiting Candidate',
                                    } }}
                                </span>
                                @endif
                            </div>

                            @if ($batch->status === 'draft')
                            <form method="POST" action="{{ route('matchmaker.clients.batches.proposals.remove', [$lead, $batch, $proposal]) }}">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs text-red-500 px-2 py-1">Remove</button>
                            </form>
                            @elseif ($proposal->sent_at)
                            @php $link = \App\Http\Controllers\Matchmaker\ClientController::proposalLink($proposal); @endphp
                            <div class="flex items-center gap-2 flex-wrap justify-end">
                                <input type="text" readonly value="{{ $link }}" class="text-xs border-gray-200 rounded-lg w-56 bg-gray-50" onclick="this.select()" id="link-{{ $proposal->id }}">
                                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('link-{{ $proposal->id }}').value); this.textContent = 'Copied!'; setTimeout(() => this.textContent = 'Copy Link', 1500);" class="text-xs font-semibold px-2 py-1.5 rounded-lg text-white hover:opacity-90" style="background: var(--mm-plum);">Copy Link</button>
                                @if ($proposal->status !== 'responded')
                                <form method="POST" action="{{ route('matchmaker.clients.batches.proposals.regenerate-link', [$lead, $batch, $proposal]) }}" onsubmit="return confirm('Generate a new link for this candidate? The old link will stop working immediately, even if it was already sent.')">
                                    @csrf
                                    <button class="text-xs font-semibold px-2 py-1.5 rounded-lg border" style="border-color: var(--mm-plum); color: var(--mm-plum);">↻ Regenerate</button>
                                </form>
                                @endif
                            </div>
                            @endif
                        </div>
                        @empty
                        <p class="text-sm text-gray-400">No candidates added yet.</p>
                        @endforelse

                        @if ($batch->status === 'draft')
                        <form method="POST" action="{{ route('matchmaker.clients.batches.proposals.add', [$lead, $batch]) }}" class="flex flex-wrap gap-2 items-end pt-2">
                            @csrf
                            <div class="flex-1 min-w-[12rem]">
                                <label class="text-xs text-gray-500">Add from shortlist</label>
                                <select name="candidate_profile_id" required class="border-gray-300 rounded text-sm w-full">
                                    <option value="">Choose a candidate…</option>
                                    @foreach ($lead->shortlistItems as $item)
                                    @unless ($batch->proposals->pluck('candidate_profile_id')->contains($item->nikah_profile_id))
                                    <option value="{{ $item->nikah_profile_id }}">{{ $item->nikahProfile->user?->name ?? 'Deleted account' }} — {{ $item->nikahProfile->age }} yrs, {{ $item->nikahProfile->city }}</option>
                                    @endunless
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 min-w-[12rem]">
                                <label class="text-xs text-gray-500">Why this match (optional)</label>
                                <input type="text" name="match_reasons[]" class="border-gray-300 rounded text-sm w-full" placeholder="Same city, similar age…">
                            </div>
                            <button class="text-xs font-semibold px-3 py-1.5 rounded-lg text-white hover:opacity-90" style="background: var(--mm-plum);">+ Add Candidate</button>
                        </form>
                        @if ($lead->shortlistItems->isEmpty())
                        <p class="text-xs text-gray-400">Shortlist a profile first (see the Shortlist tab) — batches are built from shortlisted candidates.</p>
                        @endif
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400">No proposal batches yet.</p>
                @endforelse
            </div>

            {{-- === TIMELINE === --}}
            <div x-show="tab === 'timeline'" class="p-6">
                @forelse ($lead->timelineEvents as $event)
                <div class="flex gap-3 py-3 border-b last:border-0">
                    <span class="text-lg leading-none">{{ match(true) {
                        str_contains($event->event_type, 'proposal') => '💌',
                        str_contains($event->event_type, 'shortlist') || str_contains($event->event_type, 'shared') => '⭐',
                        str_contains($event->event_type, 'requirement') => '📋',
                        str_contains($event->event_type, 'consent') => '✅',
                        str_contains($event->event_type, 'package') => '💳',
                        str_contains($event->event_type, 'registration') || str_contains($event->event_type, 'profile') => '📝',
                        str_contains($event->event_type, 'status') || str_contains($event->event_type, 'reassigned') => '🔄',
                        default => '🕓',
                    } }}</span>
                    <div>
                        <p class="text-sm text-gray-800">{{ $event->description }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $event->created_at->format('d M Y, g:i A') }} @if($event->matchmaker) · {{ $event->matchmaker->name }} @endif</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400">No activity logged yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-matchmaker-layout>
