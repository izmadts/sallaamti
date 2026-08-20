<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.nikah.verifications') }}" class="text-gray-400 hover:text-gray-600">Nikah Profiles</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Create Profile</span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <x-wizard-progress :steps="$steps" :titles="$stepTitles" current="verification" />

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <p class="font-medium text-sm mb-1">Please fix the following:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <p class="text-sm text-gray-500 mb-6">Please review the profile details before creating the account.</p>

                <div class="space-y-4">
                    <div class="border rounded-lg p-4 flex justify-between items-start">
                        <div>
                            <p class="text-xs text-gray-400">Login Account</p>
                            <p class="text-sm text-gray-800 font-medium">{{ $data['name'] }} — {{ ucfirst($data['gender']) }}</p>
                            <p class="text-sm text-gray-600">{{ $data['identifier'] }}</p>
                        </div>
                        <a href="{{ route('admin.nikah.profiles.create.step', 'account') }}" class="text-xs text-teal-700 hover:underline">Edit</a>
                    </div>

                    <div class="border rounded-lg p-4 flex justify-between items-start">
                        <div>
                            <p class="text-xs text-gray-400">Basic Info</p>
                            <p class="text-sm text-gray-800">Age: {{ $data['age'] }} @if (!empty($data['height'])) &middot; Height: {{ $data['height'] }} @endif</p>
                            <p class="text-sm text-gray-800">{{ ucfirst(str_replace('_', ' ', $data['marital_status'])) }}</p>
                            @if (!empty($data['education']) || !empty($data['profession']))
                            <p class="text-sm text-gray-600">{{ $data['education'] ?? '' }}{{ !empty($data['education']) && !empty($data['profession']) ? ' — ' : '' }}{{ $data['profession'] ?? '' }}</p>
                            @endif
                            <p class="text-sm text-gray-600">{{ $data['city'] }}, {{ $data['state'] ? $data['state'] . ', ' : '' }}{{ $data['country'] }}</p>
                        </div>
                        <a href="{{ route('admin.nikah.profiles.create.step', 'basic') }}" class="text-xs text-teal-700 hover:underline">Edit</a>
                    </div>

                    <div class="border rounded-lg p-4 flex justify-between items-start">
                        <div>
                            <p class="text-xs text-gray-400">Family & Guardian</p>
                            <p class="text-sm text-gray-800">Guardian: {{ $data['guardian_name'] }} ({{ $data['guardian_contact'] }})</p>
                            @if (!empty($data['guardian_relation']))
                            <p class="text-sm text-gray-600">Relation: {{ $data['guardian_relation'] }}</p>
                            @endif
                            @if (!empty($data['family_type']) || !empty($data['caste']) || !empty($data['ethnicity']))
                            <p class="text-sm text-gray-600">{{ collect([$data['family_type'] ?? null, $data['caste'] ?? null, $data['ethnicity'] ?? null])->filter()->implode(' · ') }}</p>
                            @endif
                        </div>
                        <a href="{{ route('admin.nikah.profiles.create.step', 'family') }}" class="text-xs text-teal-700 hover:underline">Edit</a>
                    </div>

                    <div class="border rounded-lg p-4 flex justify-between items-start">
                        <div>
                            <p class="text-xs text-gray-400">Deen & Lifestyle</p>
                            <p class="text-sm text-gray-800">{{ $data['sect'] ?? 'Sect not specified' }}</p>
                            <p class="text-sm text-gray-600">{{ collect([
                                !empty($data['prayer_frequency']) ? 'Prayer: ' . ucfirst($data['prayer_frequency']) : null,
                                !empty($data['hijab_or_beard']) ? 'Hijab/Beard: ' . ucfirst($data['hijab_or_beard']) : null,
                                !empty($data['smokes']) ? 'Smokes: ' . ucfirst($data['smokes']) : null,
                                !empty($data['diet']) ? 'Diet: ' . ucfirst(str_replace('_', ' ', $data['diet'])) : null,
                            ])->filter()->implode(' · ') }}</p>
                            <p class="text-sm text-gray-600">{{ !empty($data['open_to_polygamy']) ? 'Open to polygamous marriage' : '' }}</p>
                        </div>
                        <a href="{{ route('admin.nikah.profiles.create.step', 'deen') }}" class="text-xs text-teal-700 hover:underline">Edit</a>
                    </div>

                    <div class="border rounded-lg p-4 flex justify-between items-start">
                        <div>
                            <p class="text-xs text-gray-400">About</p>
                            <p class="text-sm text-gray-600 italic">{{ $data['about'] ?? '—' }}</p>
                            @if (!empty($data['expectations']))
                            <p class="text-sm text-gray-600 mt-1">Looking for: {{ $data['expectations'] }}</p>
                            @endif
                        </div>
                        <a href="{{ route('admin.nikah.profiles.create.step', 'about') }}" class="text-xs text-teal-700 hover:underline">Edit</a>
                    </div>

                    <div class="border rounded-lg p-4 flex justify-between items-start">
                        <div>
                            <p class="text-xs text-gray-400">Verification & Visibility</p>
                            <p class="text-sm text-gray-800">CNIC: {{ $data['cnic_number'] }}</p>
                            <p class="text-sm text-gray-600">{{ !empty($data['cnic_front_image']) && !empty($data['cnic_back_image']) ? '✓ Front & back photos uploaded' : 'Missing CNIC photo(s)' }}</p>
                            <p class="text-sm text-gray-600">Visibility: {{ ucfirst($data['visibility']) }}</p>
                        </div>
                        <a href="{{ route('admin.nikah.profiles.create.step', 'verification') }}" class="text-xs text-teal-700 hover:underline">Edit</a>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.nikah.profiles.create.finalize') }}" class="mt-6">
                    @csrf
                    <div class="flex justify-between">
                        <a href="{{ route('admin.nikah.profiles.create.step', 'verification') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← Back</a>
                        <x-primary-button>Create Profile & Account</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-admin-layout>
