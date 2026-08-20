<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.subscribers.index') }}" class="text-gray-400 hover:text-gray-600">Newsletter Subscribers</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Message Verified Subscribers</span>
        </div>
    </x-slot>

    @include('admin.bulk-messages._email-templates-script')

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if ($errors->any())
            <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (session('error'))
            <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-4">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="text-xs text-gray-500">Search email</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="e.g. gmail.com" class="border-gray-300 rounded text-sm block w-56">
                    </div>
                    <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded">Filter</button>
                    @if (request('search'))
                    <a href="{{ route('admin.subscribers.broadcast') }}" class="text-sm text-gray-500 px-2">Reset</a>
                    @endif
                </form>
                <p class="text-sm text-gray-700 mt-3">
                    <strong>{{ $totalMatching }}</strong> verified, active {{ Str::plural('subscriber', $totalMatching) }} match.
                    @if ($truncated)
                    <span class="text-amber-600">Showing the first {{ $maxRecipients }} below — refine your search to target a smaller group.</span>
                    @endif
                    Only people who verified their email and haven't unsubscribed are ever included here.
                </p>
            </div>

            @if ($subscribers->isEmpty())
            <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-400">No verified subscribers match this search.</div>
            @else
            <form method="POST" action="{{ route('admin.bulk-messages.subscribers.store') }}" x-data="{
                selected: {{ json_encode($subscribers->pluck('id')->all()) }},
                toggle(id) { this.selected.includes(id) ? this.selected = this.selected.filter(x => x !== id) : this.selected.push(id) },
                bodyMode: 'simple',
                advancedBody: {{ Js::from(old('body', '')) }},
                emailTemplates: window.sallaamtiEmailTemplates,
            }" class="space-y-4">
                @csrf

                <div class="bg-white rounded-lg shadow-sm p-4 space-y-3">
                    <div class="text-xs text-teal-800 bg-teal-50 border border-teal-100 rounded-lg px-3 py-2 space-y-1">
                        <p class="font-semibold">Requirements</p>
                        <p>A Subject and Message are both required — sent through the app's branded template, with each subscriber's own unsubscribe link added automatically at the bottom. Subscribers have no name on file, so a personalized greeting isn't available here — keep the wording generic ("Assalamu Alaikum," not a name).</p>
                        <p class="font-semibold mt-2">Precautions — avoiding a Gmail block/ban</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            <li>Keep the subject honest and specific — avoid ALL CAPS, excessive "!!!", or "FREE"/"URGENT"-style wording, which spam filters flag heavily.</li>
                            <li>Don't send back-to-back campaigns the same day — space genuine updates out, ideally no more than once every few days.</li>
                            <li>If this campaign has more recipients than Gmail's safe daily limit (~500), the system automatically splits sending into daily batches — no action needed, but a very large list will take multiple days to fully deliver.</li>
                            <li>Once sent, a campaign can't be recalled or edited — proofread before hitting Send Now.</li>
                        </ul>
                    </div>
                    <div>
                        <x-input-label for="subject" value="Subject" />
                        <x-text-input id="subject" name="subject" class="w-full mt-1" placeholder="An update from Sallaamti" value="{{ old('subject') }}" required />
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <x-input-label for="body" value="Message" />
                            <div class="flex gap-3 text-xs">
                                <button type="button" @click="bodyMode = 'simple'" :class="bodyMode === 'simple' ? 'text-teal-700 font-semibold' : 'text-gray-400'">✍️ Simple Editor</button>
                                <button type="button" @click="bodyMode = 'advanced'" :class="bodyMode === 'advanced' ? 'text-teal-700 font-semibold' : 'text-gray-400'">🎨 Advanced HTML</button>
                            </div>
                        </div>

                        <div x-show="bodyMode === 'simple'">
                            <input id="body-trix-content" type="hidden" :name="bodyMode === 'simple' ? 'body' : null" value="{{ old('body') }}">
                            <trix-editor input="body-trix-content"></trix-editor>
                        </div>

                        <div x-show="bodyMode === 'advanced'" x-cloak>
                            <div class="flex gap-2 flex-wrap mb-2">
                                <span class="text-xs text-gray-400 self-center">Load a starting template:</span>
                                <button type="button" @click="advancedBody = emailTemplates.simple" class="text-xs px-3 py-1 rounded-full border border-gray-200 hover:border-teal-500 hover:text-teal-700">Simple Update</button>
                                <button type="button" @click="advancedBody = emailTemplates.announcement" class="text-xs px-3 py-1 rounded-full border border-gray-200 hover:border-teal-500 hover:text-teal-700">Announcement + Button</button>
                                <button type="button" @click="advancedBody = emailTemplates.digest" class="text-xs px-3 py-1 rounded-full border border-gray-200 hover:border-teal-500 hover:text-teal-700">Newsletter Digest</button>
                            </div>
                            <textarea :name="bodyMode === 'advanced' ? 'body' : null" x-model="advancedBody" rows="14"
                                class="w-full font-mono text-xs border-gray-300 rounded-lg" placeholder="&lt;p&gt;Assalamu Alaikum,&lt;/p&gt;"></textarea>
                            <p class="text-xs text-gray-400 mt-1">Full HTML with inline CSS — colors, padding, buttons, sections all work. It's sanitized on save (scripts and unsafe tags stripped) but styling is preserved.</p>
                        </div>
                    </div>
                </div>

                {{-- Recipient preview --}}
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-sm font-semibold text-gray-700">Recipients (<span x-text="selected.length"></span> selected)</p>
                        <div class="text-xs text-teal-600 space-x-3">
                            <button type="button" @click="selected = {{ json_encode($subscribers->pluck('id')->all()) }}" class="hover:underline">Select all</button>
                            <button type="button" @click="selected = []" class="hover:underline">Select none</button>
                        </div>
                    </div>
                    <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 border border-gray-100 rounded-lg">
                        @foreach ($subscribers as $subscriber)
                        <label class="flex items-center gap-3 px-3 py-2 text-sm hover:bg-gray-50">
                            <input type="checkbox"
                                name="subscriber_ids[]"
                                value="{{ $subscriber->id }}"
                                :checked="selected.includes({{ $subscriber->id }})"
                                @change="toggle({{ $subscriber->id }})"
                                class="rounded border-gray-300 text-teal-600">
                            <span class="text-gray-700">{{ $subscriber->email }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <x-primary-button type="submit" onclick="return confirm('Send this to all selected subscribers now?')">Send Now</x-primary-button>
            </form>
            @endif

        </div>
    </div>
</x-admin-layout>
