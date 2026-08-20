<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Admission — {{ $course->title }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <x-quran-safety-card />

            <div class="bg-white rounded-lg shadow-sm p-6">
                <x-wizard-progress :steps="$steps" :titles="$stepTitles" :current="$step" />

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 text-red-700 rounded text-sm">
                    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <form method="POST" action="{{ route('quran-live.admission.step.save', [$course, 'parent']) }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label value="Parent / Guardian Full Name" />
                        <x-text-input name="guardian_name" class="w-full mt-1" :value="old('guardian_name', $data['guardian_name'] ?? '')" required />
                    </div>
                    <div>
                        <x-input-label value="WhatsApp Number (with country code)" />
                        <x-text-input name="whatsapp_number" class="w-full mt-1" placeholder="+92 3XX XXXXXXX" :value="old('whatsapp_number', $data['whatsapp_number'] ?? '')" required />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Country" />
                            @php $currentCountry = old('country', $data['country'] ?? 'Pakistan'); @endphp
                            <select name="country" class="border-gray-300 rounded-md w-full mt-1" required>
                                <option value="">-- Select Country --</option>
                                @foreach (\App\Support\CountryStates::countries() as $c)
                                <option value="{{ $c }}" {{ $currentCountry === $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label value="City / State" />
                            <x-text-input name="city_state" class="w-full mt-1" :value="old('city_state', $data['city_state'] ?? '')" />
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <x-primary-button>Next: About the Student →</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
