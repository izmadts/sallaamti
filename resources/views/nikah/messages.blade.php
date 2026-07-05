<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Guardian Messages</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-yellow-50 border border-yellow-200 rounded p-3 text-sm text-yellow-800">
                ℹ️ This is a guardian-mediated channel. Please keep all communication respectful and Islamic in conduct.
            </div>

            <div class="bg-white rounded-lg shadow-sm p-5 space-y-4">
                @forelse ($messages as $msg)
                <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs rounded-lg px-4 py-2 text-sm {{ $msg->sender_id === auth()->id() ? 'bg-pink-100 text-gray-800' : 'bg-gray-100 text-gray-800' }}">
                        <p class="font-medium text-xs text-gray-500 mb-1">{{ $msg->sender->name }}</p>
                        <p>{{ $msg->message }}</p>
                        <p class="text-xs text-gray-400 mt-1 text-right">{{ $msg->created_at->format('d M, h:i A') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-400 text-sm text-center">No messages yet. Send the first message below.</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('nikah.messages.store', $interest) }}" class="bg-white rounded-lg shadow-sm p-4">
                @csrf
                <div class="flex gap-3">
                    <textarea name="message" rows="2" class="flex-1 border-gray-300 rounded-md text-sm" placeholder="Write a message..." required></textarea>
                    <x-primary-button class="self-end">Send</x-primary-button>
                </div>
            </form>

            <a href="{{ route('nikah.interests') }}" class="text-sm text-gray-500 hover:underline">← Back to Interests</a>
        </div>
    </div>
</x-app-layout>