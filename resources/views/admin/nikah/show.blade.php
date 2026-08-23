<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.nikah.verifications') }}" class="text-gray-400 hover:text-gray-600">Nikah Profiles</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">{{ $profile->user?->name ?? 'Deleted account' }}</span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Header --}}
            <div class="bg-white rounded-xl shadow-sm p-6 flex flex-wrap justify-between items-start gap-4">
                <div class="flex gap-4">
                    @if ($profile->photo)
                    <img src="{{ route('nikah.file', [$profile, 'photo']) }}" class="w-24 h-24 object-cover rounded-lg border">
                    @else
                    <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs">No Photo</div>
                    @endif
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $profile->user?->name ?? 'Deleted account' }}</h2>
                        <p class="text-sm text-gray-500">{{ $profile->user?->email ?: '— no email —' }} · {{ $profile->user?->phone ?: '— no phone —' }}</p>
                        <p class="text-sm text-gray-500">{{ $profile->age }} yrs, {{ ucfirst($profile->user?->gender ?? '—') }}, {{ $profile->city }}, {{ $profile->country }}</p>
                        <p class="text-xs text-gray-400 mt-1">Profile created {{ $profile->created_at->format('d M Y') }} @if ($profile->user) · Account created {{ $profile->user->created_at->format('d M Y') }} @endif</p>
                        @if ($profile->isWalkIn())
                        <p class="text-xs text-amber-700 mt-0.5">🚶 Walk-in — entered by {{ $profile->createdBy?->name ?? 'a staff member (account since removed)' }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="text-xs px-3 py-1 rounded-full font-semibold
                            {{ $profile->verification_status === 'verified' ? 'bg-green-100 text-green-800' :
                               ($profile->verification_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($profile->verification_status) }}
                    </span>
                    <div class="flex gap-2">
                        @if ($profile->verification_status !== 'verified')
                        <form method="POST" action="{{ route('admin.nikah.approve', $profile) }}">
                            @csrf
                            <button class="bg-green-600 text-white text-xs px-3 py-1.5 rounded hover:bg-green-700"
                                {{ $profile->payment_status !== 'confirmed' ? 'disabled title=Payment+must+be+confirmed+first' : '' }}>
                                Approve
                            </button>
                        </form>
                        @endif
                        @if ($profile->verification_status !== 'rejected')
                        <button type="button" onclick="document.getElementById('reject-form').classList.toggle('hidden')"
                            class="bg-red-600 text-white text-xs px-3 py-1.5 rounded hover:bg-red-700">Reject</button>
                        @endif
                    </div>
                </div>
            </div>

            <form id="reject-form" method="POST" action="{{ route('admin.nikah.reject', $profile) }}" class="hidden bg-white rounded-xl shadow-sm p-4 flex gap-2">
                @csrf
                <input type="text" name="rejection_reason" placeholder="Reason for rejection" class="flex-1 border-gray-300 rounded text-sm px-3" required>
                <button class="bg-red-600 text-white text-sm px-4 py-2 rounded hover:bg-red-700">Confirm Reject</button>
            </form>

            @if ($profile->rejection_reason)
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                <strong>Rejection reason on file:</strong> {{ $profile->rejection_reason }}
            </div>
            @endif

            {{-- Shareable profile summary --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-1 border-b pb-2">📤 Share Profile Summary</h4>
                <p class="text-xs text-gray-400 mt-2 mb-3">
                    A ready-to-paste profile card for WhatsApp groups, Messenger or anywhere else — missing fields show as ***** instead of being left blank.
                    Guardian contact, CNIC and payment details are never included.
                </p>

                @unless ($profile->verification_status === 'verified' && $profile->is_active && !$profile->isSuspended())
                <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mb-3">
                    ⚠️ The profile link below will only work once this profile is verified and active.
                </p>
                @endunless

                <textarea id="nikah-share-text" readonly rows="6"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs font-mono text-gray-600 focus:outline-none focus:border-teal-500">{{ $profile->shareSummary() }}</textarea>

                <div class="flex gap-2 flex-wrap mt-3">
                    <button type="button" onclick="nikahCopyShareSummary(this)"
                        class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 text-sm px-4 py-2 rounded-lg hover:bg-gray-200">
                        📋 Copy Summary
                    </button>
                    <a href="https://wa.me/?text={{ urlencode($profile->shareSummary()) }}" target="_blank"
                        class="inline-flex items-center gap-2 bg-green-500 text-white text-sm px-4 py-2 rounded-lg hover:bg-green-600">
                        💬 WhatsApp
                    </a>
                    <button type="button" onclick="nikahShareSummary()"
                        class="inline-flex items-center gap-2 bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700">
                        📤 Share (Messenger & more)
                    </button>
                </div>
            </div>

            <script>
                function nikahCopyShareSummary(btn) {
                    const text = document.getElementById('nikah-share-text').value;
                    navigator.clipboard.writeText(text).then(() => {
                        if (btn) {
                            const original = btn.innerHTML;
                            btn.innerHTML = '✅ Copied!';
                            setTimeout(() => btn.innerHTML = original, 1500);
                        }
                    });
                }

                function nikahShareSummary() {
                    const text = document.getElementById('nikah-share-text').value;
                    if (navigator.share) {
                        navigator.share({ title: 'Sallaamti Nikah Profile', text });
                    } else {
                        nikahCopyShareSummary();
                    }
                }
            </script>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">

                    {{-- Basic & Background --}}
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Basic & Family Background</h4>
                        <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                            <div><dt class="text-gray-400">Height</dt><dd>{{ $profile->height ?: '—' }}</dd></div>
                            <div><dt class="text-gray-400">Marital Status</dt><dd>{{ ucfirst(str_replace('_', ' ', $profile->marital_status ?? '—')) }}</dd></div>
                            @if (in_array($profile->marital_status, ['divorced', 'widowed', 'separated', 'married']))
                            <div><dt class="text-gray-400">Children</dt><dd>{{ is_null($profile->has_children) ? '—' : ($profile->has_children ? 'Yes' . ($profile->children_count ? " ({$profile->children_count})" : '') : 'No') }}</dd></div>
                            <div><dt class="text-gray-400">Lives With</dt><dd>{{ $profile->living_situation ? (['alone' => 'Alone', 'with_mother' => 'Mother', 'with_father' => 'Father', 'with_maternal_grandparents' => 'Maternal Grandparents', 'with_paternal_grandparents' => 'Paternal Grandparents', 'with_children' => 'Their Children', 'other' => 'Other'][$profile->living_situation] ?? ucfirst(str_replace('_', ' ', $profile->living_situation))) : '—' }}</dd></div>
                            @endif
                            <div><dt class="text-gray-400">Sect</dt><dd>{{ $profile->sect ?: '—' }}</dd></div>
                            <div><dt class="text-gray-400">Caste</dt><dd>{{ $profile->caste ?: '—' }}</dd></div>
                            <div><dt class="text-gray-400">Education</dt><dd>{{ $profile->education ?: '—' }}</dd></div>
                            <div><dt class="text-gray-400">Profession</dt><dd>{{ $profile->profession ?: '—' }}</dd></div>
                            <div><dt class="text-gray-400">Family Type</dt><dd>{{ $profile->family_type ?: '—' }}</dd></div>
                            <div><dt class="text-gray-400">Ethnicity</dt><dd>{{ $profile->ethnicity ?: '—' }}</dd></div>
                            <div><dt class="text-gray-400">Language</dt><dd>{{ $profile->language ?: '—' }}</dd></div>
                            <div><dt class="text-gray-400">Open to Polygamy</dt><dd>{{ $profile->open_to_polygamy ? 'Yes' : 'No' }}</dd></div>
                        </dl>
                    </div>

                    {{-- Deen & Lifestyle --}}
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Deen & Lifestyle</h4>
                        <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                            <div><dt class="text-gray-400">Prayer Regularity</dt><dd>{{ $profile->prayer_frequency ? ucfirst($profile->prayer_frequency) : '—' }}</dd></div>
                            <div><dt class="text-gray-400">Hijab/Beard</dt><dd>{{ $profile->hijab_or_beard ? ucfirst($profile->hijab_or_beard) : '—' }}</dd></div>
                            <div><dt class="text-gray-400">Smoking</dt><dd>{{ $profile->smokes ? ucfirst($profile->smokes) : '—' }}</dd></div>
                            <div><dt class="text-gray-400">Diet</dt><dd>{{ $profile->diet ? ucfirst(str_replace('_', ' ', $profile->diet)) : '—' }}</dd></div>
                        </dl>
                    </div>

                    {{-- About & Expectations --}}
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">About</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $profile->about ?: '— not filled in —' }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Looking For / Expectations</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $profile->expectations ?: '— not filled in —' }}</p>
                    </div>

                    {{-- Partner Preferences --}}
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Partner Preferences</h4>
                        <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                            <div><dt class="text-gray-400">Age Range</dt><dd>{{ $profile->pref_min_age ?: '—' }} – {{ $profile->pref_max_age ?: '—' }}</dd></div>
                            <div><dt class="text-gray-400">City</dt><dd>{{ $profile->pref_city ?: 'Any' }}</dd></div>
                            <div><dt class="text-gray-400">Sect</dt><dd>{{ $profile->pref_sect ?: 'Any' }}</dd></div>
                            <div><dt class="text-gray-400">Education</dt><dd>{{ $profile->pref_education ?: 'Any' }}</dd></div>
                            <div><dt class="text-gray-400">Marital Status</dt><dd>{{ $profile->pref_marital_status ?: 'Any' }}</dd></div>
                        </dl>
                    </div>

                    {{-- Contact Member --}}
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">📩 Contact Member</h4>
                        <p class="text-xs text-gray-400 mb-3">
                            Something missing or unclear on their profile? Send a note — it's emailed to them and also
                            shown in their in-app notifications, with a link straight to their profile edit page.
                        </p>
                        <form method="POST" action="{{ route('admin.nikah.contact', $profile) }}" class="flex gap-2">
                            @csrf
                            <textarea name="message" rows="2" required placeholder="e.g. Your CNIC photo is blurry — please re-upload a clearer image."
                                class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500"></textarea>
                            <button class="bg-teal-600 text-white text-sm px-4 py-2 rounded hover:bg-teal-700 self-end">Send</button>
                        </form>
                    </div>

                </div>

                <div class="space-y-6">

                    {{-- Guardian --}}
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Guardian</h4>
                        <dl class="text-sm space-y-2">
                            <div class="flex justify-between"><dt class="text-gray-400">Name</dt><dd>{{ $profile->guardian_name ?: '—' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-400">Relation</dt><dd>{{ $profile->guardian_relation ?: '—' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-400">Contact</dt><dd>{{ $profile->guardian_contact ?: '—' }}</dd></div>
                        </dl>
                        @if ($profile->isGuardianVerified())
                        <p class="text-xs text-purple-600 mt-2">✓ Verified {{ $profile->guardian_verified_at->format('d M Y') }}</p>
                        @else
                        <form method="POST" action="{{ route('admin.nikah.verify-guardian', $profile) }}" class="mt-2">
                            @csrf
                            <button class="text-xs text-purple-600 hover:underline">Mark Guardian Verified</button>
                        </form>
                        @endif
                    </div>

                    {{-- CNIC --}}
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">CNIC</h4>
                        <p class="text-sm text-gray-600 mb-3">{{ $profile->cnic_number ?: '— not provided —' }}</p>
                        <div class="flex gap-2 flex-wrap">
                            @if ($profile->cnic_front_image)
                            <a href="{{ route('nikah.file', [$profile, 'cnic_front_image']) }}" target="_blank">
                                <img src="{{ route('nikah.file', [$profile, 'cnic_front_image']) }}" class="w-32 rounded border hover:opacity-80">
                            </a>
                            @endif
                            @if ($profile->cnic_back_image)
                            <a href="{{ route('nikah.file', [$profile, 'cnic_back_image']) }}" target="_blank">
                                <img src="{{ route('nikah.file', [$profile, 'cnic_back_image']) }}" class="w-32 rounded border hover:opacity-80">
                            </a>
                            @endif
                            @if (!$profile->cnic_front_image && !$profile->cnic_back_image)
                            <p class="text-xs text-gray-400">No CNIC images uploaded.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Payment --}}
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Verification Fee Payment</h4>
                        <dl class="text-sm space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-gray-400">Status</dt>
                                <dd class="font-medium">
                                    {{ ucfirst($profile->payment_status ?? 'unpaid') }}
                                    @if ($profile->fee_waived)
                                    <span class="text-xs px-1.5 py-0.5 rounded bg-purple-100 text-purple-700 ml-1">Waived{{ $profile->feeWaivedBy ? ' by ' . $profile->feeWaivedBy->name : '' }}</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between"><dt class="text-gray-400">Amount</dt><dd>{{ $profile->payment_amount ? 'Rs. ' . number_format($profile->payment_amount) : '—' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-400">Method</dt><dd>{{ $profile->payment_method ?: '—' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-400">Reference</dt><dd>{{ $profile->payment_reference ?: '—' }}</dd></div>
                            @if ($profile->fee_waived && $profile->fee_waived_reason)
                            <div class="flex justify-between"><dt class="text-gray-400">Waiver Reason</dt><dd>{{ $profile->fee_waived_reason }}</dd></div>
                            @endif
                        </dl>
                        @if ($profile->payment_screenshot)
                        <a href="{{ route('nikah.file', [$profile, 'payment_screenshot']) }}" target="_blank" class="block mt-3">
                            <img src="{{ route('nikah.file', [$profile, 'payment_screenshot']) }}" class="w-full rounded border hover:opacity-80">
                        </a>
                        @endif
                        @if ($profile->payment_status === 'submitted')
                        <a href="{{ route('admin.nikah.payments', $profile) }}" class="block text-center mt-3 bg-yellow-500 text-white text-sm px-4 py-2 rounded hover:bg-yellow-600">
                            💳 Review Payment
                        </a>
                        @endif

                        @if (in_array($profile->payment_status, ['unpaid', 'rejected']))
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 mb-1">📲 Received Payment Another Way?</p>
                            <p class="text-[11px] text-gray-400 mb-2">
                                If the member sent their receipt on WhatsApp, in person, or any other channel, record it here to confirm their payment directly — no need to make them use the online form.
                            </p>
                            <form method="POST" action="{{ route('admin.nikah.payments.record-offline', $profile) }}" enctype="multipart/form-data" class="space-y-2">
                                @csrf
                                <select name="payment_method" required class="w-full border-gray-300 rounded text-xs px-2 py-1.5">
                                    <option value="whatsapp">Received via WhatsApp</option>
                                    <option value="cash">Cash</option>
                                    <option value="jazzcash">JazzCash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="other">Other</option>
                                </select>
                                <input type="text" name="payment_reference" placeholder="Reference / note (optional)" class="w-full border-gray-300 rounded text-xs px-2 py-1.5">
                                <input type="file" name="payment_screenshot" accept="image/*" class="w-full text-xs" title="Optional — attach the screenshot they sent you, if you have one.">
                                <button class="w-full bg-teal-600 text-white text-xs px-3 py-1.5 rounded hover:bg-teal-700"
                                    onclick="return confirm('Confirm this payment was received and mark verification fee as paid for {{ $profile->user?->name ?? 'this profile' }}?')">
                                    ✅ Confirm Payment Received
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>

                    {{-- Moderation Notes --}}
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">📝 Moderation Notes ({{ $profile->moderationNotes->count() }})</h4>
                        <div class="space-y-2 mb-3">
                            @forelse ($profile->moderationNotes as $note)
                            <div class="bg-gray-50 rounded p-2 text-xs text-gray-600">
                                <span class="font-semibold">{{ $note->admin?->name ?? 'Deleted admin' }}</span>
                                <span class="text-gray-400">{{ $note->created_at->format('d M Y, h:i A') }}</span>
                                <p class="mt-0.5">{{ $note->note }}</p>
                            </div>
                            @empty
                            <p class="text-xs text-gray-400">No notes yet.</p>
                            @endforelse
                        </div>
                        <form method="POST" action="{{ route('admin.nikah.notes.store', $profile) }}" class="flex gap-2">
                            @csrf
                            <input type="text" name="note" placeholder="Internal note (not visible to member)" class="flex-1 border-gray-300 rounded text-xs px-2 py-1" required>
                            <button class="bg-gray-600 text-white text-xs px-3 py-1 rounded hover:bg-gray-700">Add</button>
                        </form>
                    </div>

                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                <h4 class="font-semibold text-red-800 mb-1">⚠️ Danger Zone</h4>
                <p class="text-xs text-red-700 mb-3">
                    Permanently wipes this Nikah profile and everything tied to it — photos, CNIC images, payment screenshot, interests, saved/blocked lists, reports, and moderation notes.
                    The member's account itself is not affected; they can start a fresh Nikah profile afterward. This cannot be undone.
                </p>
                <form method="POST" action="{{ route('admin.nikah.destroy', $profile) }}"
                    onsubmit="return confirm('Permanently delete the Nikah profile and ALL associated data for {{ $profile->user?->name ?? 'this profile' }}? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button class="bg-red-600 text-white text-sm px-4 py-2 rounded hover:bg-red-700">
                        🗑️ Permanently Delete Nikah Profile
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-admin-layout>
