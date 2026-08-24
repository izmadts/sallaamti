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
            </div>
            @endunless

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

                <div class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-1">Referral Link</p>
                        <input type="text" readonly value="{{ url('/register?ref=' . $application->counselor_code) }}" class="text-xs border-gray-200 rounded-lg w-full bg-white" onclick="this.select()">
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
                </div>
                <p class="text-xs text-amber-700 mt-3">⚠️ Referral link attribution tracking (crediting this counselor when someone registers via this link) is not wired up yet — this link format is ready for when that's built.</p>
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
                    <div><dt class="text-gray-400">Area</dt><dd>{{ $application->area ?: '—' }}</dd></div>
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
