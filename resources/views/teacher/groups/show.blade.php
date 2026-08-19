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
            <div class="bg-white rounded-lg shadow-sm p-6" x-data="{ threadOpen: false }">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h4 class="font-semibold text-gray-800">{{ $groupStudent->admission?->student_name ?? $groupStudent->user->name }}</h4>
                        <p class="text-xs text-gray-500">Guardian account: {{ $groupStudent->user->name }} | Joined: {{ $groupStudent->joined_date->format('d M Y') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">{{ ucfirst($groupStudent->status) }}</span>
                        @if ($groupStudent->admission)
                        <button type="button" @click="threadOpen = !threadOpen" class="text-xs px-3 py-1 rounded-full border border-teal-200 text-teal-700 hover:bg-teal-50">
                            💬 Message<span x-show="!threadOpen">{{ $groupStudent->admission->messages->isNotEmpty() ? ' (' . $groupStudent->admission->messages->count() . ')' : '' }}</span>
                        </button>
                        @endif
                    </div>
                </div>

                @if ($groupStudent->admission)
                <div x-show="threadOpen" x-cloak class="mb-5 rounded-xl border border-gray-100 p-4 space-y-3" style="background: var(--cream)">
                    @forelse ($groupStudent->admission->messages as $message)
                    @php $isMe = $message->sender_id === auth()->id(); @endphp
                    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-md rounded-2xl px-4 py-2.5 text-sm {{ $isMe ? 'bg-teal-600 text-white' : 'bg-white text-gray-800 shadow-sm' }}">
                            <p class="font-semibold text-xs mb-0.5 {{ $isMe ? 'text-teal-100' : 'text-gray-400' }}">{{ $isMe ? 'You' : ($message->sender->name ?? 'Guardian') }}</p>
                            <p class="leading-relaxed">{{ $message->message }}</p>
                            <p class="text-xs mt-1 {{ $isMe ? 'text-teal-200' : 'text-gray-400' }}">{{ $message->created_at->format('d M, h:i A') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-2">No messages yet — say hello!</p>
                    @endforelse

                    <form method="POST" action="{{ route('teacher.students.message', $groupStudent) }}" class="flex gap-2 pt-1">
                        @csrf
                        <input type="text" name="message" required maxlength="2000" placeholder="Message the guardian..." class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500">
                        <button class="px-4 py-2 rounded-lg text-white text-sm font-semibold" style="background: #0d6b6b">Send</button>
                    </form>
                </div>
                @endif

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