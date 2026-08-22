<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nikah Profiles</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="rounded-xl p-4 flex items-start gap-3" style="background: var(--teal-light); border: 1px solid #0d6b6b33">
                <span class="text-xl">🛡️</span>
                <p class="text-sm text-gray-700">
                    For everyone's privacy, contact details, CNIC, and photo are never shown here. If you find a good match, request contact details from admin on that profile's page.
                </p>
            </div>

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif

            {{-- Filters --}}
            <form method="GET" class="bg-white rounded-xl shadow-sm p-4 flex flex-wrap gap-3 items-end">
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Gender</label>
                    <select name="gender" class="border-gray-200 rounded-lg text-sm">
                        <option value="">Any</option>
                        <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">City</label>
                    <input type="text" name="city" value="{{ request('city') }}" class="border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Sect</label>
                    <input type="text" name="sect" value="{{ request('sect') }}" class="border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Marital Status</label>
                    <select name="marital_status" class="border-gray-200 rounded-lg text-sm">
                        <option value="">Any</option>
                        @foreach (['never_married' => 'Never Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed', 'separated' => 'Separated', 'married' => 'Married'] as $val => $label)
                        <option value="{{ $val }}" {{ request('marital_status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <input type="number" name="min_age" placeholder="Min age" value="{{ request('min_age') }}" class="w-24 border-gray-200 rounded-lg text-sm">
                    <input type="number" name="max_age" placeholder="Max age" value="{{ request('max_age') }}" class="w-24 border-gray-200 rounded-lg text-sm">
                </div>
                <button class="px-4 py-2 rounded-lg text-white text-sm font-semibold" style="background: #0d6b6b">Filter</button>
                @if (request()->hasAny(['gender', 'city', 'sect', 'marital_status', 'min_age', 'max_age']))
                <a href="{{ route('matchmaker.nikah.index') }}" class="text-sm text-gray-400 hover:text-gray-600">✕ Clear</a>
                @endif
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($profiles as $profile)
                <a href="{{ route('matchmaker.nikah.show', $profile) }}" class="block bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-semibold text-gray-800">{{ $profile->age }} yrs, {{ ucfirst($profile->user?->gender ?? '—') }}</h4>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $profile->verification_status === 'verified' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ ucfirst($profile->verification_status) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500">{{ $profile->city }}{{ $profile->country ? ', ' . $profile->country : '' }}</p>
                    <p class="text-sm text-gray-500">{{ $profile->sect ?: '—' }} · {{ ucfirst(str_replace('_', ' ', $profile->marital_status ?? '—')) }}</p>
                    <p class="text-xs text-gray-400 mt-2">{{ $profile->education ?: '—' }}</p>
                </a>
                @empty
                <div class="col-span-full text-center py-16 bg-white rounded-2xl shadow-sm">
                    <div class="text-4xl mb-2">🔍</div>
                    <p class="text-gray-500">No profiles match those filters.</p>
                </div>
                @endforelse
            </div>

            {{ $profiles->links() }}

        </div>
    </div>
</x-app-layout>
