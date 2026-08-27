<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('db.My Progress') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Child switcher — only shown when a family has more than one child --}}
            @if ($groupStudents->count() > 1)
            <div class="flex flex-wrap gap-2">
                @foreach ($groupStudents as $gs)
                <a href="{{ route('quran-live.my-progress', ['child' => $gs->id]) }}"
                    class="text-sm px-4 py-2 rounded-full font-medium {{ $gs->id === $current->id ? 'text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}"
                    @if ($gs->id === $current->id) style="background: #0d6b6b" @endif>
                    {{ $gs->admission?->student_name ?? $gs->user?->name ?? __('db.Unknown') }}
                </a>
                @endforeach
            </div>
            @endif

            {{-- Assessments --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">{{ __('db.Assessment Results') }}</h3>
                @forelse ($assessments as $a)
                <div class="flex justify-between items-center border-b py-2 last:border-0 text-sm">
                    <div>
                        <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $a->type)) }}</span>
                        <span class="text-gray-400 ml-2">{{ $a->assessment_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-gray-600">{{ $a->score }}/100</span>
                        <span class="font-bold text-lg {{ $a->grade === 'F' ? 'text-red-600' : ($a->grade >= 'B' ? 'text-green-600' : 'text-yellow-600') }}">
                            {{ $a->grade }}
                        </span>
                    </div>
                </div>
                @if ($a->remarks)
                <p class="text-xs text-gray-400 pb-2">{{ $a->remarks }}</p>
                @endif
                @empty
                <p class="text-gray-400 text-sm">{{ __('db.No assessments recorded yet.') }}</p>
                @endforelse
            </div>

            {{-- Progress Reports --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">{{ __('db.Monthly Progress Reports') }}</h3>
                @forelse ($progressReports as $report)
                <div class="border rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="font-medium">{{ \Carbon\Carbon::createFromFormat('Y-m', $report->month)->format('F Y') }}</h4>
                        <span class="text-xs px-2 py-1 rounded-full
                                {{ $report->overall_rating === 'excellent' ? 'bg-green-100 text-green-700' :
                                   ($report->overall_rating === 'good' ? 'bg-blue-100 text-blue-700' :
                                   ($report->overall_rating === 'average' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700')) }}">
                            {{ ucfirst(str_replace('_', ' ', $report->overall_rating)) }}
                        </span>
                    </div>
                    <div class="text-sm space-y-1 text-gray-600">
                        <p>{{ __('db.Attendance:') }} <strong>{{ $report->classes_attended }}/{{ $report->classes_total }} {{ __('db.classes') }}</strong></p>
                        @if ($report->quran_progress)<p>{{ __('db.Quran Progress:') }} {{ $report->quran_progress }}</p>@endif
                        @if ($report->behavior)<p>{{ __('db.Behavior:') }} {{ $report->behavior }}</p>@endif
                        @if ($report->homework_completion)<p>{{ __('db.Homework:') }} {{ $report->homework_completion }}</p>@endif
                        @if ($report->teacher_comments)
                        <p class="mt-2 italic text-gray-500">"{{ $report->teacher_comments }}"</p>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-gray-400 text-sm">{{ __('db.No progress reports yet.') }}</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>