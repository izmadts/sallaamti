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

                <form method="POST" action="{{ route('nikah.create.step.save', 'verification') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <x-nikah-section :title="__('db.Verification (Required)')" icon="🪪" color="rose" :description="__('db.Your CNIC will only be used for verification and is never shown publicly.')">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="cnic_number" :value="__('db.CNIC Number')" />
                                <x-text-input id="cnic_number" name="cnic_number" type="text" class="w-full mt-1" :value="old('cnic_number', $data['cnic_number'] ?? '')" required />
                                <p class="text-xs text-gray-400 mt-1">{{ __('db.Must be unique — one CNIC can only be used for one profile.') }}</p>
                            </div>
                            <div></div>
                            <div>
                                <x-input-label for="cnic_front_image" :value="__('db.CNIC Photo (Front)')" />
                                <input id="cnic_front_image" name="cnic_front_image" type="file" accept="image/*" class="w-full mt-1" {{ empty($data['cnic_front_image']) ? 'required' : '' }}>
                                @if (!empty($data['cnic_front_image']))
                                <p class="text-xs text-green-600 mt-1">✓ {{ __('db.Already uploaded — choose a file only if you want to replace it.') }}</p>
                                @endif
                            </div>
                            <div>
                                <x-input-label for="cnic_back_image" :value="__('db.CNIC Photo (Back)')" />
                                <input id="cnic_back_image" name="cnic_back_image" type="file" accept="image/*" class="w-full mt-1" {{ empty($data['cnic_back_image']) ? 'required' : '' }}>
                                @if (!empty($data['cnic_back_image']))
                                <p class="text-xs text-green-600 mt-1">✓ {{ __('db.Already uploaded — choose a file only if you want to replace it.') }}</p>
                                @endif
                            </div>
                        </div>
                        <hr class="mt-4 border-rose-200">
                        <div class="mt-4">
                            <x-input-label for="photo" :value="__('db.Your Nikah Profile Photo (private, shared only if you allow, hidden until mutual interest is accepted)')" />
                            <input id="photo" name="photo" type="file" accept="image/*" class="w-full mt-1" />
                            @if (!empty($data['photo']))
                            <p class="text-xs text-green-600 mt-1">✓ {{ __('db.Already uploaded — choose a file only if you want to replace it.') }}</p>
                            @endif
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <input type="checkbox" id="allow_photo_sharing" name="allow_photo_sharing" value="1" {{ old('allow_photo_sharing', $data['allow_photo_sharing'] ?? true) ? 'checked' : '' }} class="rounded">
                            <x-input-label for="allow_photo_sharing" :value="__('db.Allow my photo to be shared with a match after mutual interest is accepted')" />
                        </div>
                    </x-nikah-section>

                    <x-nikah-section :title="__('db.Profile Visibility')" icon="👁️" color="teal">
                        @php $vis = old('visibility', $data['visibility'] ?? 'public'); @endphp
                        <select id="visibility" name="visibility" required class="border-gray-300 rounded-md shadow-sm w-full">
                            <option value="public" {{ $vis === 'public' ? 'selected' : '' }}>{{ __('db.Public (visible in Browse Matches search after verification)') }}</option>
                            <option value="private" {{ $vis === 'private' ? 'selected' : '' }}>{{ __('db.Private (hidden from search — only visible to people you personally send your share link to)') }}</option>
                        </select>
                    </x-nikah-section>

                    <div class="flex justify-between">
                        <a href="{{ route('nikah.create.step', 'about') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← {{ __('db.Back') }}</a>
                        <x-primary-button>{{ __('db.Review & Submit') }} →</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
