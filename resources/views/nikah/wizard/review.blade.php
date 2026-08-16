<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('db.Review Your Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <x-wizard-progress :steps="$steps" :titles="$stepTitles" current="verification" />

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 text-red-700 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <p class="text-sm text-gray-500 mb-6">{{ __('db.Please review everything below before submitting. You can go back to any step to make changes.') }}</p>

                @php
                $sections = [
                    'basic' => ['Age' => 'age', 'Height' => 'height', 'Marital Status' => 'marital_status', 'Education' => 'education', 'Profession' => 'profession', 'City' => 'city', 'Country' => 'country'],
                    'family' => ['Caste' => 'caste', 'Family Type' => 'family_type', 'Ethnicity' => 'ethnicity', 'Language' => 'language', 'Guardian Name' => 'guardian_name', 'Guardian Contact' => 'guardian_contact', 'Guardian Relation' => 'guardian_relation'],
                    'deen' => ['Sect' => 'sect', 'Prayer Frequency' => 'prayer_frequency', 'Hijab/Beard' => 'hijab_or_beard', 'Smokes' => 'smokes', 'Diet' => 'diet', 'Open to Polygamy' => 'open_to_polygamy'],
                    'about' => ['About' => 'about', 'Expectations' => 'expectations', 'Preferred Age Range' => null, 'Preferred City' => 'pref_city', 'Preferred Sect' => 'pref_sect', 'Preferred Education' => 'pref_education', 'Preferred Marital Status' => 'pref_marital_status'],
                    'verification' => ['CNIC Number' => 'cnic_number', 'Visibility' => 'visibility'],
                ];
                @endphp

                <div class="space-y-6">
                    @foreach ($sections as $step => $fields)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-semibold text-gray-700">{{ $stepTitles[$step] }}</h3>
                            <a href="{{ route('nikah.create.step', $step) }}" class="text-xs text-teal-700 hover:underline">{{ __('db.Edit') }}</a>
                        </div>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            @foreach ($fields as $label => $key)
                            <div>
                                <dt class="text-gray-400 text-xs">{{ __('db.' . $label) }}</dt>
                                <dd class="text-gray-800">
                                    @if ($label === 'Preferred Age Range')
                                        {{ $data['pref_min_age'] ?? '—' }} – {{ $data['pref_max_age'] ?? '—' }}
                                    @elseif ($key === 'open_to_polygamy')
                                        {{ !empty($data[$key]) ? __('db.Yes') : __('db.No') }}
                                    @else
                                        {{ $data[$key] ?? '—' }}
                                    @endif
                                </dd>
                            </div>
                            @endforeach
                        </dl>
                    </div>
                    @endforeach

                    <div class="border rounded-lg p-4">
                        <h3 class="font-semibold text-gray-700 mb-2">{{ __('db.Photos & Verification') }}</h3>
                        <div class="flex gap-4 text-sm text-gray-600">
                            <span>{{ !empty($data['cnic_front_image']) ? '✓' : '✗' }} {{ __('db.CNIC Front') }}</span>
                            <span>{{ !empty($data['cnic_back_image']) ? '✓' : '✗' }} {{ __('db.CNIC Back') }}</span>
                            <span>{{ !empty($data['photo']) ? '✓' : '✗' }} {{ __('db.Profile Photo') }}</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('nikah.create.finalize') }}" class="mt-6 flex justify-between">
                    @csrf
                    <a href="{{ route('nikah.create.step', 'verification') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← {{ __('db.Back') }}</a>
                    <x-primary-button>{{ __('db.Continue to Verification Payment') }} →</x-primary-button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
