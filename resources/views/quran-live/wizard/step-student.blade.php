<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Admission — {{ $course->title }}</h2>
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

                <form method="POST" action="{{ route('quran-live.admission.step.save', [$course, 'student']) }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label value="Student Full Name" />
                        <x-text-input name="student_name" class="w-full mt-1" :value="old('student_name', $data['student_name'] ?? '')" required
                            title="If you're admitting more than one child, use each child's own name here — that's what tells them apart." />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Student Gender" />
                            <select name="student_gender" class="border-gray-300 rounded-md w-full mt-1" required>
                                <option value="">Select</option>
                                <option value="male" {{ old('student_gender', $data['student_gender'] ?? '') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('student_gender', $data['student_gender'] ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label value="Age" />
                            <x-text-input name="student_age" type="number" class="w-full mt-1" :value="old('student_age', $data['student_age'] ?? '')" required />
                        </div>
                    </div>
                    <div>
                        <x-input-label value="Current Education Grade (optional)" />
                        @php $currentGrade = old('education_grade', $data['education_grade'] ?? ''); @endphp
                        <select name="education_grade" class="border-gray-300 rounded-md w-full mt-1">
                            <option value="">-- Select Grade / Level --</option>
                            @foreach ([
                                'Pre-school / Kindergarten','Grade 1','Grade 2','Grade 3','Grade 4','Grade 5',
                                'Grade 6','Grade 7','Grade 8','Grade 9','Grade 10','O-Levels','A-Levels',
                                'Grade 11','Grade 12','Undergraduate','Graduate / Postgraduate',
                                'Homeschooled','Not currently in school','Other',
                            ] as $grade)
                            <option value="{{ $grade }}" {{ $currentGrade === $grade ? 'selected' : '' }}>{{ $grade }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="learned_quran_before" value="1" id="learned_quran_before" {{ old('learned_quran_before', $data['learned_quran_before'] ?? false) ? 'checked' : '' }}>
                        <label for="learned_quran_before" class="text-sm">Student has learned Quran before</label>
                    </div>

                    <div class="flex justify-between pt-2">
                        <a href="{{ route('quran-live.admission.step', [$course, 'parent']) }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← Back</a>
                        <x-primary-button>Next: Class Preferences →</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
