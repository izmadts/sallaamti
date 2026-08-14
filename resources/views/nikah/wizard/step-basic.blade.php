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

                <form method="POST" action="{{ route('nikah.create.step.save', 'basic') }}" class="space-y-6">
                    @csrf

                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">{{ __('db.Basic Information') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="age" :value="__('db.Age')" />
                                <x-text-input id="age" name="age" type="number" class="w-full mt-1" :value="old('age', $data['age'] ?? '')" required />
                            </div>
                            <div>
                                <x-input-label for="height" value="Height (e.g. 5'8&quot;)" />
                                <x-text-input id="height" name="height" type="text" class="w-full mt-1" :value="old('height', $data['height'] ?? '')" />
                            </div>
                            <div>
                                <x-input-label for="marital_status" :value="__('db.Marital Status')" />
                                @php $ms = old('marital_status', $data['marital_status'] ?? 'never_married'); @endphp
                                <select id="marital_status" name="marital_status" required class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="never_married" {{ $ms === 'never_married' ? 'selected' : '' }}>{{ __('db.Never Married') }}</option>
                                    <option value="divorced" {{ $ms === 'divorced' ? 'selected' : '' }}>{{ __('db.Divorced') }}</option>
                                    <option value="widowed" {{ $ms === 'widowed' ? 'selected' : '' }}>{{ __('db.Widowed') }}</option>
                                    <option value="separated" {{ $ms === 'separated' ? 'selected' : '' }}>{{ __('db.Separated') }}</option>
                                    <option value="married" {{ $ms === 'married' ? 'selected' : '' }}>{{ __('db.Married') }}</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="education" :value="__('db.Education')" />
                                <x-text-input id="education" name="education" type="text" class="w-full mt-1" :value="old('education', $data['education'] ?? '')" />
                            </div>
                            <div>
                                <x-input-label for="profession" :value="__('db.Profession')" />
                                <x-text-input id="profession" name="profession" type="text" class="w-full mt-1" :value="old('profession', $data['profession'] ?? '')" />
                            </div>
                            <div>
                                <x-input-label for="city" :value="__('db.City')" />
                                <x-text-input id="city" name="city" type="text" class="w-full mt-1" :value="old('city', $data['city'] ?? '')" required />
                            </div>
                            <div>
                                <x-input-label for="country" :value="__('db.Country')" />
                                <x-text-input id="country" name="country" type="text" class="w-full mt-1" :value="old('country', $data['country'] ?? 'Pakistan')" />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>{{ __('db.Next') }} →</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
