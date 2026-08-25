<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.leads.index') }}" class="text-gray-400 hover:text-gray-600">Leads</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">{{ $lead->name }}</span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Lead details + editable status/package --}}
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">{{ $lead->name }}</h2>
                            <p class="text-sm text-gray-500">{{ $lead->phone ?: '— no phone —' }} · {{ $lead->email ?: '— no email —' }}</p>
                        </div>
                        @if ($lead->isConverted())
                        <a href="{{ route('admin.nikah.show', $lead->nikah_profile_id) }}" class="text-sm font-semibold px-3 py-1.5 rounded-lg text-white hover:opacity-90" style="background: #0d6b6b">View Nikah Profile →</a>
                        @else
                        <form method="POST" action="{{ route('admin.leads.convert', $lead) }}">
                            @csrf
                            <button class="text-sm font-semibold px-3 py-1.5 rounded-lg text-white hover:opacity-90" style="background: #be185d"
                                onclick="return confirm('Start registering {{ $lead->name }} as a real Sallaamti account? You\'ll continue in the walk-in wizard with their details pre-filled.')">
                                Convert to Client →
                            </button>
                        </form>
                        @endif
                    </div>

                    @if (!$lead->isConverted())
                    <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
                        Not registered yet. Already have a profile for them (started separately)?
                        <form method="POST" action="{{ route('admin.leads.link-profile', $lead) }}" class="inline-flex items-center gap-2 mt-1">
                            @csrf
                            <input type="number" name="nikah_profile_id" placeholder="Profile ID" class="border-gray-300 rounded text-xs w-24 py-1" required>
                            <button class="text-xs bg-amber-600 text-white px-2 py-1 rounded">Link it</button>
                        </form>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('admin.leads.update', $lead) }}" class="space-y-4">
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
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="phone" value="Phone / WhatsApp" />
                                <x-text-input id="phone" name="phone" type="text" class="w-full mt-1" :value="$lead->phone" />
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
                                <x-input-label for="assigned_to" value="Assigned To" />
                                <select id="assigned_to" name="assigned_to" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Unassigned</option>
                                    @foreach ($matchmakers as $mm)
                                    <option value="{{ $mm->id }}" {{ $lead->assigned_to === $mm->id ? 'selected' : '' }}>{{ $mm->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <x-input-label for="next_follow_up_at" value="Next Follow-up" />
                            <x-text-input id="next_follow_up_at" name="next_follow_up_at" type="date" class="w-full mt-1" :value="$lead->next_follow_up_at?->toDateString()" />
                        </div>
                        <div>
                            <x-input-label for="notes" value="Notes" />
                            <textarea id="notes" name="notes" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1">{{ $lead->notes }}</textarea>
                        </div>

                        <div class="pt-3 border-t border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Matchmaking Package</p>
                            <div class="grid grid-cols-4 gap-4">
                                <div class="col-span-2">
                                    <x-input-label for="nikah_package_id" value="Package" />
                                    <select id="nikah_package_id" name="nikah_package_id" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                        <option value="">None</option>
                                        @foreach ($packages as $p)
                                        <option value="{{ $p->id }}" {{ $lead->nikah_package_id === $p->id ? 'selected' : '' }}>{{ $loop->iteration }}. {{ $p->name }} (Rs. {{ number_format($p->price) }}{{ $p->duration_days ? ' / ' . $p->duration_days . 'd' : '' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="package_price" value="Price (Rs.)" />
                                    <x-text-input id="package_price" name="package_price" type="number" step="0.01" class="w-full mt-1" :value="$lead->package_price" placeholder="Defaults to package price" />
                                </div>
                                <div>
                                    <x-input-label for="package_started_at" value="Started" />
                                    <x-text-input id="package_started_at" name="package_started_at" type="date" class="w-full mt-1" :value="$lead->package_started_at?->toDateString()" placeholder="Defaults to today" />
                                </div>
                            </div>
                            @if ($lead->nikah_package_id)
                            <p class="text-xs text-gray-500 mt-2">
                                Expires: {{ $lead->package_expires_at?->format('d M Y') ?? 'Never (one-time package)' }}
                                @if ($lead->packageExpired()) <span class="text-red-600 font-semibold">— expired</span> @endif
                                @if ($lead->nikahPackage?->proposal_limit)
                                · {{ $lead->remainingProposalAllowance() }} of {{ $lead->nikahPackage->proposal_limit }} proposals remaining
                                @endif
                            </p>
                            @endif
                        </div>

                        <div class="flex justify-end pt-2">
                            <x-primary-button>Save Changes</x-primary-button>
                        </div>
                    </form>
                </div>

                {{-- Side info --}}
                <div class="space-y-4">
                    <div class="bg-white rounded-xl shadow-sm p-4 text-sm text-gray-500">
                        <p>Added {{ $lead->created_at->format('d M Y') }} by {{ $lead->createdBy?->name ?? '—' }}</p>
                        <p class="mt-1">Looking for: {{ $lead->looking_for ? str_replace('_', ' ', $lead->looking_for) : 'Unknown' }}</p>
                    </div>
                </div>
            </div>

            {{-- Shortlist --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">💌 Shortlist ({{ $lead->shortlistItems->count() }})</h3>

                @if ($lead->shortlistItems->isEmpty())
                <p class="text-sm text-gray-400 mb-4">Nothing shortlisted yet — search below to add candidates.</p>
                @else
                <div class="space-y-2 mb-4">
                    @foreach ($lead->shortlistItems as $item)
                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                        <div>
                            <p class="font-medium text-gray-800">
                                <a href="{{ route('admin.nikah.show', $item->nikah_profile_id) }}" class="hover:underline">{{ $item->nikahProfile->user?->name ?? 'Deleted account' }}</a>
                                <span class="text-xs text-gray-400 ml-1">{{ $item->nikahProfile->age }} yrs, {{ $item->nikahProfile->city }}</span>
                            </p>
                            @if ($item->note)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $item->note }}</p>
                            @endif
                            <p class="text-xs mt-0.5 {{ $item->sent_at ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $item->sent_at ? '✓ Shared ' . $item->sent_at->diffForHumans() : 'Not shared yet' }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            @unless ($item->sent_at)
                            <form method="POST" action="{{ route('admin.leads.shortlist.sent', [$lead, $item]) }}">
                                @csrf
                                <button class="text-xs bg-teal-600 text-white px-2 py-1 rounded">Mark Shared</button>
                            </form>
                            @endunless
                            <form method="POST" action="{{ route('admin.leads.shortlist.remove', [$lead, $item]) }}" onsubmit="return confirm('Remove from shortlist?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs text-red-500 px-2 py-1">Remove</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <details class="mt-2">
                    <summary class="text-sm font-medium cursor-pointer" style="color: var(--teal, #0d6b6b)">+ Search verified profiles to add</summary>
                    <form method="GET" class="flex flex-wrap gap-3 items-end mt-3 mb-3">
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
                        <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded">Search</button>
                    </form>

                    @if ($searchResults->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ($searchResults as $profile)
                        <div class="flex items-center justify-between border border-gray-100 rounded-lg p-3">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $profile->user?->name ?? 'Deleted account' }}</p>
                                <p class="text-xs text-gray-500">{{ $profile->age }} yrs, {{ $profile->city }} @if($profile->sect) · {{ $profile->sect }} @endif</p>
                            </div>
                            <form method="POST" action="{{ route('admin.leads.shortlist.add', $lead) }}">
                                @csrf
                                <input type="hidden" name="nikah_profile_id" value="{{ $profile->id }}">
                                <button class="text-xs bg-teal-600 text-white px-2 py-1 rounded">+ Add</button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    @elseif (request()->hasAny(['search_city', 'search_gender', 'search_sect']))
                    <p class="text-sm text-gray-400">No matching profiles found.</p>
                    @endif
                </details>
            </div>

            {{-- Progress page link — admin sees the raw link and phone; the matchmaker's own view only gets action buttons (Copy/WhatsApp/SMS), never the link text or number itself. --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-1 border-b pb-2">🔗 Client Progress Page Link</h3>
                <p class="text-xs text-gray-500 mt-2 mb-3">The one standing link {{ $lead->name }} uses for everything — status, documents, consents, proposals. Only admin sees the raw link and phone number here; the assigned counselor only gets Copy/Send buttons.</p>

                @php $adminProgressLink = \App\Http\Controllers\Matchmaker\ClientController::progressLink($lead); @endphp
                <div class="flex flex-wrap items-center gap-2">
                    @if ($adminProgressLink)
                    <input type="text" readonly value="{{ $adminProgressLink }}" class="text-xs border-gray-200 rounded-lg flex-1 min-w-[16rem] bg-gray-50" onclick="this.select()" id="admin-progress-link">
                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('admin-progress-link').value); this.textContent = 'Copied!'; setTimeout(() => this.textContent = 'Copy Link', 1500);" class="text-xs font-semibold px-2 py-1.5 rounded-lg bg-gray-800 text-white hover:opacity-90">Copy Link</button>
                    @else
                    <p class="text-sm text-gray-400">No link generated yet — the assigned counselor generates it from their own Client page, or add a phone number and regenerate here.</p>
                    @endif
                    <form method="POST" action="{{ route('admin.leads.progress-link.regenerate', $lead) }}" @if($adminProgressLink) onsubmit="return confirm('Generate a new progress link? The old one will stop working immediately.')" @endif>
                        @csrf
                        <button class="text-xs font-semibold px-2 py-1.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50">{{ $adminProgressLink ? '↻ Regenerate' : '+ Generate Link' }}</button>
                    </form>
                </div>
                @unless ($lead->phone)
                <p class="text-xs text-amber-700 mt-2">Add a phone number above first.</p>
                @endunless
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">🕓 Activity Timeline ({{ $lead->timelineEvents->count() }})</h3>
                @forelse ($lead->timelineEvents as $event)
                <div class="flex gap-3 py-2.5 border-b last:border-0">
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
</x-admin-layout>
