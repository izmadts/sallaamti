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

                <form method="POST" action="{{ route($routePrefix . '.nikah.profiles.create.step.save', 'about') }}" class="space-y-6">
                    @csrf

                    <x-nikah-section title="About" icon="💬" color="purple">
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="about" value="About (optional)" />
                                <textarea id="about" name="about" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1">{{ old('about', $data['about'] ?? '') }}</textarea>
                            </div>
                            <div>
                                <x-input-label for="expectations" value="Looking For (optional)" />
                                <textarea id="expectations" name="expectations" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1">{{ old('expectations', $data['expectations'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </x-nikah-section>

                    <div class="flex justify-between pt-2">
                        <a href="{{ route($routePrefix . '.nikah.profiles.create.step', 'deen') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← Back</a>
                        <x-primary-button>Next: Verification →</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-dynamic-component>
