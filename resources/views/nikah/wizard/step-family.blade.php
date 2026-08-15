<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('db.Create Your Nikah Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <x-wizard-progress :steps="$steps" :titles="$stepTitles" :current="$step" />

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 text-red-700 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('nikah.create.step.save', 'family') }}" class="space-y-6">
                    @csrf

                    <x-nikah-section :title="__('db.Family & Background')" icon="👨‍👩‍👧" color="amber">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="caste" :value="__('db.Caste (optional)')" />
                                <x-text-input id="caste" name="caste" type="text" class="w-full mt-1" :value="old('caste', $data['caste'] ?? '')" />
                            </div>
                            <div>
                                <x-input-label for="family_type" :value="__('db.Family Type')" />
                                <x-text-input id="family_type" name="family_type" type="text" class="w-full mt-1" :value="old('family_type', $data['family_type'] ?? '')" placeholder="Joint / Nuclear" />
                            </div>
                            <div>
                                <x-input-label for="ethnicity" :value="__('db.Ethnicity (optional)')" />
                                <x-text-input id="ethnicity" name="ethnicity" type="text" class="w-full mt-1" :value="old('ethnicity', $data['ethnicity'] ?? '')" placeholder="e.g. Punjabi, Pashtun, Sindhi" />
                            </div>
                            <div>
                                <x-input-label for="language" :value="__('db.Language(s) Spoken (optional)')" />
                                <x-text-input id="language" name="language" type="text" class="w-full mt-1" :value="old('language', $data['language'] ?? '')" placeholder="e.g. Urdu, English" />
                            </div>
                            <div>
                                <x-input-label for="guardian_relation" :value="__('db.Guardian Relation')" />
                                <x-text-input id="guardian_relation" name="guardian_relation" type="text" class="w-full mt-1" :value="old('guardian_relation', $data['guardian_relation'] ?? '')" placeholder="Father / Brother" />
                            </div>
                            <div>
                                <x-input-label for="guardian_name" :value="__('db.Guardian Name')" />
                                <x-text-input id="guardian_name" name="guardian_name" type="text" class="w-full mt-1" :value="old('guardian_name', $data['guardian_name'] ?? '')" required />
                            </div>
                            <div>
                                <x-input-label for="guardian_contact" :value="__('db.Guardian Contact Number')" />
                                <x-text-input id="guardian_contact" name="guardian_contact" type="text" class="w-full mt-1" :value="old('guardian_contact', $data['guardian_contact'] ?? '')" required />
                            </div>
                        </div>
                    </x-nikah-section>

                    <div class="flex justify-between">
                        <a href="{{ route('nikah.create.step', 'basic') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← {{ __('db.Back') }}</a>
                        <x-primary-button>{{ __('db.Next') }} →</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
