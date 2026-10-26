<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.support-messages.index') }}" class="text-gray-400 hover:text-gray-600">Member Messages</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">{{ $user->name }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4 mb-4 max-h-[60vh] overflow-y-auto" id="thread">
                @forelse ($messages as $message)
                @php $fromMember = (int) $message->sender_id === (int) $user->id; @endphp
                <div class="flex {{ $fromMember ? 'justify-start' : 'justify-end' }}">
                    <div class="max-w-[75%] {{ $fromMember ? 'bg-gray-100 text-gray-800' : 'bg-teal-600 text-white' }} rounded-2xl px-4 py-2.5">
                        <p class="text-sm whitespace-pre-wrap">{{ $message->body }}</p>
                        <p class="text-[11px] mt-1 {{ $fromMember ? 'text-gray-400' : 'text-teal-100' }}">
                            {{ $fromMember ? $user->name : ($message->sender->name ?? 'Admin') }} — {{ $message->created_at->format('d M, h:i A') }}
                        </p>
                    </div>
                </div>
                @empty
                <p class="text-gray-500">No messages yet.</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('admin.support-messages.reply', $user) }}" class="flex gap-2">
                @csrf
                <textarea name="body" rows="2" required placeholder="Type a reply…" class="flex-1 border-gray-300 rounded-lg text-sm"></textarea>
                <button class="bg-teal-600 text-white px-5 rounded-lg hover:bg-teal-700 self-end py-2.5">Send</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const thread = document.getElementById('thread');
            if (thread) thread.scrollTop = thread.scrollHeight;
        });
    </script>
</x-admin-layout>
