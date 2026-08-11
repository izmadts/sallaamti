<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ $group->group_name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">


            {{-- Daily Link --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">Today's Class Link ({{ now()->format('d M Y') }})</h3>
                <form method="POST" action="{{ route('teacher.groups.daily-link.store', $group) }}" class="flex gap-3 items-end">
                    @csrf
                    <div class="flex-1">
                        <x-input-label value="Zoom Join URL" />
                        <x-text-input name="join_url" class="w-full mt-1" :value="$todaysLink?->join_url" required />
                    </div>
                    <div class="w-32">
                        <x-input-label value="Passcode" />
                        <x-text-input name="passcode" class="w-full mt-1" :value="$todaysLink?->passcode" />
                    </div>
                    <x-primary-button>{{ $todaysLink ? 'Update' : 'Post' }} Link</x-primary-button>
                </form>
            </div>

            {{-- Students list with assessment + report forms --}}
            @foreach ($students as $groupStudent)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h4 class="font-semibold text-gray-800">{{ $groupStudent->user->name }}</h4>
                        <p class="text-xs text-gray-500">{{ $groupStudent->user->email }} | Joined: {{ $groupStudent->joined_date->format('d M Y') }}</p>
                    </div>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">{{ ucfirst($groupStudent->status) }}</span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Assessment Form --}}
                    <div>
                        <h5 class="text-sm font-medium text-gray-600 mb-2">Record Assessment</h5>
                        <form method="POST" action="{{ route('teacher.groups.assessment.store', [$group, $groupStudent]) }}" class="space-y-2">
                            @csrf
                            <select name="type" class="border-gray-300 rounded text-sm w-full" required>
                                <option value="weekly_quiz">Weekly Quiz</option>
                                <option value="monthly_test">Monthly Test</option>
                                <option value="quarterly_exam">Quarterly Exam</option>
                                <option value="annual_exam">Annual Exam</option>
                            </select>
                            <div class="flex gap-2">
                                <input type="number" name="score" step="0.1" min="0" max="100" placeholder="Score /100" class="border-gray-300 rounded text-sm flex-1" required>
                                <input type="date" name="assessment_date" value="{{ now()->toDateString() }}" class="border-gray-300 rounded text-sm flex-1" required>
                            </div>
                            <input type="text" name="remarks" placeholder="Remarks (optional)" class="border-gray-300 rounded text-sm w-full">
                            <button class="w-full bg-blue-600 text-white text-sm py-1.5 rounded hover:bg-blue-700">Save Assessment</button>
                        </form>
                    </div>

                    {{-- Progress Report Form --}}
                    <div>
                        <h5 class="text-sm font-medium text-gray-600 mb-2">Monthly Progress Report</h5>
                        <form method="POST" action="{{ route('teacher.groups.progress-report.store', [$group, $groupStudent]) }}" class="space-y-2">
                            @csrf
                            <input type="month" name="month" value="{{ $currentMonth }}" class="border-gray-300 rounded text-sm w-full" required>
                            <div class="flex gap-2">
                                <input type="number" name="classes_attended" placeholder="Attended" min="0" class="border-gray-300 rounded text-sm w-1/2" required>
                                <input type="number" name="classes_total" placeholder="Total classes" min="0" class="border-gray-300 rounded text-sm w-1/2" required>
                            </div>
                            <input type="text" name="quran_progress" placeholder="Quran progress (e.g. reached Surah Al-Baqarah)" class="border-gray-300 rounded text-sm w-full">
                            <input type="text" name="behavior" placeholder="Behavior" class="border-gray-300 rounded text-sm w-full">
                            <input type="text" name="homework_completion" placeholder="Homework completion" class="border-gray-300 rounded text-sm w-full">
                            <textarea name="teacher_comments" placeholder="Overall comments..." rows="2" class="border-gray-300 rounded text-sm w-full"></textarea>
                            <select name="overall_rating" class="border-gray-300 rounded text-sm w-full" required>
                                <option value="excellent">Excellent</option>
                                <option value="good">Good</option>
                                <option value="average">Average</option>
                                <option value="needs_improvement">Needs Improvement</option>
                            </select>
                            <button class="w-full bg-green-600 text-white text-sm py-1.5 rounded hover:bg-green-700">Save Report</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</x-app-layout>