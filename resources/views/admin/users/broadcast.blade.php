<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.users.index', $filters) }}" class="text-gray-400 hover:text-gray-600">User Management</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Message These Users</span>
        </div>
    </x-slot>

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
                    $emailCount = $matching->count();
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
                selected: {{ json_encode($matching->pluck('id')->all()) }},
                whatsappSelected: {{ json_encode($whatsappEligible->pluck('id')->all()) }},
                toggle(id, list) { this[list].includes(id) ? this[list] = this[list].filter(x => x !== id) : this[list].push(id) },
                selectAll(ids, list) { this[list] = [...ids] },
                selectNone(list) { this[list] = [] },
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
                        <div>
                            <x-input-label for="subject" value="Subject" />
                            <x-text-input id="subject" name="subject" class="w-full mt-1" placeholder="An update from Sallaamti" />
                        </div>
                        <div>
                            <x-input-label for="body" value="Message" />
                            <textarea id="body" name="body" rows="8" class="w-full mt-1 border-gray-300 rounded-lg text-sm" placeholder="Assalamu Alaikum, ..."></textarea>
                            <p class="text-xs text-gray-400 mt-1">Sent using the app's branded email template, with an unsubscribe link added automatically.</p>
                        </div>
                    </div>

                    {{-- WhatsApp compose --}}
                    <div x-show="channel === 'whatsapp'" x-cloak class="space-y-3">
                        @if (!$whatsappConnected)
                        <p class="text-sm text-gray-400">No WhatsApp Business account connected yet — <a href="{{ route('admin.integrations.index') }}" class="text-teal-600 hover:underline">connect one in Integrations</a> first.</p>
                        @else
                        <p class="text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
                            WhatsApp doesn't allow free-form broadcast text — Meta requires a pre-approved message template for any message you initiate. Enter that template's name below and fill in its variables (in order); only users who opted in to WhatsApp notifications ({{ $whatsappEligible->count() }} of the {{ $matching->count() }} shown) will receive it.
                        </p>
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
                            <button type="button" x-show="channel === 'email'" @click="selectAll({{ json_encode($matching->pluck('id')->all()) }}, 'selected')" class="hover:underline">Select all</button>
                            <button type="button" x-show="channel === 'email'" @click="selectNone('selected')" class="hover:underline">Select none</button>
                            <button type="button" x-show="channel === 'whatsapp'" @click="selectAll({{ json_encode($whatsappEligible->pluck('id')->all()) }}, 'whatsappSelected')" class="hover:underline">Select all</button>
                            <button type="button" x-show="channel === 'whatsapp'" @click="selectNone('whatsappSelected')" class="hover:underline">Select none</button>
                        </div>
                    </div>
                    <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 border border-gray-100 rounded-lg">
                        @foreach ($matching as $user)
                        <label class="flex items-center gap-3 px-3 py-2 text-sm hover:bg-gray-50" x-show="channel === 'email' || {{ $user->whatsapp_notify_opt_in && $user->phone ? 'true' : 'false' }}">
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
