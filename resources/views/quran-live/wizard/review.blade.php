<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Admission — {{ $course->title }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <x-wizard-progress :steps="$steps" :titles="$stepTitles" current="preferences" />

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 text-red-700 rounded text-sm">
                    <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <p class="text-sm text-gray-500 mb-6">Please review the admission details before submitting.</p>

                <div class="space-y-4">
                    <div class="border rounded-lg p-4 flex justify-between items-start">
                        <div>
                            <p class="text-xs text-gray-400">Parent / Guardian</p>
                            <p class="text-sm text-gray-800 font-medium">{{ $data['guardian_name'] }}</p>
                            <p class="text-sm text-gray-600">{{ $data['whatsapp_number'] }}</p>
                            <p class="text-sm text-gray-600">{{ $data['city_state'] ? $data['city_state'] . ', ' : '' }}{{ $data['country'] }}</p>
                        </div>
                        <a href="{{ route('quran-live.admission.step', [$course, 'parent']) }}" class="text-xs text-teal-700 hover:underline">Edit</a>
                    </div>

                    <div class="border rounded-lg p-4 flex justify-between items-start">
                        <div>
                            <p class="text-xs text-gray-400">Student</p>
                            <p class="text-sm text-gray-800 font-medium">{{ $data['student_name'] }} — {{ ucfirst($data['student_gender']) }}, {{ $data['student_age'] }} yrs</p>
                            @if (!empty($data['education_grade']))
                            <p class="text-sm text-gray-600">Grade: {{ $data['education_grade'] }}</p>
                            @endif
                            <p class="text-sm text-gray-600">{{ !empty($data['learned_quran_before']) ? 'Has learned Quran before' : 'New to Quran learning' }}</p>
                        </div>
                        <a href="{{ route('quran-live.admission.step', [$course, 'student']) }}" class="text-xs text-teal-700 hover:underline">Edit</a>
                    </div>

                    <div class="border rounded-lg p-4 flex justify-between items-start">
                        <div>
                            <p class="text-xs text-gray-400">Class Preferences</p>
                            <p class="text-sm text-gray-800">Days: {{ !empty($data['preferred_days']) ? implode(', ', $data['preferred_days']) : 'Any' }}</p>
                            <p class="text-sm text-gray-800">Time: {{ $data['preferred_time'] ?? '—' }} @if (!empty($data['timezone'])) ({{ $data['timezone'] }}) @endif</p>
                            <p class="text-sm text-gray-800">Teacher: {{ ucfirst(str_replace('_', ' ', $data['teacher_preference'] ?? 'no_preference')) }}</p>
                            <p class="text-sm text-gray-800">Class Type: {{ $data['class_type'] === 'one_to_one' ? 'One-to-One (Private)' : 'Group Class' }}</p>
                            @if (!empty($data['selected_level']))
                            <p class="text-sm text-gray-800">Level: {{ $data['selected_level'] }}</p>
                            @endif
                            @if (!empty($data['comments']))
                            <p class="text-sm text-gray-600 mt-1 italic">"{{ $data['comments'] }}"</p>
                            @endif
                        </div>
                        <a href="{{ route('quran-live.admission.step', [$course, 'preferences']) }}" class="text-xs text-teal-700 hover:underline">Edit</a>
                    </div>
                </div>

                <form method="POST" action="{{ route('quran-live.admission.finalize', $course) }}" class="mt-6">
                    @csrf
                    <div class="bg-gray-50 border rounded p-4 flex items-start gap-2 mb-4">
                        <input type="checkbox" name="declaration_accepted" value="1" id="declaration_accepted" required class="mt-1">
                        <label for="declaration_accepted" class="text-sm text-gray-600">I, as parent/guardian, confirm the above information is accurate and I consent to my child's/my enrollment in this online Quran class with Sallaamti.</label>
                    </div>
                    <div class="flex justify-between">
                        <a href="{{ route('quran-live.admission.step', [$course, 'preferences']) }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← Back</a>
                        <x-primary-button>Submit Admission</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
