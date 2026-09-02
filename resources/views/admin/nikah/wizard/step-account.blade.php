<x-dynamic-component :component="$routePrefix === 'matchmaker' ? 'matchmaker-layout' : 'admin-layout'">
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route($routePrefix === 'matchmaker' ? 'matchmaker.nikah.index' : 'admin.nikah.verifications') }}" class="text-gray-400 hover:text-gray-600">Nikah Profiles</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Create Profile</span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <x-wizard-progress :steps="$steps" :titles="$stepTitles" :current="$step" />

                <p class="text-sm text-gray-500 mb-6">
                    For walk-in registrants who can't create their own account — this creates their login and Nikah profile together.
                    Held to the same requirements as a self-created profile.
                </p>

                @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
                @endif

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

                <form method="POST" action="{{ route($routePrefix . '.nikah.profiles.create.step.save', 'account') }}" class="space-y-6">
                    @csrf

                    <x-nikah-section title="Login Account" icon="🔑" color="blue" description="An account is created for them — if you give an email, a password-setup link is sent right away.">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="name" value="Full Name" />
                                <x-text-input id="name" name="name" type="text" class="w-full mt-1" :value="old('name', $data['name'] ?? '')" required />
                            </div>
                            <div>
                                <x-input-label for="identifier" value="Email or Phone Number" />
                                <x-text-input id="identifier" name="identifier" type="text" class="w-full mt-1" :value="old('identifier', $data['identifier'] ?? '')" required
                                    placeholder="e.g. name@example.com or 03001234567" title="An email lets us send them a password-setup link immediately. Phone-only accounts need a login link added manually later." />
                            </div>
                            <div>
                                <x-input-label for="gender" value="Gender" />
                                @php $genderVal = old('gender', $data['gender'] ?? ''); @endphp
                                <select id="gender" name="gender" required class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="">Select</option>
                                    <option value="male" {{ $genderVal === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $genderVal === 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                        </div>
                    </x-nikah-section>

                    <div class="flex justify-end pt-2">
                        <x-primary-button>Next: Basic Info →</x-primary-button>
                    </div>
                </form>

                @if (auth()->user()->hasRole('admin'))
                <form method="POST" action="{{ route($routePrefix . '.nikah.profiles.create.step.skip', 'account') }}" class="text-right mt-2">
                    @csrf
                    <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 underline">Skip (admin preview — no data saved)</button>
                </form>
                @endif

            </div>
        </div>
    </div>
</x-dynamic-component>
