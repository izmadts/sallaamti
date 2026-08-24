<x-guest-layout title="Your Matchmaking Progress — Sallaamti" description="Check the status of your matchmaking journey with Sallaamti.">

    <div class="py-12 bg-cream">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (!$unlocked)

            <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center text-3xl mb-4" style="background: var(--teal-light);">🔒</div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Your Matchmaking Progress</h3>
                <p class="text-sm text-gray-500 mb-6">Enter the <strong>last 7 digits</strong> of the WhatsApp number your matchmaker has on file to view your status. You'll need to do this every time you visit this page — nothing is remembered on this device.</p>

                @if (($error ?? null) || $errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm text-left">
                    {{ $error ?? $errors->first() }}
                </div>
                @endif

                <form method="POST" action="{{ $verifyUrl }}" class="flex flex-col items-center gap-3">
                    @csrf
                    <input type="text" name="last7" inputmode="numeric" pattern="[0-9]{7}" maxlength="7" minlength="7" required placeholder="e.g. 3001234"
                        class="border-gray-300 rounded-lg text-center text-lg tracking-widest w-48" autofocus>
                    <button class="text-white text-sm font-semibold px-6 py-3 rounded-lg hover:opacity-90 transition" style="background: #0d6b6b">Unlock My Progress</button>
                </form>
            </div>

            @else

            <div class="rounded-xl p-4 flex items-start gap-3 bg-white border" style="border-color: #0d6b6b33">
                <span class="text-xl">💍</span>
                <p class="text-sm text-gray-700">
                    Hello {{ $lead->name }} — here's everything your matchmaker has shared so far. This page always asks for verification again next time you visit.
                </p>
            </div>

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm text-center">{{ session('status') }}</div>
            @endif

            @php $pendingConsents = $lead->consentRequests->where('status', 'pending'); @endphp
            @if ($pendingConsents->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-1">✅ Please Confirm</h4>
                <p class="text-xs text-gray-500 mb-4">Your matchmaker is asking you to confirm the following. This goes straight to Sallaamti — nothing is assumed on your behalf.</p>

                <div class="space-y-3">
                    @foreach ($pendingConsents as $req)
                    <div class="border border-gray-100 rounded-lg p-4">
                        <p class="text-sm text-gray-700 mb-3">{{ \App\Models\MatchmakingConsent::TYPES[$req->consent_type] }}</p>
                        {{-- Must be a genuinely signed URL, not a plain route() call — this
                             route sits behind the 'signed' middleware group, which checks
                             a real signature, not just a matching ?token= value. --}}
                        <form method="POST" action="{{ \Illuminate\Support\Facades\URL::signedRoute('public.matchmaking.progress.consents.respond', ['lead' => $lead->id, 'consentRequest' => $req->id, 'token' => $lead->progress_link_token]) }}" class="flex flex-wrap gap-3">
                            @csrf
                            <input type="hidden" name="last7" value="{{ $last7 ?? '' }}">
                            <button name="decision" value="grant" class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">✅ I Agree</button>
                            <button name="decision" value="decline" class="bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-semibold px-5 py-2.5 rounded-lg transition">Not Now</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if ($lead->nikahProfile && (empty($lead->nikahProfile->cnic_front_image) || empty($lead->nikahProfile->cnic_back_image) || empty($lead->nikahProfile->cnic_number)))
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-1">🪪 Upload Your Verification Documents</h4>
                <p class="text-xs text-gray-500 mb-4">Your matchmaker registered your profile but couldn't collect your CNIC/photo in person. Upload them here — this goes straight to Sallaamti for verification, never through your matchmaker's phone.</p>

                @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ $documentsUrl }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="last7" value="{{ $last7 ?? '' }}">

                    <div>
                        <label class="text-xs text-gray-500 block mb-1">CNIC Number</label>
                        <input type="text" name="cnic_number" value="{{ old('cnic_number') }}" placeholder="e.g. 12345-1234567-1" class="border-gray-300 rounded-lg w-full text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">CNIC Photo (Front)</label>
                        <input type="file" name="cnic_front_image" accept="image/*" capture="environment" class="text-sm w-full">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">CNIC Photo (Back)</label>
                        <input type="file" name="cnic_back_image" accept="image/*" capture="environment" class="text-sm w-full">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Your Photo (optional)</label>
                        <input type="file" name="photo" accept="image/*" capture="environment" class="text-sm w-full">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="allow_photo_sharing" name="allow_photo_sharing" value="1" class="rounded">
                        <label for="allow_photo_sharing" class="text-xs text-gray-600">Allow my photo to be shared with a match after mutual interest is accepted</label>
                    </div>
                    <button class="text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:opacity-90 transition" style="background: #0d6b6b">Submit for Verification</button>
                </form>
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Current Status</p>
                <span class="inline-block text-sm px-3 py-1 rounded-full font-semibold
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

            {{-- Proposal / response history --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">💌 Proposals Shared With You</h4>

                @forelse ($lead->proposalBatches->where('status', '!=', 'draft') as $batch)
                <div class="mb-4 last:mb-0 border border-gray-100 rounded-lg overflow-hidden">
                    <div class="px-3 py-2 bg-gray-50 text-xs font-semibold text-gray-600">
                        Batch #{{ $batch->batch_number }} — {{ $batch->sent_at?->format('d M Y') }}
                    </div>
                    <div class="p-3 space-y-2">
                        @foreach ($batch->proposals as $proposal)
                        <div class="flex items-center justify-between text-sm border-b last:border-0 pb-2 last:pb-0">
                            <div>
                                <p class="font-medium text-gray-800">{{ $proposal->candidate->age }} yrs, {{ $proposal->candidate->city }}</p>
                                @if ($proposal->candidate->sect)
                                <p class="text-xs text-gray-400">{{ $proposal->candidate->sect }}</p>
                                @endif
                            </div>
                            <span class="text-xs px-2 py-0.5 rounded-full
                                {{ match($proposal->response) {
                                    'interested' => 'bg-green-100 text-green-800',
                                    'not_interested' => 'bg-red-100 text-red-700',
                                    'maybe' => 'bg-amber-100 text-amber-800',
                                    default => 'bg-gray-100 text-gray-600',
                                } }}">
                                {{ $proposal->response ? ucfirst(str_replace('_', ' ', $proposal->response)) : ($proposal->status === 'viewed' ? 'Awaiting your response' : ucfirst($proposal->status)) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400">No proposals shared yet — your matchmaker will send some here once they've found a match.</p>
                @endforelse
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">🕓 Activity</h4>
                @forelse ($lead->timelineEvents as $event)
                <div class="py-2 border-b last:border-0">
                    <p class="text-sm text-gray-700">{{ $event->description }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $event->created_at->format('d M Y') }}</p>
                </div>
                @empty
                <p class="text-sm text-gray-400">No activity yet.</p>
                @endforelse
            </div>

            @endif

        </div>
    </div>
</x-guest-layout>
