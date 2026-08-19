{{--
    Shared question-rendering block for quiz/show.blade.php (course final
    quiz) and quiz/show-lesson.blade.php (per-lesson quiz) — same markup,
    different submit route, so the parent view wraps this in its own <form>.

    Kid-friendly pass: big tappable colored answer cards instead of bare
    radio rows (reusing the peer-checked pattern from support/create.blade.php's
    category picker), plus a "read aloud" button using the browser's built-in
    speech synthesis — no audio files/backend needed, and it matters a lot
    here since these are Quran/Arabic-literacy learners who may not read
    fluently yet.
--}}
@foreach ($quiz->questions as $i => $q)
<div class="rounded-2xl border-2 border-gray-100 p-5">
    <div class="flex items-start gap-2 mb-3">
        <p class="font-bold text-gray-800 text-lg flex-1">{{ $i + 1 }}. {{ $q->question }}</p>
        <button type="button" onclick="speakQuizText({{ Js::from($q->question) }})"
            class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-lg hover:bg-teal-50" style="background: var(--teal-light)" title="Read aloud">
            🔊
        </button>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        @foreach ($q->options as $idx => $opt)
        @php $letter = chr(65 + $idx); @endphp
        <label class="cursor-pointer" x-data>
            <input type="radio" name="answers[{{ $q->id }}]" value="{{ $idx }}" required class="sr-only peer">
            <div class="flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 transition-all duration-200 peer-checked:border-teal-600 peer-checked:bg-teal-50 hover:border-teal-300">
                <span class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white" style="background: var(--teal)">{{ $letter }}</span>
                <span class="text-sm font-medium text-gray-700">{{ $opt }}</span>
            </div>
        </label>
        @endforeach
    </div>
</div>
@endforeach

<script>
    function speakQuizText(text) {
        if (!('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();
        window.speechSynthesis.speak(new SpeechSynthesisUtterance(text));
    }
</script>
