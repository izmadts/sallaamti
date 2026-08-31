<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.matchmaker-applications.index') }}" class="text-gray-400 hover:text-gray-600">Nikah Counselor Applications</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">{{ $application->full_name }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif
            @if (session('error'))
            <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
            <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Header --}}
            <div class="bg-white rounded-xl shadow-sm p-6 flex flex-wrap justify-between items-start gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $application->full_name }}</h2>
                    <p class="text-sm text-gray-500">{{ $application->guardian_name }} · {{ ucfirst($application->gender) }}, {{ $application->age }} yrs</p>
                    @if ($application->counselor_code)
                    <p class="text-sm font-semibold mt-1" style="color: #b8962e">ID: {{ $application->counselor_code }}</p>
                    @endif
                </div>
                <span class="text-xs px-3 py-1 rounded-full font-semibold
                    {{ match(true) {
                        $application->status === 'certified' => 'bg-green-100 text-green-800',
                        $application->isTerminal() => 'bg-red-100 text-red-800',
                        default => 'bg-amber-100 text-amber-800',
                    } }}">
                    {{ \App\Models\MatchmakerApplication::STEPS[$application->status] ?? ucfirst($application->status) }}
                </span>
            </div>

            {{-- Pipeline progress --}}
            @unless ($application->isTerminal())
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-4 border-b pb-2">Onboarding Pipeline</h4>
                <div class="flex flex-wrap gap-2 mb-5">
                    @php $currentIndex = array_search($application->status, array_keys(\App\Models\MatchmakerApplication::STEPS)); @endphp
                    @foreach (\App\Models\MatchmakerApplication::STEPS as $value => $label)
                    @php $stepIndex = array_search($value, array_keys(\App\Models\MatchmakerApplication::STEPS)); @endphp
                    <span class="text-xs px-2.5 py-1 rounded-full {{ $stepIndex <= $currentIndex ? 'text-white' : 'bg-gray-100 text-gray-400' }}" @if($stepIndex <= $currentIndex) style="background: {{ $stepIndex === $currentIndex ? '#0d6b6b' : '#0d6b6b99' }}" @endif>
                        {{ $stepIndex + 1 }}. {{ $label }}
                    </span>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('admin.matchmaker-applications.status', $application) }}" class="flex flex-wrap gap-2 items-end">
                    @csrf
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Move to Stage</label>
                        <select name="status" class="border-gray-300 rounded-md text-sm">
                            @foreach (\App\Models\MatchmakerApplication::STEPS as $value => $label)
                            <option value="{{ $value }}" {{ $application->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="text-sm font-semibold px-4 py-2 rounded-lg text-white hover:opacity-90" style="background: #0d6b6b">Update Stage</button>
                </form>
                <p class="text-xs text-gray-400 mt-2">Moving to "Certified" creates their Sallaamti account, grants the matchmaker role, generates their Counselor ID, and issues their certificate — all automatically. This is blocked until they've accepted the Agreement/NDA below.</p>

                <form method="POST" action="{{ route('admin.matchmaker-applications.reject', $application) }}" class="mt-4 pt-4 border-t flex gap-2 items-end" onsubmit="return confirm('Reject this application?')">
                    @csrf
                    <div class="flex-1">
                        <label class="text-xs text-gray-500 block mb-1">Rejection Notes (optional)</label>
                        <input type="text" name="notes" class="border-gray-300 rounded-md text-sm w-full">
                    </div>
                    <button class="text-sm font-semibold px-4 py-2 rounded-lg border border-red-300 text-red-600 hover:bg-red-50">Reject Application</button>
                </form>

                <form method="POST" action="{{ route('admin.matchmaker-applications.withdraw', $application) }}" class="mt-3 flex gap-2 items-end" onsubmit="return confirm('Mark this application as withdrawn? Use this only if the applicant themselves asked to withdraw — otherwise use Reject above.')">
                    @csrf
                    <div class="flex-1">
                        <label class="text-xs text-gray-500 block mb-1">Withdrawal Notes (optional)</label>
                        <input type="text" name="notes" class="border-gray-300 rounded-md text-sm w-full" placeholder="e.g. applicant asked to withdraw by phone">
                    </div>
                    <button class="text-sm font-semibold px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50">Mark as Withdrawn</button>
                </form>
            </div>
            @endunless

            {{-- Reactivate a rejected application — admin-only (reject()/withdraw()
                 have no side effects to undo, so this just clears the rejection
                 and drops it back to the first stage for admin to re-advance) --}}
            @if ($application->status === 'rejected' && auth()->user()->hasRole('admin'))
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-2 border-b pb-2">Reactivate Application</h4>
                <p class="text-xs text-gray-500 mb-3">Undoes the rejection and moves this application back to "Application Received" — you can then move it forward through the pipeline again.</p>
                <form method="POST" action="{{ route('admin.matchmaker-applications.reactivate', $application) }}" onsubmit="return confirm('Reactivate this application? It will move back to \'Application Received.\'')">
                    @csrf
                    <button class="text-sm font-semibold px-4 py-2 rounded-lg text-white hover:opacity-90" style="background: #0d6b6b">Reactivate Application</button>
                </form>
            </div>
            @endif

            {{-- Agreement / NDA --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-1 border-b pb-2">📜 Nikah Counselor Agreement & NDA</h4>

                @if ($application->hasAcceptedAgreementAndNda())
                <p class="text-sm bg-green-50 text-green-700 rounded-lg p-3 mt-3">✅ Accepted {{ $application->agreement_accepted_at->format('d M Y, h:i A') }} (IP: {{ $application->agreement_ip }})</p>
                @else
                <p class="text-xs text-gray-500 mt-3 mb-3">They haven't accepted the Agreement/NDA themselves yet — required before certification.</p>

                <form method="POST" action="{{ route('admin.matchmaker-applications.agreement-link', $application) }}">
                    @csrf
                    <button class="text-xs font-semibold px-3 py-1.5 rounded-lg text-white hover:opacity-90" style="background: #0d6b6b">{{ $application->agreement_link_token ? '↻ Regenerate Link' : '+ Generate Agreement Link' }}</button>
                </form>

                @if ($application->agreement_link_token)
                <div class="flex flex-wrap items-center gap-2 mt-3">
                    <input type="text" readonly value="{{ \App\Http\Controllers\Admin\MatchmakerApplicationController::agreementLink($application) }}" class="text-xs border-gray-200 rounded-lg flex-1 min-w-[16rem] bg-gray-50" onclick="this.select()" id="agreement-link">
                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('agreement-link').value); this.textContent = 'Copied!'; setTimeout(() => this.textContent = 'Copy Link', 1500);" class="text-xs font-semibold px-2 py-1.5 rounded-lg text-white hover:opacity-90" style="background: #0d6b6b">Copy Link</button>
                </div>
                <p class="text-xs text-amber-700 mt-2">They'll need the last 7 digits of their mobile number ({{ $application->mobile_number }}) to open it, every time.</p>
                @endif
                @endif
            </div>

            {{-- Certified: level + referral --}}
            @if ($application->isCertified())
            <div class="bg-white rounded-xl shadow-sm p-6" style="border-top: 3px solid #b8962e">
                <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">🎖️ Certified Nikah Counselor</h4>

                <form method="POST" action="{{ route('admin.matchmaker-applications.level', $application) }}" class="flex flex-wrap gap-2 items-end mb-4">
                    @csrf
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Public Level</label>
                        <select name="level" class="border-gray-300 rounded-md text-sm">
                            @foreach (\App\Models\MatchmakerApplication::LEVELS as $value => $label)
                            <option value="{{ $value }}" {{ $application->level === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="text-xs font-semibold px-3 py-1.5 rounded-lg border" style="border-color: #b8962e; color: #b8962e;">Update Level</button>
                </form>

                <div class="grid sm:grid-cols-3 gap-4 text-sm">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-1">Referral Link</p>
                        <input type="text" readonly value="{{ url('/register?ref=' . $application->counselor_code) }}" class="text-xs border-gray-200 rounded-lg w-full bg-white" onclick="this.select()">
                        <img src="{{ $application->referralQrCodeBase64() }}" class="w-16 h-16 rounded border border-gray-200 p-1 bg-white mt-2">
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-1">Referred Registrations</p>
                        <p class="text-lg font-bold" style="color: #0d6b6b">{{ \App\Models\MatchmakerReferral::where('counselor_user_id', $application->user_id)->count() }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-1">Certificate</p>
                        @php $certificate = \App\Models\Certificate::where('user_id', $application->user_id)->where('type', 'nikah_counselor_id')->first(); @endphp
                        @if ($certificate)
                        <a href="{{ route('certificate.download', $certificate) }}" class="text-sm font-semibold hover:underline" style="color: #0d6b6b">⬇ Download ID Card PDF</a>
                        @else
                        <span class="text-xs text-gray-400">Not yet generated</span>
                        @endif
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-1">Physical Card</p>
                        @if ($application->card_dispatched_at)
                        <p class="text-sm font-semibold text-green-700">✓ Dispatched {{ $application->card_dispatched_at->format('d M Y') }}</p>
                        @elseif ($application->card_requested_at)
                        <p class="text-xs text-amber-700 mb-1">Requested {{ $application->card_requested_at->format('d M Y') }}</p>
                        <form method="POST" action="{{ route('admin.matchmaker-applications.card-dispatched', $application) }}">
                            @csrf
                            <button class="text-xs font-semibold px-2 py-1 rounded-lg border" style="border-color: #0d6b6b; color: #0d6b6b">Mark Dispatched</button>
                        </form>
                        @else
                        <span class="text-xs text-gray-400">Not requested yet</span>
                        @endif
                        @if ($application->address)
                        <p class="text-xs text-gray-400 mt-2">{{ collect([$application->address, $application->area, $application->country])->filter()->implode(', ') }}</p>
                        @endif
                    </div>
                </div>

                {{-- Same numbers the counselor's own Performance page shows them — previously invisible to admin, so a manual level override or judging an auto-promotion (Console\Commands\PromoteEligibleCounselors) had nothing to go on. --}}
                @if ($performance)
                <div class="mt-4 pt-4 border-t">
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Performance</p>
                    <div class="grid sm:grid-cols-4 gap-3 text-sm mb-3">
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-lg font-bold text-gray-800">{{ $performance['stats']['verified'] }}</p>
                            <p class="text-xs text-gray-400">Verified Profiles</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-lg font-bold text-gray-800">{{ $performance['score']['overall'] ?? '—' }}{{ $performance['score']['overall'] !== null ? '%' : '' }}</p>
                            <p class="text-xs text-gray-400">Quality Score</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-lg font-bold text-gray-800">{{ $application->tenureDays() ?? '—' }}</p>
                            <p class="text-xs text-gray-400">Days Certified</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-lg font-bold text-gray-800">Rs. {{ number_format($performance['commission_earned'], 0) }}</p>
                            <p class="text-xs text-gray-400">Commission Earned</p>
                        </div>
                    </div>

                    @if ($levelProgress)
                    <p class="text-xs text-gray-500 mb-2">Progress toward auto-promotion to <strong>{{ $levelProgress['next_level_label'] }}</strong>:</p>
                    <div class="space-y-2">
                        @foreach ([
                            ['Verified Profiles', $levelProgress['verified'], ''],
                            ['Quality Score', $levelProgress['quality_score'], '%'],
                            ['Days Certified', $levelProgress['tenure_days'], ' days'],
                        ] as $req)
                        <div class="flex items-center gap-2 text-xs">
                            <span class="w-32 text-gray-500 shrink-0">{{ $req[0] }}</span>
                            <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full {{ $req[1]['met'] ? 'bg-green-500' : '' }}" style="width: {{ min(100, $req[1]['needed'] > 0 ? round($req[1]['current'] / $req[1]['needed'] * 100) : 100) }}%; {{ $req[1]['met'] ? '' : 'background: #b8962e' }}"></div>
                            </div>
                            <span class="{{ $req[1]['met'] ? 'text-green-600 font-semibold' : 'text-gray-600' }} shrink-0">{{ $req[1]['current'] }}{{ $req[2] }} / {{ $req[1]['needed'] }}{{ $req[2] }}</span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-gray-400">Already at the highest level.</p>
                    @endif
                </div>
                @endif
            </div>
            @endif

            {{-- Application details --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Application Details</h4>
                <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div><dt class="text-gray-400">Mobile</dt><dd>{{ $application->mobile_number }}</dd></div>
                    <div><dt class="text-gray-400">WhatsApp</dt><dd>{{ $application->whatsapp_number ?: 'Same as mobile' }}</dd></div>
                    <div><dt class="text-gray-400">Marital Status</dt><dd>{{ ucfirst(str_replace('_', ' ', $application->marital_status)) }}</dd></div>
                    <div><dt class="text-gray-400">Qualification</dt><dd>{{ $application->qualification === 'other' ? $application->qualification_other : (\App\Models\MatchmakerApplication::QUALIFICATIONS[$application->qualification] ?? '—') }}</dd></div>
                    <div><dt class="text-gray-400">City</dt><dd>{{ $application->area ?: '—' }}</dd></div>
                    <div><dt class="text-gray-400">Country</dt><dd>{{ $application->country ?: '—' }}</dd></div>
                    <div><dt class="text-gray-400">Address</dt><dd>{{ $application->address ?: '—' }}</dd></div>
                    <div><dt class="text-gray-400">CNIC Number</dt><dd>{{ $application->cnic_number }}</dd></div>
                    <div><dt class="text-gray-400">Linked Account</dt><dd>{{ $application->user?->name ?? '— not created yet —' }}</dd></div>
                </dl>

                <div class="grid sm:grid-cols-3 gap-3 mt-5 pt-4 border-t">
                    <a href="{{ route('admin.matchmaker-applications.file', [$application, 'selfie_photo']) }}" target="_blank" class="text-center bg-gray-50 rounded-lg p-3 text-xs text-gray-600 hover:bg-gray-100">📷 Selfie Photo</a>
                    <a href="{{ route('admin.matchmaker-applications.file', [$application, 'cnic_front_image']) }}" target="_blank" class="text-center bg-gray-50 rounded-lg p-3 text-xs text-gray-600 hover:bg-gray-100">🪪 CNIC Front</a>
                    <a href="{{ route('admin.matchmaker-applications.file', [$application, 'cnic_back_image']) }}" target="_blank" class="text-center bg-gray-50 rounded-lg p-3 text-xs text-gray-600 hover:bg-gray-100">🪪 CNIC Back</a>
                </div>
            </div>

            {{-- Payout details --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Commission Payout Details</h4>
                @if ($application->payout_method)
                <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div><dt class="text-gray-400">Method</dt><dd>{{ ucfirst(str_replace('_', ' ', $application->payout_method)) }}</dd></div>
                    <div><dt class="text-gray-400">Account Title</dt><dd>{{ $application->payout_account_title ?: '—' }}</dd></div>
                    <div><dt class="text-gray-400">Account / Mobile Number</dt><dd>{{ $application->payout_account_number ?: '—' }}</dd></div>
                    @if ($application->payout_method === 'bank_transfer')
                    <div><dt class="text-gray-400">Bank</dt><dd>{{ $application->payout_bank_name ?: '—' }}</dd></div>
                    @endif
                </dl>
                @else
                <p class="text-sm text-gray-400">Not provided yet.</p>
                @endif
            </div>

            {{-- Audit / fraud signals --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Application Metadata</h4>
                <dl class="grid sm:grid-cols-3 gap-x-6 gap-y-3 text-sm">
                    <div><dt class="text-gray-400">Submitted</dt><dd>{{ $application->created_at->format('d M Y, h:i A') }}</dd></div>
                    <div><dt class="text-gray-400">IP Address</dt><dd>{{ $application->ip_address ?: '—' }}</dd></div>
                    <div><dt class="text-gray-400">Approx. Location</dt><dd>{{ $application->device_city ?: '—' }}</dd></div>
                </dl>
                @if ($application->reviewedBy)
                <p class="text-xs text-gray-400 mt-3">Last reviewed by {{ $application->reviewedBy->name }}</p>
                @endif
            </div>

        </div>
    </div>
</x-admin-layout>
