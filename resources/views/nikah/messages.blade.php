<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ __('db.Guardian Messages') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">


            <div class="bg-yellow-50 border border-yellow-200 rounded p-3 text-sm text-yellow-800 flex items-center justify-between gap-3">
                <span>ℹ️ {{ __('db.This is a guardian-mediated channel. Please keep all communication respectful and Islamic in conduct.') }}</span>
                <button type="button" onclick="document.getElementById('report-conversation-modal').classList.remove('hidden')" class="shrink-0 text-red-600 hover:underline text-xs font-medium">
                    {{ __('db.Report this conversation') }}
                </button>
            </div>

            {{-- Safety tips — zero tolerance for trust breaches, so this stays
                 right where the actual risk is: once a conversation is open,
                 not buried in a help page nobody visits. --}}
            <details class="bg-teal-50 border border-teal-200 rounded-lg text-sm">
                <summary class="cursor-pointer px-3 py-2.5 font-semibold text-teal-800">🛡️ {{ __('db.Safety tips before you continue') }}</summary>
                <ul class="px-3 pb-3 space-y-2 text-teal-900 list-none">
                    <li>🪪 {{ __('db.The "CNIC Verified" and "Trusted Verified" badges mean our team has confirmed real identity documents and payment. A profile still "Pending" deserves extra caution.') }}</li>
                    <li>👨‍👩‍👦 {{ __('db.Tell your guardian before accepting any serious interest. A real family always wants guardian involvement — anyone rushing you past that is a red flag.') }}</li>
                    <li>💬 {{ __('db.All conversation happens through Guardian Messages. If someone pushes to move to WhatsApp or a phone call before your guardian is involved, that\'s a warning sign.') }}</li>
                    <li>🚫 {{ __('db.No genuine match or guardian will ever ask you for bank details, a verification code, or money.') }}</li>
                    <li>👤 {{ __('db.If someone says they\'re "helping a friend or relative" from their own account, or seems to be running multiple profiles, be cautious and report it.') }}</li>
                    <li>🐢 {{ __('db.Genuine families take time — multiple conversations, questions, a family introduction. Pressure to decide quickly is a major warning sign.') }}</li>
                    <li>⚑ {{ __('db.If something feels wrong, report it right away — we take every report seriously and have zero tolerance for trust breaches.') }}</li>
                </ul>
            </details>

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
                <p class="text-gray-400 text-sm text-center">{{ __('db.No messages yet. Send the first message below.') }}</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('nikah.messages.store', $interest) }}" class="bg-white rounded-lg shadow-sm p-4">
                @csrf
                <div class="flex gap-3">
                    <textarea name="message" rows="2" class="flex-1 border-gray-300 rounded-md text-sm" placeholder="{{ __('db.Write a message...') }}" required></textarea>
                    <x-primary-button class="self-end">{{ __('db.Send') }}</x-primary-button>
                </div>
            </form>

            <a href="{{ route('nikah.interests') }}" class="text-sm text-gray-500 hover:underline">← {{ __('db.Back to Interests') }}</a>
        </div>
    </div>

    <div id="report-conversation-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-5">
            <h3 class="font-semibold text-gray-800 mb-3">{{ __('db.Report this conversation') }}</h3>
            <form method="POST" action="{{ route('nikah.report', $otherProfileId) }}">
                @csrf
                <input type="hidden" name="nikah_interest_id" value="{{ $interest->id }}">
                <label class="block text-sm text-gray-600 mb-1" for="report-reason">{{ __('db.Reason') }}</label>
                <select id="report-reason" name="reason" class="w-full border-gray-300 rounded-md text-sm mb-3" required>
                    <option value="">{{ __('db.Select a reason') }}</option>
                    <option value="Harassment or inappropriate messages">{{ __('db.Harassment or inappropriate messages') }}</option>
                    <option value="Misleading profile information">{{ __('db.Misleading profile information') }}</option>
                    <option value="Requesting money or personal financial details">{{ __('db.Requesting money or personal financial details') }}</option>
                    <option value="Suspected fake matchmaker / mass outreach">{{ __('db.Suspected fake matchmaker / mass outreach') }}</option>
                    <option value="Asked to move the conversation outside the app">{{ __('db.Asked to move the conversation outside the app') }}</option>
                    <option value="Other">{{ __('db.Other') }}</option>
                </select>
                <label class="block text-sm text-gray-600 mb-1" for="report-details">{{ __('db.Details (optional)') }}</label>
                <textarea id="report-details" name="details" rows="3" class="w-full border-gray-300 rounded-md text-sm mb-4" placeholder="{{ __('db.Anything our team should know') }}"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('report-conversation-modal').classList.add('hidden')" class="px-3 py-2 text-sm text-gray-600">{{ __('db.Cancel') }}</button>
                    <button type="submit" class="px-3 py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700">{{ __('db.Submit report') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>