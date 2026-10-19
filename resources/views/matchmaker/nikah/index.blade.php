<x-matchmaker-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('db.Nikah Profiles') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="rounded-xl p-4 flex items-start gap-3" style="background: var(--teal-light); border: 1px solid #0d6b6b33">
                <span class="text-xl">🛡️</span>
                <p class="text-sm text-gray-700">
                    {{ __("db.For everyone's privacy, contact details, CNIC, and photo are never shown here. If you find a good match, request contact details from admin on that profile's page.") }}
                </p>
            </div>

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif

            {{-- Filters --}}
            <form method="GET" class="bg-white rounded-xl shadow-sm p-4 flex flex-wrap gap-3 items-end">
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">{{ __('db.Search by Profile #') }}</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('db.e.g. 123 or #123') }}" class="border-gray-200 rounded-lg text-sm w-32">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">{{ __('db.Verification') }}</label>
                    <select name="verification_status" class="border-gray-200 rounded-lg text-sm">
                        <option value="">{{ __('db.Any') }}</option>
                        <option value="pending" {{ request('verification_status') === 'pending' ? 'selected' : '' }}>{{ __('db.Pending') }}</option>
                        <option value="verified" {{ request('verification_status') === 'verified' ? 'selected' : '' }}>{{ __('db.Verified') }}</option>
                        <option value="rejected" {{ request('verification_status') === 'rejected' ? 'selected' : '' }}>{{ __('db.Rejected') }}</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">{{ __('db.Payment') }}</label>
                    <select name="payment_status" class="border-gray-200 rounded-lg text-sm">
                        <option value="">{{ __('db.Any') }}</option>
                        <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>{{ __('db.Unpaid') }}</option>
                        <option value="submitted" {{ request('payment_status') === 'submitted' ? 'selected' : '' }}>{{ __('db.Submitted') }}</option>
                        <option value="confirmed" {{ request('payment_status') === 'confirmed' ? 'selected' : '' }}>{{ __('db.Confirmed') }}</option>
                        <option value="rejected" {{ request('payment_status') === 'rejected' ? 'selected' : '' }}>{{ __('db.Rejected') }}</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">{{ __('db.Gender') }}</label>
                    <select name="gender" class="border-gray-200 rounded-lg text-sm">
                        <option value="">{{ __('db.Any') }}</option>
                        <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>{{ __('db.Male') }}</option>
                        <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>{{ __('db.Female') }}</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">{{ __('db.City') }}</label>
                    <input type="text" name="city" value="{{ request('city') }}" class="border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">{{ __('db.Sect') }}</label>
                    <select name="sect" class="border-gray-200 rounded-lg text-sm">
                        <option value="">{{ __('db.Any') }}</option>
                        @foreach (['Sunni', 'Shia', 'Ahle Hadith', 'Deobandi', 'Other'] as $sect)
                        <option value="{{ $sect }}" {{ request('sect') === $sect ? 'selected' : '' }}>{{ __('db.' . $sect) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">{{ __('db.Education') }}</label>
                    <select name="education" class="border-gray-200 rounded-lg text-sm">
                        <option value="">{{ __('db.Any') }}</option>
                        @foreach ($educationLevels as $level)
                        <option value="{{ $level }}" {{ request('education') === $level ? 'selected' : '' }}>{{ $level }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">{{ __('db.Marital Status') }}</label>
                    <select name="marital_status" class="border-gray-200 rounded-lg text-sm">
                        <option value="">{{ __('db.Any') }}</option>
                        @foreach (['never_married' => __('db.Never Married'), 'divorced' => __('db.Divorced'), 'widowed' => __('db.Widowed'), 'separated' => __('db.Separated'), 'married' => __('db.Married')] as $val => $label)
                        <option value="{{ $val }}" {{ request('marital_status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <input type="number" name="min_age" placeholder="{{ __('db.Min age') }}" value="{{ request('min_age') }}" class="w-24 border-gray-200 rounded-lg text-sm">
                    <input type="number" name="max_age" placeholder="{{ __('db.Max age') }}" value="{{ request('max_age') }}" class="w-24 border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">{{ __('db.Country') }}</label>
                    <select name="country" class="border-gray-200 rounded-lg text-sm">
                        <option value="">{{ __('db.Any') }}</option>
                        @foreach ($countries as $country)
                        <option value="{{ $country }}" {{ request('country') === $country ? 'selected' : '' }}>{{ $country }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">{{ __('db.State / Province') }}</label>
                    <input type="text" name="state" value="{{ request('state') }}" class="border-gray-200 rounded-lg text-sm">
                </div>
                <button class="px-4 py-2 rounded-lg text-white text-sm font-semibold" style="background: #0d6b6b">{{ __('db.Filter') }}</button>
                @if (request()->hasAny(['search', 'verification_status', 'payment_status', 'gender', 'city', 'sect', 'education', 'marital_status', 'min_age', 'max_age', 'country', 'state']))
                <a href="{{ route('matchmaker.nikah.index') }}" class="text-sm text-gray-400 hover:text-gray-600">✕ {{ __('db.Clear') }}</a>
                @endif
            </form>

            {{-- No Name/Email/Phone column, unlike admin's equivalent table
                 — those stay hidden here regardless of filters, same as the
                 card layout this replaced never showed them either. --}}
            <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">{{ __('db.Profile') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('db.Age / Gender') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('db.City') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('db.Sect') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('db.Marital Status') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('db.Education') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('db.Verification') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('db.Payment') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('db.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($profiles as $profile)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-xs font-mono text-gray-400">#{{ $profile->id }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $profile->age }} / {{ ucfirst($profile->user?->gender ?? '—') }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $profile->city ?: '—' }}{{ $profile->country ? ', ' . $profile->country : '' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $profile->sect ?: '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ ucfirst(str_replace('_', ' ', $profile->marital_status ?? '—')) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $profile->education ?: '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full
                                        {{ $profile->verification_status === 'verified' ? 'bg-green-100 text-green-800' :
                                           ($profile->verification_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($profile->verification_status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full
                                        {{ $profile->payment_status === 'confirmed' ? 'bg-green-100 text-green-800' :
                                           ($profile->payment_status === 'submitted' ? 'bg-yellow-100 text-yellow-800' :
                                           ($profile->payment_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600')) }}">
                                    {{ ucfirst($profile->payment_status ?? 'unpaid') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('matchmaker.nikah.show', $profile) }}" class="text-xs text-blue-600 hover:underline whitespace-nowrap">{{ __('db.🔍 View') }}</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-400">{{ __('db.No profiles match those filters.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $profiles->links() }}

        </div>
    </div>
</x-matchmaker-layout>
