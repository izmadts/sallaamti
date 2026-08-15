<x-guest-layout :title="$profile->age . ' years, ' . $profile->city . ' — Sallaamti Nikah'" :description="'Verified Nikah profile on Sallaamti: ' . $profile->age . ' years old, ' . $profile->city . ', ' . ucfirst(str_replace('_', ' ', $profile->marital_status)) . '. Sign up to view the full profile.'">

    <div class="py-12 bg-cream">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="h-40 bg-gradient-to-br {{ $profile->user->gender === 'female' ? 'from-pink-100 to-rose-200' : 'from-blue-100 to-indigo-200' }} relative flex items-center justify-center">
                    <div class="w-28 h-28 rounded-full bg-white shadow-lg flex items-center justify-center text-4xl border-4 border-white">
                        {{ $profile->user->gender === 'female' ? '👩' : '👨' }}
                    </div>
                    <div class="absolute top-2 left-1">
                        <span class="flex items-center gap-1 bg-green-500 text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow">
                            ✅ Verified Profile
                        </span>
                    </div>
                </div>

                <div class="p-6 text-center">
                    <h3 class="text-xl font-bold text-gray-800">
                        {{ $profile->age }} years, {{ $profile->city }}
                        @if ($profile->country && $profile->country !== 'Pakistan') · {{ $profile->country }} @endif
                    </h3>
                    <p class="text-gray-500 mt-1">
                        {{ ucfirst(str_replace('_', ' ', $profile->marital_status)) }}
                        @if ($profile->sect) · {{ $profile->sect }} @endif
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Details</h4>
                <dl class="text-sm space-y-2">
                    @if ($profile->height)
                    <div class="flex justify-between"><dt class="text-gray-500">Height</dt><dd>{{ $profile->height }}</dd></div>
                    @endif
                    @if ($profile->education)
                    <div class="flex justify-between"><dt class="text-gray-500">Education</dt><dd>{{ $profile->education }}</dd></div>
                    @endif
                    @if ($profile->profession)
                    <div class="flex justify-between"><dt class="text-gray-500">Profession</dt><dd>{{ $profile->profession }}</dd></div>
                    @endif
                    @if ($profile->family_type)
                    <div class="flex justify-between"><dt class="text-gray-500">Family Type</dt><dd>{{ $profile->family_type }}</dd></div>
                    @endif
                </dl>

                @if ($profile->about)
                <div class="mt-4 pt-4 border-t">
                    <h5 class="font-semibold text-gray-700 mb-2 text-sm">About</h5>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ $profile->about }}</p>
                </div>
                @endif
            </div>

            {{-- Sign-up / show-interest CTA --}}
            @auth
            <div class="rounded-xl p-6 text-center" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
                <p class="text-white font-semibold mb-3">💍 View the full profile, photos & show your interest</p>
                <a href="{{ route('nikah.profile.view', $profile) }}" class="inline-block bg-[--gold] text-white text-sm font-semibold px-6 py-3 rounded-lg" style="background:#b8962e">
                    View Full Profile →
                </a>
            </div>
            @else
            <div class="rounded-xl p-6 text-center" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
                <p class="text-white font-semibold mb-3">🔒 Sign up on Sallaamti to see the full profile, photos & express interest</p>
                <div class="flex gap-3 justify-center flex-wrap">
                    <a href="{{ route('register') }}" class="inline-block bg-[--gold] text-white text-sm font-semibold px-6 py-3 rounded-lg" style="background:#b8962e">
                        Join Free →
                    </a>
                    <a href="{{ route('login') }}" class="inline-block border border-white/50 text-white text-sm font-semibold px-6 py-3 rounded-lg hover:bg-white/10">
                        {{ __('db.Login') }}
                    </a>
                </div>
            </div>
            @endauth

            {{-- Share --}}
            <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                <h5 class="font-semibold text-gray-700 mb-3 text-sm">Share this profile</h5>
                <div class="flex gap-2 justify-center flex-wrap">
                    <a href="https://wa.me/?text={{ urlencode('Check out this profile on Sallaamti: ' . url()->current()) }}" target="_blank"
                        class="inline-flex items-center gap-2 bg-green-500 text-white text-sm px-4 py-2 rounded-lg hover:bg-green-600">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <button type="button" onclick="sallaamtiShareProfile()"
                        class="inline-flex items-center gap-2 bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700">
                        <i class="fa fa-share-alt"></i> Share
                    </button>
                    <button type="button" onclick="sallaamtiCopyProfileLink(this)"
                        class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 text-sm px-4 py-2 rounded-lg hover:bg-gray-200">
                        🔗 Copy Link
                    </button>
                </div>
            </div>

        </div>
    </div>

    <script>
        function sallaamtiShareProfile() {
            const url = window.location.href;
            if (navigator.share) {
                navigator.share({ title: 'Sallaamti Nikah Profile', text: 'Check out this profile on Sallaamti', url });
            } else {
                sallaamtiCopyProfileLink();
            }
        }

        function sallaamtiCopyProfileLink(btn) {
            navigator.clipboard.writeText(window.location.href).then(() => {
                if (btn) {
                    const original = btn.innerHTML;
                    btn.innerHTML = '✅ Copied!';
                    setTimeout(() => btn.innerHTML = original, 1500);
                }
            });
        }
    </script>

</x-guest-layout>
