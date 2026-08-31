<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.community-posts.index') }}" class="text-gray-400 hover:text-gray-600">Community Posts</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Bulk Upload</span>
        </div>
    </x-slot>

    <div class="max-w-2xl bg-white rounded-lg shadow-sm p-6">
        <p class="text-sm text-gray-500 mb-4">
            Select as many photos and videos as you like (up to 30 at a time), titled from each filename for now.
        </p>

        @if ($errors->any())
        <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.community-posts.bulk-store') }}" enctype="multipart/form-data" class="space-y-4" x-data="{ tags: '', count: 0, mode: 'instant' }">
            @csrf

            <div>
                <x-input-label value="Photos & videos" />
                <input type="file" name="files[]" accept="image/*,video/*" multiple required class="w-full mt-1"
                    @change="count = $event.target.files.length">
                <p class="text-xs text-gray-400 mt-1" x-show="count > 0" x-cloak><span x-text="count"></span> file(s) selected. Images max 2MB each, videos max 50MB each.</p>
            </div>

            <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                <x-input-label value="When should these go live?" />
                <div class="flex gap-4 mt-2">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="radio" name="mode" value="instant" x-model="mode" checked class="text-teal-600">
                        Publish instantly — appears on the Wall right away
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="radio" name="mode" value="queue" x-model="mode" class="text-teal-600">
                        Add to Queue — review captions and release on your own schedule
                    </label>
                </div>
                <p class="text-xs text-gray-400 mt-2" x-show="mode === 'queue'" x-cloak>
                    Queued items won't appear on the Wall until you publish them (individually, or via the daily scheduled batch) from the <a href="{{ route('admin.community-posts.queue') }}" class="text-[--teal] hover:underline">Queue</a> page.
                </p>
            </div>

            <div>
                <x-input-label value="Tags (applied to every item)" />
                <input type="text" name="tags" x-model="tags" class="w-full mt-1 border-gray-300 rounded-lg text-sm"
                    placeholder="Activity, Ramadan, Youth">
                <div class="flex gap-2 mt-2">
                    @foreach (['Activity', 'Event', 'Sermon'] as $quick)
                    <button type="button"
                        @click="tags = tags ? tags + ', {{ $quick }}' : '{{ $quick }}'"
                        class="text-xs font-semibold px-2.5 py-1 rounded-full border border-gray-200 text-gray-600 hover:border-teal-400 hover:text-teal-700 transition">
                        + {{ $quick }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                <p class="text-sm font-semibold text-gray-700 mb-1">📣 Share to social media (applied to every item)</p>
                <p class="text-xs text-gray-400 mb-3">
                    Nothing connected yet? <a href="{{ route('admin.integrations.index') }}" class="text-[--teal] hover:underline">Connect accounts in Integrations</a>.
                </p>
                <div class="mb-4">
                    @php $hashtagsTooltip = "Space or comma-separated keywords, with or without '#'. Appended to the caption on Facebook, Instagram, X, Threads & TikTok, and added as searchable tags on YouTube.\n\nRecommended lengths: X/Twitter ~1-2 hashtags (280 char post limit), Instagram 3-8 (2200 char max), Threads 500 char max, TikTok 150 char max caption, Facebook ~2000 char max. YouTube's keyword tags are automatically capped at 500 combined characters, so extra ones are safely dropped rather than failing the upload."; @endphp
                    <x-input-label for="hashtags" value="Hashtags / keywords (applied to every item)" title="{{ $hashtagsTooltip }}" />
                    <x-text-input id="hashtags" name="hashtags" class="w-full mt-1" :value="old('hashtags')" placeholder="#Islam #Sallaamti #Community" title="{{ $hashtagsTooltip }}" />
                    <p class="text-xs text-gray-400 mt-1">Space or comma-separated, with or without '#'. Hover the field for per-platform length guidance.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    @foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'twitter' => 'X (Twitter)', 'youtube' => 'YouTube', 'tiktok' => 'TikTok', 'threads' => 'Threads'] as $key => $label)
                    <label class="flex items-center gap-1.5 text-sm {{ in_array($key, $connectedPlatforms) ? 'text-gray-700' : 'text-gray-400 cursor-not-allowed' }}">
                        <input type="checkbox" name="social_targets[]" value="{{ $key }}"
                            {{ in_array($key, $connectedPlatforms) ? '' : 'disabled' }}
                            class="rounded border-gray-300 text-teal-600">
                        {{ $label }}
                        @unless (in_array($key, $connectedPlatforms))
                        <span class="text-[10px] text-gray-400">(not connected)</span>
                        @endunless
                    </label>
                    @endforeach
                </div>
            </div>

            <x-primary-button x-text="mode === 'instant' ? 'Publish Now' : 'Add to Queue'">Publish Now</x-primary-button>
        </form>
    </div>
</x-admin-layout>
