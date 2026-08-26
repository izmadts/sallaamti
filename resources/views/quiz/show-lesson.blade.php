<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ $quiz->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">


            @if ($bestAttempt)
            <div class="rounded-2xl p-5 text-center" style="background: {{ $bestAttempt->passed ? '#f0fdf4' : 'var(--teal-light)' }}; border: 2px solid {{ $bestAttempt->passed ? '#86efac' : 'var(--teal)' }}">
                @if ($bestAttempt->passed)
                <p class="text-2xl mb-1" style="animation: celebrate-bounce 0.6s ease">🎉</p>
                <p class="font-bold text-green-700">{{ __('db.Great job! You passed with :percentage%!', ['percentage' => $bestAttempt->score_percentage]) }}</p>
                @else
                <p class="text-2xl mb-1">💪</p>
                <p class="font-bold text-gray-700">{{ __('db.Almost there — you scored :score%. Try again, you need :passing%!', ['score' => $bestAttempt->score_percentage, 'passing' => $quiz->passing_percentage]) }}</p>
                @endif
            </div>
            @endif

            <form method="POST" action="{{ route('lesson.quiz.submit', $lesson) }}" class="bg-white rounded-2xl shadow-sm p-6 space-y-6">
                @csrf
                @include('quiz._questions')

                <x-primary-button>{{ __('db.Submit Quiz') }}</x-primary-button>
            </form>

            <a href="{{ route('lessons.show', $lesson) }}" class="text-sm text-gray-500 hover:underline">← {{ __('db.Back to lesson') }}</a>

        </div>
    </div>
</x-app-layout>