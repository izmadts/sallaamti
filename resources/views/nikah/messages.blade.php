<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Guardian Messages</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">


            <div class="bg-yellow-50 border border-yellow-200 rounded p-3 text-sm text-yellow-800 flex items-center justify-between gap-3">
                <span>ℹ️ This is a guardian-mediated channel. Please keep all communication respectful and Islamic in conduct.</span>
                <button type="button" onclick="document.getElementById('report-conversation-modal').classList.remove('hidden')" class="shrink-0 text-red-600 hover:underline text-xs font-medium">
                    Report this conversation
                </button>
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

    <div id="report-conversation-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Report this conversation</h3>
            <form method="POST" action="{{ route('nikah.report', $otherProfileId) }}">
                @csrf
                <input type="hidden" name="nikah_interest_id" value="{{ $interest->id }}">
                <label class="block text-sm text-gray-600 mb-1" for="report-reason">Reason</label>
                <select id="report-reason" name="reason" class="w-full border-gray-300 rounded-md text-sm mb-3" required>
                    <option value="">Select a reason</option>
                    <option value="Harassment or inappropriate messages">Harassment or inappropriate messages</option>
                    <option value="Misleading profile information">Misleading profile information</option>
                    <option value="Requesting money or personal financial details">Requesting money or personal financial details</option>
                    <option value="Other">Other</option>
                </select>
                <label class="block text-sm text-gray-600 mb-1" for="report-details">Details (optional)</label>
                <textarea id="report-details" name="details" rows="3" class="w-full border-gray-300 rounded-md text-sm mb-4" placeholder="Anything our team should know"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('report-conversation-modal').classList.add('hidden')" class="px-3 py-2 text-sm text-gray-600">Cancel</button>
                    <button type="submit" class="px-3 py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700">Submit report</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>