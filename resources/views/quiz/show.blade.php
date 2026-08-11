<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ $quiz->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">


            @if ($bestAttempt)
            <div class="bg-white rounded-lg shadow-sm p-4 text-sm">
                Best score: <strong>{{ $bestAttempt->score_percentage }}%</strong> —
                @if ($bestAttempt->passed)
                <span class="text-green-600">✅ Passed</span>
                @else
                <span class="text-red-600">Not passed yet (need {{ $quiz->passing_percentage }}%)</span>
                @endif
            </div>
            @endif

            <form method="POST" action="{{ route('quiz.submit', $course) }}" class="bg-white rounded-lg shadow-sm p-6 space-y-6">
                @csrf
                @foreach ($quiz->questions as $i => $q)
                <div>
                    <p class="font-medium text-gray-800 mb-2">{{ $i + 1 }}. {{ $q->question }}</p>
                    <div class="space-y-1">
                        @foreach ($q->options as $idx => $opt)
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="radio" name="answers[{{ $q->id }}]" value="{{ $idx }}" required>
                            {{ $opt }}
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <x-primary-button>Submit Quiz</x-primary-button>
            </form>

        </div>
    </div>
</x-app-layout>