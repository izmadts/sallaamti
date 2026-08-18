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
        ];
    @endphp

    <div class="w-full space-y-6">

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 border-b pb-2">📣 Social Media Auto-Posting</h3>
            <p class="text-xs text-gray-500 mt-2">
                Connect each platform below, then check the platforms you want under "Share to social media" when publishing a Community Post — it posts automatically the moment you publish.
                WhatsApp isn't listed: it has no public API for posting to a feed/wall the way these five do, so it stays a plain contact link in Settings.
            </p>
            <p class="text-xs text-gray-500 mt-2">
                <strong>Facebook Page + Instagram share one connection</strong> — Instagram Business accounts are always linked through a Facebook Page, so there's no separate Instagram login; connecting Facebook below picks up the linked Instagram account automatically if one exists.
            </p>
        </div>

        {{-- Facebook / Instagram --}}
        <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i class="{{ $platformMeta['facebook']['icon'] }}"></i> Facebook Page
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
            <ol class="text-xs text-gray-500 list-decimal ms-4 space-y-0.5">
                <li>Uses the same Meta App configured under Settings → Social Login — make sure an App ID/Secret is saved there first.</li>
                <li>In that Meta App, add the "Facebook Login for Business" product and, under its settings, add the redirect URI below as a valid OAuth redirect URI.</li>
                <li>While the app is in Development Mode, add yourself as an Admin/Tester on the app so you can connect immediately — no App Review needed for posting to your own Page.</li>
                <li>Click Connect and choose the Page you manage when prompted by Facebook.</li>
            </ol>
            <div>
                <x-input-label value="Valid OAuth redirect URI" />
                <input type="text" readonly value="{{ route('admin.integrations.callback', 'facebook') }}" onclick="this.select()"
                    class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 font-mono text-gray-600">
            </div>
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
                    @else
                    <a href="{{ route('admin.integrations.connect', 'twitter') }}" class="text-xs font-semibold px-3 py-1.5 rounded-full text-white" style="background: var(--teal)">Connect X</a>
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
                    @else
                    <a href="{{ route('admin.integrations.connect', 'youtube') }}" class="text-xs font-semibold px-3 py-1.5 rounded-full text-white" style="background: var(--teal)">Connect YouTube</a>
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
                    @else
                    <a href="{{ route('admin.integrations.connect', 'tiktok') }}" class="text-xs font-semibold px-3 py-1.5 rounded-full text-white" style="background: var(--teal)">Connect TikTok</a>
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
            </div>

            <button class="text-white text-sm font-semibold px-5 py-2.5 rounded-lg" style="background: var(--teal)">Save Integration Settings</button>
        </form>

    </div>
</x-admin-layout>
