<x-matchmaker-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('matchmaker.clients.index') }}" class="text-gray-400 hover:text-gray-600">{{ __('db.My Clients') }}</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">{{ $lead->name }}</span>
        </div>
    </x-slot>

    @php
        $journey = \App\Support\LeadJourney::forLead($lead);
        $currentStageKey = \App\Support\LeadJourney::currentStage($lead);
        $nextActionText = \App\Support\LeadJourney::nextActionText($lead);
    @endphp

    <div class="max-w-6xl mx-auto space-y-6" x-data="{ tab: '{{ \App\Support\LeadJourney::STAGES[$currentStageKey]['tab'] ?? 'overview' }}' }">

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
                <p class="text-sm text-gray-500">{{ $lead->visiblePhoneFor(auth()->user()) ?: __('db.— no phone —') }} · {{ $lead->email ?: __('db.— no email —') }}</p>
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
                <a href="{{ route(auth()->user()->can('nikah.view') ? 'admin.nikah.show' : 'matchmaker.nikah.show', $lead->nikah_profile_id) }}" class="text-sm font-semibold px-3 py-2 rounded-lg text-white hover:opacity-90" style="background: var(--mm-plum);">{{ __('db.View Nikah Profile') }} →</a>
                @else
                <form method="POST" action="{{ route('matchmaker.clients.convert', $lead) }}">
                    @csrf
                    <button class="text-sm font-semibold px-3 py-2 rounded-lg text-white hover:opacity-90" style="background: #be185d"
                        onclick="return confirm({{ Js::from(__('db.Start registering :name as a real Sallaamti account? You\'ll continue in the walk-in wizard with their details pre-filled.', ['name' => $lead->name])) }})">
                        {{ __('db.Convert to Registered Profile') }} →
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Journey stepper — same 7 steps documented in the Nikah Counselor
             Guide, made visible instead of something you have to already
             know. Clicking a step jumps straight to its tab. --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-center justify-between overflow-x-auto pb-1">
                @foreach ($journey as $i => $stage)
                <div class="flex items-center {{ $i < count($journey) - 1 ? 'flex-1' : '' }}">
                    <button type="button" @click="tab = '{{ $stage['tab'] }}'" class="flex flex-col items-center gap-1 shrink-0 group">
                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold border-2 transition
                            {{ $stage['done'] ? 'text-white' : ($stage['key'] === $currentStageKey ? 'bg-white' : 'bg-gray-50 border-gray-200 text-gray-300') }}"
                            style="{{ $stage['done'] ? 'background: var(--mm-plum); border-color: var(--mm-plum);' : ($stage['key'] === $currentStageKey ? 'border-color: var(--mm-plum); color: var(--mm-plum);' : '') }}">
                            {{ $stage['done'] ? '✓' : $stage['icon'] }}
                        </span>
                        <span class="text-[11px] font-medium text-center leading-tight w-16 {{ $stage['key'] === $currentStageKey ? 'text-gray-800' : 'text-gray-400' }} group-hover:text-gray-700">
                            {{ $stage['label'] }}
                        </span>
                    </button>
                    @if ($i < count($journey) - 1)
                    <div class="flex-1 h-0.5 mx-1 {{ $stage['done'] ? '' : 'bg-gray-100' }}" style="{{ $stage['done'] ? 'background: var(--mm-plum);' : '' }}"></div>
                    @endif
                </div>
                @endforeach
            </div>
            @if ($nextActionText)
            <div class="mt-4 pt-4 border-t flex items-center gap-2 text-sm">
                <span class="font-semibold" style="color: var(--mm-plum);">{{ __('db.Next:') }}</span>
                <span class="text-gray-600">{{ __('db.' . $nextActionText) }}</span>
            </div>
            @else
            <div class="mt-4 pt-4 border-t text-sm text-green-700">
                🎉 {{ __('db.Every step is done for this client.') }}
            </div>
            @endif
        </div>

        {{-- Tabs --}}
        <div class="bg-white rounded-xl shadow-sm">
            <div class="flex flex-wrap border-b overflow-x-auto">
                <button @click="tab = 'overview'" :class="tab === 'overview' ? 'border-b-2 font-semibold' : 'text-gray-500'" style="border-color: var(--mm-plum);" class="px-4 py-3 text-sm whitespace-nowrap">{{ __('db.Overview') }}</button>
                <button @click="tab = 'requirements'" :class="tab === 'requirements' ? 'border-b-2 font-semibold' : 'text-gray-500'" style="border-color: var(--mm-plum);" class="px-4 py-3 text-sm whitespace-nowrap">{{ __('db.Requirements') }}</button>
                <button @click="tab = 'shortlist'" :class="tab === 'shortlist' ? 'border-b-2 font-semibold' : 'text-gray-500'" style="border-color: var(--mm-plum);" class="px-4 py-3 text-sm whitespace-nowrap">{{ __('db.Shortlist (:count)', ['count' => $lead->shortlistItems->count()]) }}</button>
                <button @click="tab = 'consent'" :class="tab === 'consent' ? 'border-b-2 font-semibold' : 'text-gray-500'" style="border-color: var(--mm-plum);" class="px-4 py-3 text-sm whitespace-nowrap">{{ __('db.Consent') }}</button>
                <button @click="tab = 'batches'" :class="tab === 'batches' ? 'border-b-2 font-semibold' : 'text-gray-500'" style="border-color: var(--mm-plum);" class="px-4 py-3 text-sm whitespace-nowrap">{{ __('db.Proposals (:count)', ['count' => $lead->proposalBatches->count()]) }}</button>
                <button @click="tab = 'timeline'" :class="tab === 'timeline' ? 'border-b-2 font-semibold' : 'text-gray-500'" style="border-color: var(--mm-plum);" class="px-4 py-3 text-sm whitespace-nowrap">{{ __('db.Timeline (:count)', ['count' => $lead->timelineEvents->count()]) }}</button>
            </div>

            {{-- === OVERVIEW === --}}
            <div x-show="tab === 'overview'" class="p-6 space-y-4">
                @unless ($lead->isConverted())
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
                    {{ __('db.Not registered yet. Already have a profile for them (started separately)?') }}
                    <form method="POST" action="{{ route('matchmaker.clients.link-profile', $lead) }}" class="inline-flex items-center gap-2 mt-1">
                        @csrf
                        <input type="number" name="nikah_profile_id" placeholder="{{ __('db.Profile ID') }}" class="border-gray-300 rounded text-xs w-24 py-1" required>
                        <button class="text-xs text-white px-2 py-1 rounded" style="background: var(--mm-plum);">{{ __('db.Link it') }}</button>
                    </form>
                </div>
                @endunless

                <form method="POST" action="{{ route('matchmaker.clients.update', $lead) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="name" :value="__('db.Name')" />
                            <x-text-input id="name" name="name" type="text" class="w-full mt-1" :value="$lead->name" required />
                        </div>
                        <div>
                            <x-input-label for="status" :value="__('db.Status')" />
                            <select id="status" name="status" required class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                @foreach (['new' => __('db.New'), 'contacted' => __('db.Contacted'), 'interested' => __('db.Interested'), 'registered' => __('db.Registered'), 'not_interested' => __('db.Not Interested'), 'closed' => __('db.Closed')] as $value => $label)
                                <option value="{{ $value }}" {{ $lead->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if ($canManageTeam)
                    <div>
                        <x-input-label for="assigned_to" :value="__('db.Assigned To')" />
                        <select id="assigned_to" name="assigned_to" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                            @foreach ($matchmakers as $mm)
                            <option value="{{ $mm->id }}" {{ $lead->assigned_to === $mm->id ? 'selected' : '' }}>{{ $mm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4">
                        <div x-data="{ editingPhone: false }">
                            <x-input-label for="phone" :value="__('db.Phone / WhatsApp')" />
                            @if ($lead->created_by === auth()->id() && $lead->phone)
                            <div x-show="!editingPhone" class="flex items-center gap-2 mt-1">
                                <span class="text-sm font-medium text-gray-800 border border-gray-200 rounded-md px-3 py-2 flex-1 bg-gray-50">{{ $lead->phone }}</span>
                                <button type="button" @click="editingPhone = true" class="text-xs font-semibold px-2 py-2 whitespace-nowrap" style="color: var(--mm-plum);">{{ __('db.Edit') }}</button>
                            </div>
                            <div x-show="editingPhone" x-cloak>
                                <x-text-input id="phone" name="phone" type="text" class="w-full mt-1" value="" placeholder="{{ __('db.leave blank to keep') }} {{ $lead->phone }}" />
                            </div>
                            <p class="text-xs text-gray-400 mt-1">{{ __("db.You entered this number yourself when adding :name, so it's shown here — it stays hidden from every other counselor.", ['name' => $lead->name]) }}</p>
                            @else
                            <x-text-input id="phone" name="phone" type="text" class="w-full mt-1" value="" placeholder="{{ $lead->maskedPhone() ?: __('db.Not on file yet') }} — {{ __('db.leave blank to keep') }}" />
                            <p class="text-xs text-gray-400 mt-1">{{ __('db.Hidden for privacy. Type a new number here only to correct it — use Send via WhatsApp/SMS below to actually reach :name.', ['name' => $lead->name]) }}</p>
                            @endif
                        </div>
                        <div>
                            <x-input-label for="email" :value="__('db.Email')" />
                            <x-text-input id="email" name="email" type="email" class="w-full mt-1" :value="$lead->email" />
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="gender" :value="__('db.Gender')" />
                            <select id="gender" name="gender" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                <option value="">{{ __('db.Unknown') }}</option>
                                <option value="male" {{ $lead->gender === 'male' ? 'selected' : '' }}>{{ __('db.Male') }}</option>
                                <option value="female" {{ $lead->gender === 'female' ? 'selected' : '' }}>{{ __('db.Female') }}</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="source" :value="__('db.Source')" />
                            <select id="source" name="source" required class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                @foreach (['facebook' => __('db.Facebook'), 'instagram' => __('db.Instagram'), 'whatsapp' => __('db.WhatsApp'), 'website' => __('db.Website'), 'phone' => __('db.Phone'), 'referral' => __('db.Referral'), 'manual' => __('db.Manual'), 'other' => __('db.Other')] as $value => $label)
                                <option value="{{ $value }}" {{ $lead->source === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="next_follow_up_at" :value="__('db.Next Follow-up')" />
                            <x-text-input id="next_follow_up_at" name="next_follow_up_at" type="date" class="w-full mt-1" :value="$lead->next_follow_up_at?->toDateString()" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="notes" :value="__('db.Notes')" />
                        <textarea id="notes" name="notes" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1">{{ $lead->notes }}</textarea>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <p class="text-xs text-gray-400">{{ __('db.Added :date by :name', ['date' => $lead->created_at->format('d M Y'), 'name' => $lead->createdBy?->name ?? '—']) }}</p>
                        <button class="text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:opacity-90 hover:-translate-y-0.5 transition shadow-sm" style="background: var(--mm-plum);">{{ __('db.Save Changes') }}</button>
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
                    <h4 class="font-semibold text-gray-700 mb-1">🔗 {{ __('db.Client Progress Page') }}</h4>
                    <p class="text-xs text-gray-500 mb-3">{{ __("db.A standing link :name can revisit any time to see their status, timeline, and proposal history. They'll enter the last 7 digits of the phone number above to unlock it — every time, nothing is remembered.", ['name' => $lead->name]) }}</p>

                    @php
                        $progressLink = \App\Http\Controllers\Matchmaker\ClientController::progressLink($lead);
                        $initials = collect(preg_split('/\s+/', trim($lead->name)))->filter()->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->take(2)->implode('');
                        $linkTitle = $initials . ' — ' . __('db.Link Generated by :name', ['name' => $lead->assignedTo->name ?? auth()->user()->name]);
                    @endphp
                    @if ($progressLink)
                    <p class="text-xs font-semibold text-gray-600 mb-2">🔗 {{ $linkTitle }}</p>
                    <p class="text-xs text-gray-400 mb-2">{{ __("db.The link itself isn't shown here — copy it and paste it into WhatsApp, Messenger, SMS, or wherever you're already talking to :name. Only admin can see the raw link.", ['name' => $lead->name]) }}</p>
                    @endif
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($progressLink)
                        <button type="button" data-link="{{ $progressLink }}" onclick="navigator.clipboard.writeText(this.dataset.link); this.textContent = {{ Js::from('✅ ' . __('db.Copied!')) }}; setTimeout(() => this.textContent = {{ Js::from('📋 ' . __('db.Copy Link')) }}, 1500);" class="text-xs font-semibold px-3 py-1.5 rounded-lg text-white hover:opacity-90" style="background: var(--mm-plum);">📋 {{ __('db.Copy Link') }}</button>
                        @endif
                        <form method="POST" action="{{ route('matchmaker.clients.progress-link.regenerate', $lead) }}" @if($progressLink) onsubmit="return confirm({{ Js::from(__('db.Generate a new progress link? The old one will stop working immediately.')) }})" @endif>
                            @csrf
                            <button class="text-xs font-semibold px-2 py-1.5 rounded-lg border" style="border-color: var(--mm-plum); color: var(--mm-plum);">{{ $progressLink ? '↻ ' . __('db.Regenerate') : '+ ' . __('db.Generate Link') }}</button>
                        </form>
                    </div>
                    @unless ($lead->phone)
                    <p class="text-xs text-amber-700 mt-2">{{ __("db.Add a phone number above first — that's what :name will enter to unlock the page.", ['name' => $lead->name]) }}</p>
                    @endunless
                </div>
            </div>

            {{-- === REQUIREMENTS === --}}
            <div x-show="tab === 'requirements'" class="p-6"
                x-data="{
                    items: {{ Js::from($lead->requirement?->items->map(fn($i) => ['requirement_type' => $i->requirement_type, 'requirement_value' => $i->requirement_value, 'priority' => $i->priority, 'notes' => $i->notes])->values() ?? []) }},
                    addRow() { this.items.push({ requirement_type: 'city', requirement_value: '', priority: 'preferred', notes: '' }) },
                    removeRow(i) { this.items.splice(i, 1) },
                }">
                <p class="text-sm text-gray-500 mb-4">{{ __('db.What this client is looking for in a match — used to guide candidate search. Saving replaces the full list below.') }}</p>

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
                                <input type="text" :name="'items[' + i + '][requirement_value]'" x-model="item.requirement_value" placeholder="{{ __('db.Value (e.g. Lahore, 25-32 yrs)') }}" class="border-gray-300 rounded text-sm flex-1 min-w-[10rem]" required>
                                <select :name="'items[' + i + '][priority]'" x-model="item.priority" class="border-gray-300 rounded text-sm">
                                    <option value="must_have">{{ __('db.Must Have') }}</option>
                                    <option value="preferred">{{ __('db.Preferred') }}</option>
                                    <option value="flexible">{{ __('db.Flexible') }}</option>
                                </select>
                                <input type="text" :name="'items[' + i + '][notes]'" x-model="item.notes" placeholder="{{ __('db.Notes (optional)') }}" class="border-gray-300 rounded text-sm flex-1 min-w-[8rem]">
                                <button type="button" @click="removeRow(i)" class="text-red-500 text-xs px-2 py-1.5">{{ __('db.Remove') }}</button>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addRow()" class="text-sm font-semibold px-3 py-1.5 rounded-lg border" style="border-color: var(--mm-plum); color: var(--mm-plum);">+ {{ __('db.Add Requirement') }}</button>

                    <div>
                        <x-input-label for="req_notes" :value="__('db.General Notes')" />
                        <textarea id="req_notes" name="notes" rows="2" class="border-gray-300 rounded-md shadow-sm w-full mt-1">{{ $lead->requirement?->notes }}</textarea>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button class="text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:opacity-90 hover:-translate-y-0.5 transition shadow-sm" style="background: var(--mm-plum);">{{ __('db.Save Requirements') }}</button>
                    </div>
                </form>
            </div>

            {{-- === SHORTLIST === --}}
            <div x-show="tab === 'shortlist'" class="p-6">

                {{-- Suggested matches — ranked against the saved Requirements, see App\Services\Matchmaking\CompatibilityScorer --}}
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">🎯 {{ __('db.Suggested Matches') }}</h4>
                    @if (!$lead->requirement || $lead->requirement->items->isEmpty())
                    <p class="text-sm text-gray-400">{{ __('db.Save') }} <a href="#" @click.prevent="tab = 'requirements'" class="hover:underline" style="color: var(--mm-plum);">{{ __('db.Requirements') }}</a> {{ __("db.first — suggestions are ranked against what's saved there.") }}</p>
                    @elseif ($suggestions->isEmpty())
                    <p class="text-sm text-gray-400">{{ __('db.No strong matches against the saved requirements right now — try widening them, or search manually below.') }}</p>
                    @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ($suggestions as $row)
                        @php $profile = $row['profile']; $result = $row['result']; @endphp
                        <div class="border border-gray-100 rounded-lg p-3">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-medium text-gray-800">{{ $profile->user?->name ?? __('db.Deleted account') }}</p>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full shrink-0 whitespace-nowrap
                                    {{ $result['score'] >= 70 ? 'bg-green-100 text-green-800' : ($result['score'] >= 40 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600') }}">
                                    {{ $result['score'] >= 70 ? '🟢 ' . __('db.High') : ($result['score'] >= 40 ? '🟡 ' . __('db.Medium') : '⚪ ' . __('db.Low')) }} · {{ $result['score'] }}%
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">{{ $profile->user?->gender ? ucfirst($profile->user->gender) . ', ' : '' }}{{ $profile->age }} yrs, {{ $profile->city }} @if($profile->sect) · {{ $profile->sect }} @endif</p>
                            @if ($result['matched']->isNotEmpty())
                            <p class="text-xs text-green-700 mt-1">✓ {{ __('db.Matches: :list', ['list' => $result['matched']->pluck('requirement_type')->map(fn($t) => \App\Models\MatchmakingRequirementItem::TYPES[$t] ?? $t)->implode(', ')]) }}</p>
                            @endif
                            <form method="POST" action="{{ route('matchmaker.clients.shortlist.add', $lead) }}" class="mt-2">
                                @csrf
                                <input type="hidden" name="nikah_profile_id" value="{{ $profile->id }}">
                                <button class="text-xs text-white px-2 py-1 rounded" style="background: var(--mm-plum);">+ {{ __('db.Add to Shortlist') }}</button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                @if ($lead->shortlistItems->isEmpty())
                <p class="text-sm text-gray-400 mb-4">{{ __('db.Nothing shortlisted yet — search below to add candidates.') }}</p>
                @else
                <div class="space-y-2 mb-6">
                    @foreach ($lead->shortlistItems as $item)
                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                        <div>
                            <p class="font-medium text-gray-800">
                                <a href="{{ route('matchmaker.nikah.show', $item->nikah_profile_id) }}" class="hover:underline">{{ $item->nikahProfile->user?->name ?? __('db.Deleted account') }}</a>
                                <span class="text-xs text-gray-400 ml-1">{{ __('db.:age yrs, :city', ['age' => $item->nikahProfile->age, 'city' => $item->nikahProfile->city]) }}</span>
                            </p>
                            @if ($item->note)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $item->note }}</p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('matchmaker.clients.shortlist.remove', [$lead, $item]) }}" onsubmit="return confirm({{ Js::from(__('db.Remove from shortlist?')) }})">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs text-red-500 px-2 py-1">{{ __('db.Remove') }}</button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif

                <details class="mt-2" {{ request()->hasAny(['search_city', 'search_gender', 'search_sect']) ? 'open' : '' }}>
                    <summary class="text-sm font-medium cursor-pointer" style="color: var(--mm-plum);">+ {{ __('db.Search verified profiles to add') }}</summary>
                    <form method="GET" action="{{ route('matchmaker.clients.show', $lead) }}#shortlist" class="flex flex-wrap gap-3 items-end mt-3 mb-3">
                        <div>
                            <label class="text-xs text-gray-500">{{ __('db.Gender') }}</label>
                            <select name="search_gender" class="border-gray-300 rounded text-sm block">
                                <option value="">{{ __('db.Any') }}</option>
                                <option value="male" {{ request('search_gender') === 'male' ? 'selected' : '' }}>{{ __('db.Male') }}</option>
                                <option value="female" {{ request('search_gender') === 'female' ? 'selected' : '' }}>{{ __('db.Female') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">{{ __('db.City') }}</label>
                            <input type="text" name="search_city" value="{{ request('search_city') }}" class="border-gray-300 rounded text-sm block w-40">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">{{ __('db.Sect') }}</label>
                            <input type="text" name="search_sect" value="{{ request('search_sect') }}" class="border-gray-300 rounded text-sm block w-40">
                        </div>
                        <button class="text-white text-sm px-4 py-2 rounded" style="background: var(--mm-plum-dark);">{{ __('db.Search') }}</button>
                    </form>

                    @if ($searchResults->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ($searchResults as $profile)
                        <div class="flex items-center justify-between border border-gray-100 rounded-lg p-3">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $profile->user?->name ?? __('db.Deleted account') }}</p>
                                <p class="text-xs text-gray-500">{{ __('db.:age yrs, :city', ['age' => $profile->age, 'city' => $profile->city]) }} @if($profile->sect) · {{ $profile->sect }} @endif</p>
                            </div>
                            <form method="POST" action="{{ route('matchmaker.clients.shortlist.add', $lead) }}">
                                @csrf
                                <input type="hidden" name="nikah_profile_id" value="{{ $profile->id }}">
                                <button class="text-xs text-white px-2 py-1 rounded" style="background: var(--mm-plum);">+ {{ __('db.Add') }}</button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    @elseif (request()->hasAny(['search_city', 'search_gender', 'search_sect']))
                    <p class="text-sm text-gray-400">{{ __('db.No matching profiles found.') }}</p>
                    @endif
                </details>
            </div>

            {{-- === CONSENT === --}}
            <div x-show="tab === 'consent'" class="p-6">
                <h4 class="font-semibold text-gray-700 mb-1">✅ {{ __('db.Consent') }}</h4>
                <p class="text-xs text-gray-500 mb-3">{!! __("db.Best way: ask them to confirm it themselves through their secure link — the system asks them directly, no guesswork about what they actually agreed to. If that's not possible, you can still record consent you got verbally, by phone, or in person. An active :participation consent is required before you can send this client any proposals.", ['participation' => '<strong>' . __('db.Nikah Counseling Participation') . '</strong>']) !!}</p>

                @if ($lead->consentRequests->where('status', 'pending')->isNotEmpty())
                <div class="space-y-1.5 mb-3">
                    @foreach ($lead->consentRequests->where('status', 'pending') as $req)
                    <div class="text-xs bg-blue-50 text-blue-800 rounded-lg px-3 py-2">
                        ⏳ {{ __('db.Waiting on :name to confirm:', ['name' => $lead->name]) }} <strong>{{ explode(' — ', \App\Models\MatchmakingConsent::TYPES[$req->consent_type])[0] }}</strong>
                        <span class="text-blue-500"> · {{ __('db.requested :time', ['time' => $req->requested_at->diffForHumans()]) }}</span>
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
                            <span class="text-gray-400"> · {{ \App\Models\MatchmakingConsent::METHODS[$consent->method] ?? $consent->method }} · {{ $consent->granted_at->format('d M Y') }} · {{ __('db.by :name', ['name' => $consent->recordedBy?->name ?? '—']) }}</span>
                            @if (!$consent->isActive())
                            <span class="text-red-500"> · {{ __('db.revoked :date by :name', ['date' => $consent->revoked_at->format('d M Y'), 'name' => $consent->revokedBy?->name ?? '—']) }}</span>
                            @endif
                        </div>
                        @if ($consent->isActive())
                        <form method="POST" action="{{ route('matchmaker.clients.consents.revoke', [$lead, $consent]) }}" onsubmit="return confirm({{ Js::from(__('db.Revoke this consent?')) }})">
                            @csrf
                            <button class="text-red-500 hover:underline">{{ __('db.Revoke') }}</button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('matchmaker.clients.consents.request', $lead) }}" class="flex flex-wrap gap-2 items-end mb-3">
                    @csrf
                    <div>
                        <label class="text-xs text-gray-500">{{ __('db.Type') }}</label>
                        <select name="consent_type" required class="border-gray-300 rounded text-sm block">
                            @foreach (\App\Models\MatchmakingConsent::TYPES as $value => $label)
                            <option value="{{ $value }}" title="{{ $label }}">{{ explode(' — ', $label)[0] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="text-xs font-semibold px-3 py-1.5 rounded-lg border" style="border-color: var(--mm-plum); color: var(--mm-plum);">🔗 {{ __('db.Ask Them to Confirm via Link') }}</button>
                    @unless ($lead->phone)
                    <span class="text-xs text-amber-700">{{ __('db.Add a phone number above first.') }}</span>
                    @endunless
                </form>

                <p class="text-xs text-gray-400 mb-2">{{ __('db.— or, if you already got it verbally, by phone, or in person —') }}</p>

                <form method="POST" action="{{ route('matchmaker.clients.consents.record', $lead) }}" class="flex flex-wrap gap-2 items-end">
                    @csrf
                    <div>
                        <label class="text-xs text-gray-500">{{ __('db.Type') }}</label>
                        <select name="consent_type" required class="border-gray-300 rounded text-sm block">
                            @foreach (\App\Models\MatchmakingConsent::TYPES as $value => $label)
                            <option value="{{ $value }}" title="{{ $label }}">{{ explode(' — ', $label)[0] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">{{ __('db.How obtained') }}</label>
                        <select name="method" required class="border-gray-300 rounded text-sm block">
                            @foreach (\App\Models\MatchmakingConsent::METHODS as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1 min-w-[10rem]">
                        <label class="text-xs text-gray-500">{{ __('db.Notes (optional)') }}</label>
                        <input type="text" name="notes" class="border-gray-300 rounded text-sm block w-full">
                    </div>
                    <button class="text-xs font-semibold px-3 py-1.5 rounded-lg text-white hover:opacity-90" style="background: var(--mm-plum);">{{ __('db.Record Consent') }}</button>
                </form>
            </div>

            {{-- === PROPOSALS (proposal batches) === --}}
            <div x-show="tab === 'batches'" class="p-6 space-y-6">
                @if ($lead->nikah_package_id)
                <div class="text-xs px-3 py-2 rounded-lg {{ $lead->packageExpired() ? 'bg-red-50 text-red-700' : 'bg-gray-50 text-gray-600' }}">
                    📦 {{ $lead->nikahPackage->name }}
                    @if ($lead->packageExpired())
                    — <strong>{{ __('db.expired') }}</strong> {{ $lead->package_expires_at->format('d M Y') }}
                    @elseif ($lead->package_expires_at)
                    — {{ __('db.active until :date', ['date' => $lead->package_expires_at->format('d M Y')]) }}
                    @endif
                    @if ($lead->nikahPackage->proposal_limit)
                    · {{ __('db.:remaining of :limit proposals remaining', ['remaining' => $lead->remainingProposalAllowance(), 'limit' => $lead->nikahPackage->proposal_limit]) }}
                    @endif
                </div>
                @else
                <p class="text-sm text-gray-500 bg-gray-50 rounded-lg p-3">{{ __("db.No package active yet — the client chooses and pays for their own package on their progress link; nothing for you to assign here.") }}</p>
                @endif

                @unless ($lead->nikah_profile_id)
                <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3">{{ __("db.Link or register this client's Nikah profile before you can send them a proposal batch.") }}</p>
                @elseif (!$lead->hasActiveConsent('matchmaking_participation'))
                <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3">{!! __("db.Record this client's :participation consent (see the Consent tab) before you can send them a proposal batch.", ['participation' => '<strong>' . __('db.Nikah Counseling Participation') . '</strong>']) !!}</p>
                @else
                <form method="POST" action="{{ route('matchmaker.clients.batches.create', $lead) }}">
                    @csrf
                    <button class="text-sm font-semibold px-4 py-2.5 rounded-lg text-white hover:opacity-90 hover:-translate-y-0.5 transition shadow-sm" style="background: var(--mm-plum);">➕ {{ __('db.Start New Proposal Batch') }}</button>
                </form>
                @endunless

                @forelse ($lead->proposalBatches as $batch)
                <div class="border border-gray-100 rounded-xl overflow-hidden">
                    <div class="flex flex-wrap justify-between items-center gap-2 px-4 py-3 bg-gray-50">
                        <div>
                            <span class="font-semibold text-gray-800">{{ __('db.Batch #:number', ['number' => $batch->batch_number]) }}</span>
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
                        <form method="POST" action="{{ route('matchmaker.clients.batches.send', [$lead, $batch]) }}" onsubmit="return confirm({{ Js::from(__('db.Mark this batch as sent? :name will see these candidates on their own progress link and can respond there.', ['name' => $lead->name])) }})">
                            @csrf
                            <button class="text-xs font-semibold px-3 py-1.5 rounded-lg text-white hover:opacity-90" style="background: var(--mm-plum-dark);">{{ __('db.Mark as Sent') }} →</button>
                        </form>
                        @endif
                    </div>

                    <div class="p-4 space-y-3">
                        @forelse ($batch->proposals as $proposal)
                        <div class="flex flex-wrap items-start justify-between gap-3 border-b last:border-0 pb-3 last:pb-0">
                            <div class="flex-1 min-w-[12rem]">
                                <p class="font-medium text-gray-800 text-sm">{{ $proposal->candidate->user?->name ?? __('db.Deleted account') }}</p>
                                <p class="text-xs text-gray-400">{{ __('db.:age yrs, :city', ['age' => $proposal->candidate->age, 'city' => $proposal->candidate->city]) }}</p>
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
                                        'accepted' => '💞 ' . __('db.Mutual Interest!'),
                                        'declined' => __('db.Candidate Declined'),
                                        default => __('db.Awaiting Candidate'),
                                    } }}
                                </span>
                                @endif
                            </div>

                            @if ($batch->status === 'draft')
                            <form method="POST" action="{{ route('matchmaker.clients.batches.proposals.remove', [$lead, $batch, $proposal]) }}">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs text-red-500 px-2 py-1">{{ __('db.Remove') }}</button>
                            </form>
                            @elseif ($proposal->sent_at && !$proposal->response)
                            <span class="text-xs text-gray-400">{{ __('db.Waiting for :name to respond on their progress link', ['name' => $lead->name]) }}</span>
                            @endif
                        </div>
                        @empty
                        <p class="text-sm text-gray-400">{{ __('db.No candidates added yet.') }}</p>
                        @endforelse

                        @if ($batch->status === 'draft')
                        <form method="POST" action="{{ route('matchmaker.clients.batches.proposals.add', [$lead, $batch]) }}" class="flex flex-wrap gap-2 items-end pt-2">
                            @csrf
                            <div class="flex-1 min-w-[12rem]">
                                <label class="text-xs text-gray-500">{{ __('db.Add from shortlist') }}</label>
                                <select name="candidate_profile_id" required class="border-gray-300 rounded text-sm w-full">
                                    <option value="">{{ __('db.Choose a candidate…') }}</option>
                                    @foreach ($lead->shortlistItems as $item)
                                    @unless ($batch->proposals->pluck('candidate_profile_id')->contains($item->nikah_profile_id))
                                    <option value="{{ $item->nikah_profile_id }}">{{ $item->nikahProfile->user?->name ?? __('db.Deleted account') }} — {{ __('db.:age yrs, :city', ['age' => $item->nikahProfile->age, 'city' => $item->nikahProfile->city]) }}</option>
                                    @endunless
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1 min-w-[12rem]">
                                <label class="text-xs text-gray-500">{{ __('db.Why this match (optional)') }}</label>
                                <input type="text" name="match_reasons[]" class="border-gray-300 rounded text-sm w-full" placeholder="{{ __('db.Same city, similar age…') }}">
                            </div>
                            <button class="text-xs font-semibold px-3 py-1.5 rounded-lg text-white hover:opacity-90" style="background: var(--mm-plum);">+ {{ __('db.Add Candidate') }}</button>
                        </form>
                        @if ($lead->shortlistItems->isEmpty())
                        <p class="text-xs text-gray-400">{{ __('db.Shortlist a profile first (see the Shortlist tab) — batches are built from shortlisted candidates.') }}</p>
                        @endif
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400">{{ __('db.No proposal batches yet.') }}</p>
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
                <p class="text-sm text-gray-400">{{ __('db.No activity logged yet.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-matchmaker-layout>
