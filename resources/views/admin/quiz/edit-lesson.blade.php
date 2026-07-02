<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Quiz — {{ $lesson->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-6">
                <form method="POST" action="{{ route('admin.lessons.quiz.store', $lesson) }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label value="Quiz Title" />
                        <x-text-input name="title" class="w-full mt-1" :value="$quiz->title ?? 'Lesson Quiz'" required />
                    </div>
                    <div>
                        <x-input-label value="Passing Percentage" />
                        <x-text-input name="passing_percentage" type="number" class="w-full mt-1" :value="$quiz->passing_percentage ?? 70" required />
                    </div>
                    <x-primary-button>Save Quiz Settings</x-primary-button>
                </form>
            </div>

            @if ($quiz)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">Questions</h3>

                <div class="space-y-3 mb-6">
                    @foreach ($quiz->questions as $q)
                    <div class="border rounded p-3 flex justify-between items-start">
                        <div>
                            <p class="font-medium text-sm">{{ $q->question }}</p>
                            <ul class="text-xs text-gray-500 mt-1 list-disc list-inside">
                                @foreach ($q->options as $i => $opt)
                                <li class="{{ $i === $q->correct_option ? 'text-green-600 font-medium' : '' }}">{{ $opt }} {{ $i === $q->correct_option ? '(correct)' : '' }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <form method="POST" action="{{ route('admin.quiz.questions.destroy', $q) }}" onsubmit="return confirm('Delete this question?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-600">Delete</button>
                        </form>
                    </div>
                    @endforeach
                </div>

                <h4 class="font-semibold text-gray-700 text-sm mb-2">Add New Question</h4>
                <form method="POST" action="{{ route('admin.quiz.questions.store', $quiz) }}" class="space-y-3">
                    @csrf
                    <div>
                        <x-input-label value="Question" />
                        <textarea name="question" rows="2" class="border-gray-300 rounded-md w-full mt-1" required></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        @for ($i = 0; $i < 4; $i++)
                            <div>
                            <x-input-label value="Option {{ $i + 1 }}" />
                            <x-text-input name="options[]" class="w-full mt-1" required />
                    </div>
                    @endfor
            </div>
            <div>
                <x-input-label value="Correct Option" />
                <select name="correct_option" class="border-gray-300 rounded-md w-full mt-1" required>
                    <option value="0">Option 1</option>
                    <option value="1">Option 2</option>
                    <option value="2">Option 3</option>
                    <option value="3">Option 4</option>
                </select>
            </div>
            <x-primary-button>Add Question</x-primary-button>
            </form>
        </div>
        @endif

    </div>
    </div>
</x-admin-layout>