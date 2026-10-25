<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">❓ Member Q&amp;A ("Ap k Sawalat")</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @php $activeStatus = $status ?? 'pending'; @endphp
            <div class="grid grid-cols-3 gap-3">
                @foreach (['pending' => $stats['pending'], 'answered' => $stats['answered'], 'all' => $stats['total']] as $s => $count)
                <a href="{{ route('admin.questions.index', ['status' => $s]) }}"
                    class="bg-white rounded-lg shadow-sm p-4 text-center border-2 {{ $activeStatus === $s ? 'border-teal-500' : 'border-transparent' }}">
                    <p class="text-2xl font-bold text-gray-800">{{ $count }}</p>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $s === 'all' ? 'Total' : ucfirst($s) }}</p>
                </a>
                @endforeach
            </div>

            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($questions as $question)
                <div class="p-5 flex justify-between items-start flex-wrap gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800">{{ $question->question }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">by {{ $question->asker->name }} — {{ $question->created_at->format('d M Y, h:i A') }}</p>
                        @if ($question->status === 'answered')
                        <p class="text-sm text-gray-600 mt-1.5">
                            @if ($question->canonical)
                            🔗 Linked to: "{{ $question->canonical->question }}"
                            @else
                            {{ $question->answer }}
                            @endif
                        </p>
                        @endif
                    </div>

                    <div class="flex flex-col items-end gap-2 w-64">
                        <span class="text-xs px-2 py-1 rounded-full {{ $question->status === 'answered' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($question->status) }}
                        </span>

                        @if ($question->status !== 'answered')
                        <form method="POST" action="{{ route('admin.questions.answer', $question) }}" class="w-full flex flex-col gap-1.5 items-end">
                            @csrf
                            <textarea name="answer" rows="2" placeholder="Write the answer…" required class="border rounded text-sm px-2 py-1 w-full"></textarea>
                            <button class="bg-green-600 text-white text-sm px-3 py-1.5 rounded hover:bg-green-700">Answer</button>
                        </form>

                        @if ($answerable->isNotEmpty())
                        <details class="text-right w-full">
                            <summary class="cursor-pointer text-sm text-teal-700 hover:text-teal-800 list-none">🔗 Link to existing ▾</summary>
                            <form method="POST" action="{{ route('admin.questions.link', $question) }}" class="mt-2 flex flex-col gap-1.5 items-end">
                                @csrf
                                <select name="canonical_question_id" required class="border rounded text-xs px-2 py-1 w-full">
                                    <option value="">Pick a question…</option>
                                    @foreach ($answerable as $existing)
                                    <option value="{{ $existing->id }}">{{ \Illuminate\Support\Str::limit($existing->question, 60) }}</option>
                                    @endforeach
                                </select>
                                <button class="bg-teal-600 text-white text-xs px-3 py-1.5 rounded hover:bg-teal-700">Link</button>
                            </form>
                        </details>
                        @endif
                        @endif

                        <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" onsubmit="return confirm('Permanently delete this question? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-400 hover:text-red-600 hover:underline">Delete permanently</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="p-5 text-gray-500">No questions here.</p>
                @endforelse
            </div>

            {{ $questions->links() }}
        </div>
    </div>
</x-admin-layout>
