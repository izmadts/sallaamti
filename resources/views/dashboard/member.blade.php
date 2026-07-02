<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Welcome back, {{ $user->name }} 👋</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Notifications --}}
            @if ($unreadNotifications->count() > 0)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 space-y-2">
                <p class="text-sm font-semibold text-blue-700">🔔 Notifications</p>
                @foreach ($unreadNotifications as $n)
                <a href="{{ $n->data['url'] ?? '#' }}" class="block text-sm text-blue-600 hover:underline">
                    {{ $n->data['message'] }}
                </a>
                @endforeach
            </div>
            @endif

            {{-- Quick stats --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow-sm p-4 text-center border-t-4 border-teal-400">
                    <p class="text-2xl font-bold text-teal-600">{{ $stats['courses_enrolled'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Courses Enrolled</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 text-center border-t-4 border-green-400">
                    <p class="text-2xl font-bold text-green-600">{{ $stats['lessons_completed'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Lessons Completed</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-4 text-center border-t-4 border-yellow-400">
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['certificates_earned'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Certificates Earned</p>
                </div>
            </div>

            
        </div>
    </div>
</x-app-layout>