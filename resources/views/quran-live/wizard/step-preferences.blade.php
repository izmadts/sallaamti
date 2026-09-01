<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('db.Admission — :course', ['course' => $course->title]) }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <x-wizard-progress :steps="$steps" :titles="$stepTitles" :current="$step" />

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 text-red-700 rounded text-sm">
                    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                @php
                    $existingDays = old('preferred_days', $data['preferred_days'] ?? []);
                    $levels = \App\Models\QuranLiveCourse::where('is_published', true)->orderBy('level_number')->get();
                @endphp
                <form method="POST" action="{{ route('quran-live.admission.step.save', [$course, 'preferences']) }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label :value="__('db.Preferred Class Days')" />
                        <div class="grid grid-cols-4 gap-2 mt-1 text-sm">
                            @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                            <label class="flex items-center gap-1">
                                <input type="checkbox" name="preferred_days[]" value="{{ $day }}" {{ in_array($day, $existingDays) ? 'checked' : '' }}> {{ $day }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <x-input-label :value="__('db.Preferred Time')" />
                        <p class="text-xs text-gray-400 mb-1">{{ __("db.Our academy's available class slots, shown in Pakistan Time (PKT).") }}</p>
                        @php $currentTime = old('preferred_time', $data['preferred_time'] ?? ''); @endphp
                        <select name="preferred_time" class="border-gray-300 rounded-md w-full mt-1">
                            @foreach ([
                                '12:00 PM','1:00 PM','2:00 PM','3:00 PM','4:00 PM','5:00 PM','6:00 PM',
                                '7:00 PM','8:00 PM','9:00 PM','10:00 PM','11:00 PM','12:00 AM',
                            ] as $t)
                            <option value="{{ $t }}" {{ $currentTime === $t ? 'selected' : '' }}>{{ $t }} PKT</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('db.Your Timezone')" />
                        @php $currentTz = old('timezone', $data['timezone'] ?? 'Asia/Karachi'); @endphp
                        <select name="timezone" class="border-gray-300 rounded-md w-full mt-1">
                            <option value="">{{ __('db.-- Select Timezone --') }}</option>
                            @foreach (\DateTimeZone::listIdentifiers() as $tz)
                            <option value="{{ $tz }}" {{ $currentTz === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('db.Teacher Preference')" />
                        @php $currentTeacherPref = old('teacher_preference', $data['teacher_preference'] ?? 'no_preference'); @endphp
                        <select name="teacher_preference" class="border-gray-300 rounded-md w-full mt-1" required>
                            <option value="no_preference" {{ $currentTeacherPref === 'no_preference' ? 'selected' : '' }}>{{ __('db.No Preference') }}</option>
                            <option value="male" {{ $currentTeacherPref === 'male' ? 'selected' : '' }}>{{ __('db.Male Teacher') }}</option>
                            <option value="female" {{ $currentTeacherPref === 'female' ? 'selected' : '' }}>{{ __('db.Female Teacher') }}</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('db.Which Level are you applying for?')" />
                        @php $currentLevel = old('selected_level', $data['selected_level'] ?? ''); @endphp
                        <select name="selected_level" class="border-gray-300 rounded-md w-full mt-1">
                            <option value="">{{ __('db.-- Select Level --') }}</option>
                            @foreach ($levels as $lvl)
                            <option value="{{ $lvl->title }}" {{ $currentLevel === $lvl->title ? 'selected' : '' }}>{{ $lvl->level_number }} — {{ $lvl->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('db.Previous Level Completed (if any)')" />
                        @php $currentPrevLevel = old('previous_level', $data['previous_level'] ?? ''); @endphp
                        <select name="previous_level" class="border-gray-300 rounded-md w-full mt-1">
                            <option value="">{{ __('db.None / First time') }}</option>
                            @foreach ($levels as $lvl)
                            <option value="{{ $lvl->title }}" {{ $currentPrevLevel === $lvl->title ? 'selected' : '' }}>{{ $lvl->level_number }} — {{ $lvl->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('db.Class Type Preference')" />
                        @php $currentClassType = old('class_type', $data['class_type'] ?? 'group'); @endphp
                        <select name="class_type" class="border-gray-300 rounded-md w-full mt-1">
                            <option value="group" {{ $currentClassType === 'group' ? 'selected' : '' }}>{{ __('db.Group Class') }}</option>
                            <option value="one_to_one" {{ $currentClassType === 'one_to_one' ? 'selected' : '' }}>{{ __('db.One-to-One (Private)') }}</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label :value="__('db.Special Requirements / Comments (optional)')" />
                        <textarea name="comments" rows="3" class="border-gray-300 rounded-md w-full mt-1">{{ old('comments', $data['comments'] ?? '') }}</textarea>
                    </div>

                    <div class="flex justify-between pt-2">
                        <a href="{{ route('quran-live.admission.step', [$course, 'student']) }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← {{ __('db.Back') }}</a>
                        <x-primary-button>{{ __('db.Next: Review & Confirm →') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
