<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.users.index', $filters) }}" class="text-gray-400 hover:text-gray-600">User Management</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Message These Users</span>
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
                <p class="text-sm text-gray-700">
                    <strong>{{ $totalMatching }}</strong> {{ Str::plural('user', $totalMatching) }} match your current filter.
                    @if ($truncated)
                    <span class="text-amber-600">Showing the first {{ $maxRecipients }} below — refine your filters to target a smaller, more specific group.</span>
                    @endif
                </p>
                @php
                    $emailCount = $emailEligible->count();
                    $emailAdvisoryClass = $emailCount >= 500 ? 'text-red-600' : ($emailCount >= 400 ? 'text-amber-600' : 'text-gray-400');
                @endphp
                <p class="text-xs {{ $emailAdvisoryClass }} mt-1">
                    A regular Gmail address (the one Sallaamti sends from) can safely send to about 500 recipients/day. Sending is automatically throttled either way, but a very large campaign will take longer to finish.
                </p>
            </div>

            @if ($matching->isEmpty())
            <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-400">No users match this filter.</div>
            @else
            <form method="POST" action="{{ route('admin.bulk-messages.store') }}" x-data="{
                channel: 'email',
                selected: {{ json_encode($emailEligible->pluck('id')->all()) }},
                whatsappSelected: {{ json_encode($whatsappEligible->pluck('id')->all()) }},
                toggle(id, list) { this[list].includes(id) ? this[list] = this[list].filter(x => x !== id) : this[list].push(id) },
                selectAll(ids, list) { this[list] = [...ids] },
                selectNone(list) { this[list] = [] },
                bodyMode: 'simple',
                advancedBody: {{ Js::from(old('body', '')) }},
                emailTemplates: window.sallaamtiEmailTemplates,
                insertAtCursor(el, text) {
                    const start = el.selectionStart ?? el.value.length;
                    const end = el.selectionEnd ?? el.value.length;
                    el.value = el.value.slice(0, start) + text + el.value.slice(end);
                    el.dispatchEvent(new Event('input'));
                    el.focus();
                    el.selectionStart = el.selectionEnd = start + text.length;
                },
            }" class="space-y-4">
                @csrf
                <input type="hidden" name="channel" x-model="channel">

                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex gap-2 mb-4">
                        <button type="button" @click="channel = 'email'"
                            :class="channel === 'email' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600'"
                            class="text-sm font-semibold px-4 py-2 rounded-lg transition">📧 Email</button>
                        <button type="button" @click="channel = 'whatsapp'"
                            :class="channel === 'whatsapp' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600'"
                            class="text-sm font-semibold px-4 py-2 rounded-lg transition"
                            @disabled(!$whatsappConnected)
                            @if (!$whatsappConnected) title="Connect a WhatsApp Business account in Integrations first" @endif>
                            💬 WhatsApp
                        </button>
                    </div>

                    {{-- Email compose --}}
                    <div x-show="channel === 'email'" x-cloak class="space-y-3">
                        <div class="text-xs text-teal-800 bg-teal-50 border border-teal-100 rounded-lg px-3 py-2 space-y-1">
                            <p class="font-semibold">Requirements</p>
                            <p>A Subject and Message are both required. Only the {{ $emailCount }} recipient(s) with an email on file can be selected — sent through the app's branded template, with an unsubscribe link added automatically at the bottom. Emails go out from <strong>{{ config('mail.from.address') }}</strong>, the address configured on the server — if that account's credentials ever stop working, failures show up per-recipient on the <a href="{{ route('admin.bulk-messages.index') }}" class="underline">Bulk Messages</a> history page after sending, not here up front.</p>
                            <p class="font-semibold mt-2">Precautions</p>
                            <ul class="list-disc list-inside space-y-0.5">
                                <li>Keep the subject honest and specific — avoid ALL CAPS, excessive "!!!", or "FREE"/"URGENT"-style wording, which spam filters flag heavily.</li>
                                <li>Use the <strong>@{{name}}</strong> button below to personalize the subject or message with each recipient's name — it's swapped in automatically per-recipient when sending.</li>
                                <li>If this campaign has more recipients than Gmail's safe daily limit (~500), the system automatically splits sending into daily batches — today's share goes out now, the rest follow automatically on the next day(s), no action needed.</li>
                                <li>Once sent, a campaign can't be recalled or edited — proofread before hitting Send Now.</li>
                            </ul>
                        </div>
                        <div>
                            <div class="flex justify-between items-center">
                                <x-input-label for="subject" value="Subject" />
                                <button type="button" @click="insertAtCursor($refs.subjectInput, '@{{name}}')" class="text-xs text-teal-600 hover:underline">+ Insert Name</button>
                            </div>
                            <x-text-input id="subject" name="subject" x-ref="subjectInput" class="w-full mt-1" placeholder="An update from Sallaamti" value="{{ old('subject') }}" />
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <x-input-label for="body" value="Message" />
                                <div class="flex gap-3 text-xs">
                                    <button type="button" @click="bodyMode = 'simple'" :class="bodyMode === 'simple' ? 'text-teal-700 font-semibold' : 'text-gray-400'">✍️ Simple Editor</button>
                                    <button type="button" @click="bodyMode = 'advanced'" :class="bodyMode === 'advanced' ? 'text-teal-700 font-semibold' : 'text-gray-400'">🎨 Advanced HTML</button>
                                </div>
                            </div>

                            {{-- Simple mode: Trix rich-text editor, same as the Blog Posts editor --}}
                            <div x-show="bodyMode === 'simple'">
                                <button type="button" class="text-xs text-teal-600 hover:underline mb-1"
                                    @click="document.getElementById('body-trix-content').editor.insertString('@{{name}}')">+ Insert Name</button>
                                <input id="body-trix-content" type="hidden" :name="bodyMode === 'simple' ? 'body' : null" value="{{ old('body') }}">
                                <trix-editor input="body-trix-content"></trix-editor>
                            </div>

                            {{-- Advanced mode: raw HTML source, for a newsletter-style template with real colors/buttons/sections that a Simple Editor would flatten --}}
                            <div x-show="bodyMode === 'advanced'" x-cloak>
                                <div class="flex gap-2 flex-wrap mb-2">
                                    <span class="text-xs text-gray-400 self-center">Load a starting template:</span>
                                    <button type="button" @click="advancedBody = emailTemplates.simple" class="text-xs px-3 py-1 rounded-full border border-gray-200 hover:border-teal-500 hover:text-teal-700">Simple Update</button>
                                    <button type="button" @click="advancedBody = emailTemplates.announcement" class="text-xs px-3 py-1 rounded-full border border-gray-200 hover:border-teal-500 hover:text-teal-700">Announcement + Button</button>
                                    <button type="button" @click="advancedBody = emailTemplates.digest" class="text-xs px-3 py-1 rounded-full border border-gray-200 hover:border-teal-500 hover:text-teal-700">Newsletter Digest</button>
                                    <button type="button" @click="insertAtCursor($refs.advancedBodyInput, '@{{name}}')" class="text-xs px-3 py-1 rounded-full border border-teal-200 text-teal-700 hover:bg-teal-50">+ Insert Name</button>
                                </div>
                                <textarea :name="bodyMode === 'advanced' ? 'body' : null" x-ref="advancedBodyInput" x-model="advancedBody" rows="14"
                                    class="w-full font-mono text-xs border-gray-300 rounded-lg" placeholder="&lt;p&gt;Assalamu Alaikum @{{name}},&lt;/p&gt;"></textarea>
                                <p class="text-xs text-gray-400 mt-1">Full HTML with inline CSS — colors, padding, buttons, sections all work. It's sanitized on save (scripts and unsafe tags stripped) but styling is preserved.</p>
                            </div>
                        </div>
                    </div>

                    {{-- WhatsApp compose --}}
                    <div x-show="channel === 'whatsapp'" x-cloak class="space-y-3">
                        @if (!$whatsappConnected)
                        <p class="text-sm text-gray-400">
                            No WhatsApp Business account connected yet. Before this tab will work you need to: (1) <a href="{{ route('admin.integrations.index') }}" class="text-teal-600 hover:underline">connect a WhatsApp Business account in Integrations</a>, and (2) have at least one pre-approved message template in Meta Business Manager.
                        </p>
                        @elseif ($whatsappOptedInTotal === 0)
                        <p class="text-sm text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
                            WhatsApp is connected, but <strong>no members have opted in to WhatsApp notifications yet</strong> — nothing will actually send until at least one does. Members opt in themselves from their Profile → Notifications page; there's no admin override for this (it's required by WhatsApp's own consent policy). You can still prepare a message below for when someone opts in.
                        </p>
                        @else
                        <div class="text-xs text-amber-800 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 space-y-1">
                            <p class="font-semibold">Requirements</p>
                            <ul class="list-disc list-inside space-y-0.5">
                                <li>The template must already be approved in Meta Business Manager — type its exact name below (case-sensitive), not the message text itself.</li>
                                <li>Fill in the template's variables in order, top to bottom. Leave a field blank only if that variable is genuinely unused.</li>
                                <li>Only users who explicitly opted in to WhatsApp notifications can be messaged — {{ $whatsappEligible->count() }} of the {{ $matching->count() }} filtered above qualify; the rest are hidden from this list.</li>
                            </ul>
                            <p class="font-semibold mt-2">Precautions — avoiding a WhatsApp number restriction/ban</p>
                            <ul class="list-disc list-inside space-y-0.5">
                                <li>WhatsApp is for genuine updates, not promotions — Meta actively tracks block rate and low engagement, and can restrict or permanently ban a business number for policy violations.</li>
                                <li>Space broadcasts out — sending too often, even to opted-in users, is the fastest way to rack up blocks and trip Meta's spam signals.</li>
                                <li>Send a small test batch first and watch the delivery status on the <a href="{{ route('admin.bulk-messages.index') }}" class="underline">Bulk Messages</a> history page before sending to everyone — a bad template/variable match fails silently per-recipient, not up front.</li>
                                <li>Never message someone who hasn't explicitly opted in — this list is already filtered to only those users, by design.</li>
                            </ul>
                        </div>
                        <div>
                            <x-input-label for="whatsapp_template_name" value="Approved template name" />
                            <x-text-input id="whatsapp_template_name" name="whatsapp_template_name" class="w-full mt-1" placeholder="e.g. member_update" />
                        </div>
                        <div>
                            <x-input-label value="Template variables, in order (fills the template's numbered placeholders)" />
                            <div class="space-y-2 mt-1">
                                @for ($i = 0; $i < 3; $i++)
                                <x-text-input name="whatsapp_template_params[]" class="w-full" placeholder="Value for variable #{{ $i + 1 }} (leave blank if unused)" />
                                @endfor
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Recipient preview --}}
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-sm font-semibold text-gray-700">
                            Recipients (<span x-text="channel === 'email' ? selected.length : whatsappSelected.length"></span> selected)
                        </p>
                        <div class="text-xs text-teal-600 space-x-3">
                            <button type="button" x-show="channel === 'email'" @click="selectAll({{ json_encode($emailEligible->pluck('id')->all()) }}, 'selected')" class="hover:underline">Select all</button>
                            <button type="button" x-show="channel === 'email'" @click="selectNone('selected')" class="hover:underline">Select none</button>
                            <button type="button" x-show="channel === 'whatsapp'" @click="selectAll({{ json_encode($whatsappEligible->pluck('id')->all()) }}, 'whatsappSelected')" class="hover:underline">Select all</button>
                            <button type="button" x-show="channel === 'whatsapp'" @click="selectNone('whatsappSelected')" class="hover:underline">Select none</button>
                        </div>
                    </div>
                    <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 border border-gray-100 rounded-lg">
                        @foreach ($matching as $user)
                        <label class="flex items-center gap-3 px-3 py-2 text-sm hover:bg-gray-50"
                            x-show="channel === 'email' ? {{ ($user->email ?: false) ? 'true' : 'false' }} : {{ ($user->whatsapp_notify_opt_in && $user->phone) ? 'true' : 'false' }}">
                            <input type="checkbox"
                                name="user_ids[]"
                                value="{{ $user->id }}"
                                :checked="channel === 'email' ? selected.includes({{ $user->id }}) : whatsappSelected.includes({{ $user->id }})"
                                @change="toggle({{ $user->id }}, channel === 'email' ? 'selected' : 'whatsappSelected')"
                                class="rounded border-gray-300 text-teal-600">
                            <span class="text-gray-700">{{ $user->name }}</span>
                            <span class="text-gray-400 text-xs">{{ $user->email }}{{ $user->phone ? ' · ' . $user->phone : '' }}</span>
                            @if ($user->whatsapp_notify_opt_in)
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-teal-50 text-teal-600">WhatsApp opted-in</span>
                            @endif
                        </label>
                        @endforeach
                    </div>
                </div>

                <x-primary-button type="submit" onclick="return confirm('Send this to all selected recipients now?')">Send Now</x-primary-button>
            </form>
            @endif

        </div>
    </div>
</x-admin-layout>
