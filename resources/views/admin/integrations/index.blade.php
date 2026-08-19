<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600">Dashboard</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Integrations</span>
        </div>
    </x-slot>

    @php
        $platformMeta = [
            'facebook' => ['icon' => 'fab fa-facebook text-blue-600', 'name' => 'Facebook'],
            'instagram' => ['icon' => 'fab fa-instagram text-pink-600', 'name' => 'Instagram'],
            'twitter' => ['icon' => 'fab fa-x-twitter text-gray-800', 'name' => 'X (Twitter)'],
            'youtube' => ['icon' => 'fab fa-youtube text-red-600', 'name' => 'YouTube'],
            'tiktok' => ['icon' => 'fab fa-tiktok text-gray-800', 'name' => 'TikTok'],
            'threads' => ['icon' => 'fab fa-threads text-gray-800', 'name' => 'Threads'],
        ];
    @endphp

    <div class="w-full space-y-6">

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 border-b pb-2">📣 Social Media Auto-Posting</h3>
            <p class="text-xs text-gray-500 mt-2">
                Connect each platform below, then check the platforms you want under "Share to social media" when publishing a Community Post — it posts automatically the moment you publish.
                WhatsApp isn't in this list: it has no public API for posting to a feed/wall the way these six do. It has its own card further down for a different purpose — notifying members directly, not publishing a post.
            </p>
            <p class="text-xs text-gray-500 mt-2">
                <strong>Facebook Page + Instagram share one connection</strong> — Instagram Business accounts are always linked through a Facebook Page, so there's no separate Instagram login; connecting Facebook below picks up the linked Instagram account automatically if one exists.
            </p>
        </div>

        {{-- Facebook / Instagram — this is a SEPARATE Meta App from the one
             under Settings → Social Login, which is only for "Sign in with
             Facebook". Two different apps, two different credential pairs,
             on purpose: this one only ever posts to a Page, that one only
             ever signs members in — keeping them apart means a change to
             one can never accidentally break the other. --}}
        <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i class="{{ $platformMeta['facebook']['icon'] }}"></i> Facebook Page (posting)
                </h4>
                @if ($account = $accounts['facebook'] ?? null)
                <div class="flex items-center gap-2">
                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-semibold">✅ {{ $account->display_name }}</span>
                    <form method="POST" action="{{ route('admin.integrations.disconnect', $account) }}">
                        @csrf
                        <button class="text-xs text-red-500 hover:underline">Disconnect</button>
                    </form>
                </div>
                @else
                <a href="{{ route('admin.integrations.connect', 'facebook') }}" class="text-xs font-semibold px-3 py-1.5 rounded-full text-white" style="background: var(--teal)">Connect Facebook</a>
                @endif
            </div>
            <p class="text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
                Uses its <strong>own</strong> Meta App — a different App ID/Secret from the one under Settings → Social Login (that one is only for member sign-in). Enter this app's credentials below, not that one's.
            </p>
            <ol class="text-xs text-gray-500 list-decimal ms-4 space-y-0.5">
                <li>In this <strong>posting</strong> Meta App's dashboard, add the "Facebook Login for Business" product and, under its settings, add the redirect URI below as a valid OAuth redirect URI.</li>
                <li>While the app is in Development Mode, add yourself as an Admin/Tester on the app so you can connect immediately — no App Review needed for posting to your own Page.</li>
                <li>Copy this app's App ID and Secret into the fields below, save, then click Connect and choose the Page you manage.</li>
            </ol>
            <div>
                <x-input-label value="Valid OAuth redirect URI" />
                <input type="text" readonly value="{{ route('admin.integrations.callback', 'facebook') }}" onclick="this.select()"
                    class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 font-mono text-gray-600">
            </div>
            <form method="POST" action="{{ route('admin.integrations.settings.update') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <div>
                    <x-input-label value="Posting App ID" />
                    <x-text-input name="facebook_posting_client_id" class="w-full mt-1" :value="$settings['facebook_posting_client_id'] ?? ''" />
                </div>
                <div>
                    <x-input-label value="Posting App Secret" />
                    <x-text-input name="facebook_posting_client_secret" class="w-full mt-1" :value="$settings['facebook_posting_client_secret'] ?? ''" />
                </div>
                <div class="sm:col-span-2">
                    <x-secondary-button type="submit">Save Posting App Credentials</x-secondary-button>
                </div>
            </form>
        </div>

        {{-- Instagram (derived) --}}
        <div class="bg-white rounded-lg shadow-sm p-6 space-y-2">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i class="{{ $platformMeta['instagram']['icon'] }}"></i> Instagram
                </h4>
                @if ($account = $accounts['instagram'] ?? null)
                <div class="flex items-center gap-2">
                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-semibold">✅ {{ $account->display_name }}</span>
                    <form method="POST" action="{{ route('admin.integrations.disconnect', $account) }}">
                        @csrf
                        <button class="text-xs text-red-500 hover:underline">Disconnect</button>
                    </form>
                </div>
                @else
                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-500">Not connected</span>
                @endif
            </div>
            <p class="text-xs text-gray-400">
                Connected automatically when you connect Facebook above, if that Page has an Instagram Business account linked to it. No Instagram Business account yet? Link one to your Page in Meta Business Suite, then reconnect Facebook.
            </p>
        </div>

        {{-- WhatsApp Business — connect the account. This is a manual
             credential form, not an OAuth redirect: WhatsApp's Cloud API
             uses a permanent access token generated for a System User in
             Meta Business Manager, pasted in directly. --}}
        <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i class="fab fa-whatsapp text-green-600"></i> WhatsApp Business
                </h4>
                @if ($whatsappAccount = $accounts['whatsapp'] ?? null)
                <div class="flex items-center gap-2">
                    <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-semibold">✅ {{ $whatsappAccount->display_name }}</span>
                    <form method="POST" action="{{ route('admin.integrations.disconnect', $whatsappAccount) }}">
                        @csrf
                        <button class="text-xs text-red-500 hover:underline">Disconnect</button>
                    </form>
                </div>
                @else
                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-500">Not connected</span>
                @endif
            </div>
            <p class="text-xs text-gray-500">
                This is <strong>not</strong> for posting to a public feed — WhatsApp has no such API. It sends a direct notification to individual members who've opted in, when a Community Post publishes. See "WhatsApp Notifications" further down to actually turn sending on — it stays off by default even once connected.
            </p>
            <ol class="text-xs text-gray-500 list-decimal ms-4 space-y-0.5">
                <li>In Meta Business Manager, add the WhatsApp product to your app and complete phone number setup.</li>
                <li>Create a System User with WhatsApp permissions, generate a <strong>permanent</strong> access token for it (not a temporary 24-hour one).</li>
                <li>Create and get a message template approved in Meta Business Manager — business-initiated messages can't be free-form text, they must use a pre-approved template with one body variable (the post title fills it in).</li>
                <li>Copy the Phone Number ID, WhatsApp Business Account ID, and the permanent token below.</li>
            </ol>
            @if ($errors->has('whatsapp_phone_number_id') || $errors->has('whatsapp_business_account_id') || $errors->has('whatsapp_access_token'))
            <div class="p-3 rounded-lg bg-red-50 border border-red-200 text-xs text-red-700">
                @foreach ($errors->only(['whatsapp_phone_number_id', 'whatsapp_business_account_id', 'whatsapp_access_token']) as $fieldErrors)
                    @foreach ((array) $fieldErrors as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                @endforeach
            </div>
            @endif
            <form method="POST" action="{{ route('admin.integrations.whatsapp.connect') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Phone Number ID" />
                        <x-text-input name="whatsapp_phone_number_id" class="w-full mt-1" :value="old('whatsapp_phone_number_id', $whatsappAccount->external_account_id ?? '')" />
                    </div>
                    <div>
                        <x-input-label value="WhatsApp Business Account ID" />
                        <x-text-input name="whatsapp_business_account_id" class="w-full mt-1" :value="old('whatsapp_business_account_id', $whatsappAccount->extra['waba_id'] ?? '')" />
                    </div>
                </div>
                <div>
                    <x-input-label value="Permanent Access Token" />
                    <x-text-input name="whatsapp_access_token" type="password" class="w-full mt-1" placeholder="{{ $whatsappAccount ? 'Re-enter the token to update the connection' : '' }}" required />
                    <p class="text-xs text-gray-400 mt-1">Not shown once saved — re-enter it here any time you need to update the connection.</p>
                </div>
                <div>
                    <x-input-label value="Display Name (optional)" />
                    <x-text-input name="whatsapp_display_name" class="w-full mt-1" :value="old('whatsapp_display_name', $whatsappAccount->display_name ?? '')" placeholder="e.g. Sallaamti Business Number" />
                </div>
                <x-secondary-button type="submit">{{ $whatsappAccount ? 'Update Connection' : 'Connect WhatsApp' }}</x-secondary-button>
            </form>
        </div>

        <form method="POST" action="{{ route('admin.integrations.settings.update') }}" class="space-y-6">
            @csrf

            {{-- X (Twitter) --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="{{ $platformMeta['twitter']['icon'] }}"></i> X (Twitter)
                    </h4>
                    @if ($account = $accounts['twitter'] ?? null)
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-semibold">✅ {{ $account->display_name }}</span>
                        <form method="POST" action="{{ route('admin.integrations.disconnect', $account) }}">
                            @csrf
                            <button class="text-xs text-red-500 hover:underline">Disconnect</button>
                        </form>
                    </div>
                    @elseif (filled($settings['twitter_client_id'] ?? null) && filled($settings['twitter_client_secret'] ?? null))
                    <a href="{{ route('admin.integrations.connect', 'twitter') }}" class="text-xs font-semibold px-3 py-1.5 rounded-full text-white" style="background: var(--teal)">Connect X</a>
                    @else
                    <span class="text-xs text-gray-400 italic">Save credentials below, then connect</span>
                    @endif
                </div>
                <ol class="text-xs text-gray-500 list-decimal ms-4 space-y-0.5">
                    <li>Go to <span class="font-mono">developer.twitter.com/en/portal/dashboard</span>, create a Project + App (free tier is enough).</li>
                    <li>Under User authentication settings, enable OAuth 2.0, set app type to "Web App, Automated App or Bot" (confidential client), and add the redirect URI below.</li>
                    <li>Under scopes, make sure <span class="font-mono">tweet.write</span>, <span class="font-mono">tweet.read</span>, <span class="font-mono">users.read</span>, and <span class="font-mono">offline.access</span> are requested.</li>
                    <li>Copy the Client ID and Client Secret into the fields below, save, then click Connect.</li>
                </ol>
                <div>
                    <x-input-label value="Redirect URI" />
                    <input type="text" readonly value="{{ route('admin.integrations.callback', 'twitter') }}" onclick="this.select()"
                        class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 font-mono text-gray-600">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Client ID" />
                        <x-text-input name="twitter_client_id" class="w-full mt-1" :value="$settings['twitter_client_id'] ?? ''" />
                    </div>
                    <div>
                        <x-input-label value="Client Secret" />
                        <x-text-input name="twitter_client_secret" class="w-full mt-1" :value="$settings['twitter_client_secret'] ?? ''" />
                    </div>
                </div>
                <x-secondary-button type="submit">Save X (Twitter) Credentials</x-secondary-button>
            </div>

            {{-- YouTube --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="{{ $platformMeta['youtube']['icon'] }}"></i> YouTube
                    </h4>
                    @if ($account = $accounts['youtube'] ?? null)
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-semibold">✅ {{ $account->display_name }}</span>
                        <form method="POST" action="{{ route('admin.integrations.disconnect', $account) }}">
                            @csrf
                            <button class="text-xs text-red-500 hover:underline">Disconnect</button>
                        </form>
                    </div>
                    @elseif (filled($settings['youtube_client_id'] ?? null) && filled($settings['youtube_client_secret'] ?? null))
                    <a href="{{ route('admin.integrations.connect', 'youtube') }}" class="text-xs font-semibold px-3 py-1.5 rounded-full text-white" style="background: var(--teal)">Connect YouTube</a>
                    @else
                    <span class="text-xs text-gray-400 italic">Save credentials below, then connect</span>
                    @endif
                </div>
                <ol class="text-xs text-gray-500 list-decimal ms-4 space-y-0.5">
                    <li>In <span class="font-mono">console.cloud.google.com</span>, create/select a project and enable the "YouTube Data API v3".</li>
                    <li>Configure the OAuth consent screen — it can stay in "Testing" status, just add your own Google account under Test Users (no Google verification needed for one channel).</li>
                    <li>Create an "OAuth client ID" (Web application) and add the redirect URI below.</li>
                    <li>Copy the Client ID and Client Secret into the fields below, save, then click Connect. Only videos publish here — YouTube has no image/text post type.</li>
                </ol>
                <div>
                    <x-input-label value="Authorized redirect URI" />
                    <input type="text" readonly value="{{ route('admin.integrations.callback', 'youtube') }}" onclick="this.select()"
                        class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 font-mono text-gray-600">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Client ID" />
                        <x-text-input name="youtube_client_id" class="w-full mt-1" :value="$settings['youtube_client_id'] ?? ''" />
                    </div>
                    <div>
                        <x-input-label value="Client Secret" />
                        <x-text-input name="youtube_client_secret" class="w-full mt-1" :value="$settings['youtube_client_secret'] ?? ''" />
                    </div>
                </div>
                <x-secondary-button type="submit">Save YouTube Credentials</x-secondary-button>
            </div>

            {{-- TikTok --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="{{ $platformMeta['tiktok']['icon'] }}"></i> TikTok
                    </h4>
                    @if ($account = $accounts['tiktok'] ?? null)
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-semibold">✅ {{ $account->display_name }}</span>
                        <form method="POST" action="{{ route('admin.integrations.disconnect', $account) }}">
                            @csrf
                            <button class="text-xs text-red-500 hover:underline">Disconnect</button>
                        </form>
                    </div>
                    @elseif (filled($settings['tiktok_client_id'] ?? null) && filled($settings['tiktok_client_secret'] ?? null))
                    <a href="{{ route('admin.integrations.connect', 'tiktok') }}" class="text-xs font-semibold px-3 py-1.5 rounded-full text-white" style="background: var(--teal)">Connect TikTok</a>
                    @else
                    <span class="text-xs text-gray-400 italic">Save credentials below, then connect</span>
                    @endif
                </div>
                <ol class="text-xs text-gray-500 list-decimal ms-4 space-y-0.5">
                    <li>Go to <span class="font-mono">developers.tiktok.com/apps</span>, create an app, and add the "Content Posting API" product.</li>
                    <li>Add the redirect URI below under Login Kit → Redirect URI.</li>
                    <li><strong>Until TikTok approves the app for the Content Posting API</strong> (their own audit, outside our control), posts land as a private draft in your TikTok inbox to finish manually — check "Approved for public posting" below once TikTok confirms the audit passed.</li>
                    <li>Copy the Client Key and Client Secret into the fields below, save, then click Connect.</li>
                </ol>
                <div>
                    <x-input-label value="Redirect URI" />
                    <input type="text" readonly value="{{ route('admin.integrations.callback', 'tiktok') }}" onclick="this.select()"
                        class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 font-mono text-gray-600">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Client Key" />
                        <x-text-input name="tiktok_client_id" class="w-full mt-1" :value="$settings['tiktok_client_id'] ?? ''" />
                    </div>
                    <div>
                        <x-input-label value="Client Secret" />
                        <x-text-input name="tiktok_client_secret" class="w-full mt-1" :value="$settings['tiktok_client_secret'] ?? ''" />
                    </div>
                </div>
                <label class="flex items-center gap-2 text-xs text-gray-600">
                    <input type="checkbox" name="tiktok_audited" value="1" {{ ($settings['tiktok_audited'] ?? '0') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600">
                    Approved for public posting (TikTok's Content Posting API audit passed)
                </label>
                <x-secondary-button type="submit">Save TikTok Credentials</x-secondary-button>
            </div>

            {{-- Threads --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="{{ $platformMeta['threads']['icon'] }}"></i> Threads
                    </h4>
                    @if ($account = $accounts['threads'] ?? null)
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-semibold">✅ {{ $account->display_name }}</span>
                        <form method="POST" action="{{ route('admin.integrations.disconnect', $account) }}">
                            @csrf
                            <button class="text-xs text-red-500 hover:underline">Disconnect</button>
                        </form>
                    </div>
                    @elseif (filled($settings['threads_client_id'] ?? null) && filled($settings['threads_client_secret'] ?? null))
                    <a href="{{ route('admin.integrations.connect', 'threads') }}" class="text-xs font-semibold px-3 py-1.5 rounded-full text-white" style="background: var(--teal)">Connect Threads</a>
                    @else
                    <span class="text-xs text-gray-400 italic">Save credentials below, then connect</span>
                    @endif
                </div>
                <ol class="text-xs text-gray-500 list-decimal ms-4 space-y-0.5">
                    <li>Go to <span class="font-mono">developers.facebook.com/apps</span>, click "Create App," and choose the "Access Threads API" use case — this issues a <strong>separate</strong> Threads App ID/Secret, not the same as the Facebook App ID above.</li>
                    <li>Add the redirect URI below as a valid OAuth redirect URI for the Threads use case.</li>
                    <li>While in Development Mode, add yourself as a Threads tester under App Roles so you can connect immediately — no App Review needed for posting to your own account.</li>
                    <li>Copy the Threads App ID and Secret into the fields below, save, then click Connect.</li>
                </ol>
                <div>
                    <x-input-label value="Redirect URI" />
                    <input type="text" readonly value="{{ route('admin.integrations.callback', 'threads') }}" onclick="this.select()"
                        class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 font-mono text-gray-600">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Threads App ID" />
                        <x-text-input name="threads_client_id" class="w-full mt-1" :value="$settings['threads_client_id'] ?? ''" />
                    </div>
                    <div>
                        <x-input-label value="Threads App Secret" />
                        <x-text-input name="threads_client_secret" class="w-full mt-1" :value="$settings['threads_client_secret'] ?? ''" />
                    </div>
                </div>
                <x-secondary-button type="submit">Save Threads Credentials</x-secondary-button>
            </div>

            {{-- WhatsApp Notifications — the actual on/off switch. Off by
                 default even after the account above is connected, per the
                 "implement it but keep it disabled for now" request — flip
                 this on once the template is approved and you're ready. --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fab fa-whatsapp text-green-600"></i> WhatsApp Notifications
                    </h4>
                    <p class="text-xs text-gray-400 mt-1">
                        When on, every member who's opted in (Profile → Notifications) gets a WhatsApp message when a Community Post publishes, using the approved template named below.
                    </p>
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="whatsapp_notifications_enabled" value="1" {{ ($settings['whatsapp_notifications_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600">
                    Send WhatsApp notifications on publish
                    @unless ($accounts['whatsapp'] ?? null)
                    <span class="text-xs text-amber-600">(connect WhatsApp Business above first)</span>
                    @endunless
                </label>
                <div>
                    <x-input-label value="Approved template name" />
                    <x-text-input name="whatsapp_template_name" class="w-full mt-1" :value="$settings['whatsapp_template_name'] ?? ''" placeholder="e.g. new_community_post" />
                    <p class="text-xs text-gray-400 mt-1">Must exactly match a template already approved in Meta Business Manager, with one body variable (filled in with the post title).</p>
                </div>
                <x-secondary-button type="submit">Save WhatsApp Notification Settings</x-secondary-button>
            </div>

            {{-- Counseling session reminders — separate template from the
                 one above, since it's a different message (a session
                 reminder, not a new-post announcement) and needs its own
                 approved Meta template. Off by default, same as above. --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fab fa-whatsapp text-green-600"></i> WhatsApp Counseling Reminders
                    </h4>
                    <p class="text-xs text-gray-400 mt-1">
                        When on, opted-in members and counselors get a WhatsApp reminder ~24 hours before a confirmed counseling session, alongside the existing email reminder.
                    </p>
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="whatsapp_counseling_reminders_enabled" value="1" {{ ($settings['whatsapp_counseling_reminders_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600">
                    Send WhatsApp counseling reminders
                    @unless ($accounts['whatsapp'] ?? null)
                    <span class="text-xs text-amber-600">(connect WhatsApp Business above first)</span>
                    @endunless
                </label>
                <div>
                    <x-input-label value="Approved template name" />
                    <x-text-input name="whatsapp_template_name_counseling_reminder" class="w-full mt-1" :value="$settings['whatsapp_template_name_counseling_reminder'] ?? ''" placeholder="e.g. counseling_session_reminder" />
                    <p class="text-xs text-gray-400 mt-1">A separate approved template from the one above — its variable should be filled with the session's date/time.</p>
                </div>
                <x-secondary-button type="submit">Save WhatsApp Notification Settings</x-secondary-button>
            </div>

            {{-- Quran Live Class reminders — same off-by-default guarded
                 pattern, separate templates since these are two different
                 messages (a class-day nudge vs. a fee-due nudge). --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i class="fab fa-whatsapp text-green-600"></i> WhatsApp Quran Class Reminders
                    </h4>
                    <p class="text-xs text-gray-400 mt-1">
                        When on, opted-in parents get a WhatsApp reminder on days their child has a scheduled class, and again if a monthly fee is still unpaid — alongside the existing email/in-app notifications.
                    </p>
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="whatsapp_quran_reminders_enabled" value="1" {{ ($settings['whatsapp_quran_reminders_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600">
                    Send WhatsApp Quran class/fee reminders
                    @unless ($accounts['whatsapp'] ?? null)
                    <span class="text-xs text-amber-600">(connect WhatsApp Business above first)</span>
                    @endunless
                </label>
                <div>
                    <x-input-label value="Approved template name — class reminder" />
                    <x-text-input name="whatsapp_template_name_quran_class_reminder" class="w-full mt-1" :value="$settings['whatsapp_template_name_quran_class_reminder'] ?? ''" placeholder="e.g. quran_class_today" />
                    <p class="text-xs text-gray-400 mt-1">Its variable should be filled with the child's name and class time.</p>
                </div>
                <div>
                    <x-input-label value="Approved template name — fee reminder" />
                    <x-text-input name="whatsapp_template_name_quran_fee_reminder" class="w-full mt-1" :value="$settings['whatsapp_template_name_quran_fee_reminder'] ?? ''" placeholder="e.g. quran_fee_due" />
                    <p class="text-xs text-gray-400 mt-1">Its variable should be filled with the child's name and course title.</p>
                </div>
                <x-secondary-button type="submit">Save WhatsApp Notification Settings</x-secondary-button>
            </div>

            {{-- Scheduled batch posting --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-700">📅 Scheduled Posting</h4>
                    <p class="text-xs text-gray-400 mt-1">
                        Bulk-upload old photos/videos in <a href="{{ route('admin.community-posts.bulk-upload') }}" class="text-[--teal] hover:underline">Community Posts → Bulk Upload</a> — they land in the <a href="{{ route('admin.community-posts.queue') }}" class="text-[--teal] hover:underline">Queue</a>, and this many go out automatically every day.
                    </p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Posts per day (0 pauses auto-posting)" />
                        <x-text-input name="scheduled_batch_size" type="number" min="0" max="50" class="w-full mt-1" :value="$settings['scheduled_batch_size'] ?? '3'" />
                    </div>
                    <div>
                        <x-input-label value="Time of day" />
                        <x-text-input name="scheduled_batch_time" type="time" class="w-full mt-1" :value="$settings['scheduled_batch_time'] ?? '09:00'" />
                    </div>
                </div>
                <x-secondary-button type="submit">Save Scheduled Posting Settings</x-secondary-button>
            </div>

            {{-- Every button above and this one submit the exact same form,
                 so any of them saves everything currently filled in on this
                 page — they're just placed next to the section someone is
                 actually editing instead of forcing a scroll to one button
                 at the very bottom. --}}
            <button class="text-white text-sm font-semibold px-5 py-2.5 rounded-lg" style="background: var(--teal)">Save All Integration Settings</button>
        </form>

    </div>
</x-admin-layout>
