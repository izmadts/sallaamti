<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('db.My Students') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($students as $gs)
                <a href="{{ route('teacher.students.show', $gs) }}" class="flex justify-between items-center p-4 hover:bg-gray-50">
                    <div>
                        <p class="font-medium">{{ $gs->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $gs->group->course->title }} — {{ $gs->group->group_name }}</p>
                        @if ($gs->admission)
                        <p class="text-xs text-gray-400">{{ $gs->admission->student_name }} | {{ __('db.Age:') }} {{ $gs->admission->student_age }}</p>
                        @endif
                    </div>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">{{ __('db.Active') }}</span>
                </a>
                @empty
                <p class="p-5 text-gray-500">{{ __('db.No students yet.') }}</p>
                @endforelse
            </div>
            {{ $students->links() }}
        </div>
    </div>
</x-app-layout>