<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ $course->title }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))<div class="p-4 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>@endif

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">Post Today's Class Link ({{ now()->format('d M Y') }})</h3>
                <form method="POST" action="{{ route('teacher.courses.daily-link.store', $course) }}" class="space-y-3">
                    @csrf
                    <div><x-input-label value="Zoom Join URL" /><x-text-input name="join_url" class="w-full mt-1" :value="$todaysLink?->join_url" required /></div>
                    <div><x-input-label value="Passcode (optional)" /><x-text-input name="passcode" class="w-full mt-1" :value="$todaysLink?->passcode" /></div>
                    <x-primary-button>{{ $todaysLink ? 'Update' : 'Post' }} Today's Link</x-primary-button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">Confirmed Students This Month ({{ $confirmedStudents->count() }})</h3>
                <div class="divide-y">
                    @foreach ($confirmedStudents as $sub)
                    <div class="py-2 text-sm">{{ $sub->user->name }} — {{ $sub->user->email }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>