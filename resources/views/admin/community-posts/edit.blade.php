<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.community-posts.index') }}" class="text-gray-400 hover:text-gray-600">Community Posts</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Edit Post</span>
        </div>
    </x-slot>

    <div class="max-w-2xl bg-white rounded-lg shadow-sm p-6">
        @if ($errors->any())
        <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if ($post->photo)
        <img src="{{ Storage::url($post->photo) }}" class="w-full h-40 object-cover rounded-lg mb-4">
        @endif

        <form method="POST" action="{{ route('admin.community-posts.update', $post) }}" enctype="multipart/form-data" class="space-y-4" x-data="{ tags: '{{ old('tags', implode(', ', $post->tags ?? [])) }}' }">
            @csrf
            @method('PUT')

            <div>
                <x-input-label value="Title" />
                <x-text-input name="title" class="w-full mt-1" value="{{ old('title', $post->title) }}" required />
            </div>

            <div>
                <x-input-label value="Event Date (optional)" />
                <x-text-input name="event_at" type="datetime-local" class="w-full mt-1" value="{{ old('event_at', $post->event_at?->format('Y-m-d\TH:i')) }}" />
            </div>

            <div>
                <x-input-label value="Body" />
                <textarea name="body" rows="6" class="w-full mt-1 border-gray-300 rounded-lg text-sm" required>{{ old('body', $post->body) }}</textarea>
            </div>

            <div>
                <x-input-label value="Photo (optional — replaces the current one)" />
                <input type="file" name="photo" accept="image/*" class="w-full mt-1">
            </div>

            <div>
                <x-input-label value="Video (optional — replaces the current one, max 50MB)" />
                @if ($post->video)
                <p class="text-xs text-gray-400 mb-1">Current: {{ basename($post->video) }}</p>
                @endif
                <input type="file" name="video" accept="video/mp4,video/quicktime,video/webm" class="w-full mt-1">
            </div>

            <div>
                <x-input-label value="Tags" />
                <input type="text" name="tags" x-model="tags" class="w-full mt-1 border-gray-300 rounded-lg text-sm" placeholder="Activity, Ramadan, Youth">
                <p class="text-xs text-gray-400 mt-1">Comma-separated. These become the filter tags on the Sallaamti Wall.</p>
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
                <p class="text-sm font-semibold text-gray-700 mb-1">📣 Share to social media</p>
                <p class="text-xs text-gray-400 mb-3">
                    Posts automatically the next time this moves from draft to published. Nothing connected yet? <a href="{{ route('admin.integrations.index') }}" class="text-[--teal] hover:underline">Connect accounts in Integrations</a>.
                </p>
                <div class="flex flex-wrap gap-3">
                    @foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'twitter' => 'X (Twitter)', 'youtube' => 'YouTube', 'tiktok' => 'TikTok'] as $key => $label)
                    <label class="flex items-center gap-1.5 text-sm {{ in_array($key, $connectedPlatforms) ? 'text-gray-700' : 'text-gray-400 cursor-not-allowed' }}">
                        <input type="checkbox" name="social_targets[]" value="{{ $key }}"
                            {{ in_array($key, old('social_targets', $post->social_targets ?? [])) ? 'checked' : '' }}
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

            @if ($post->status === 'scheduled')
            <div class="p-3 rounded-lg bg-blue-50 border border-blue-100 text-sm text-blue-700">
                📋 This post is in the scheduled queue. Saving here only updates its content — use the <a href="{{ route('admin.community-posts.queue') }}" class="underline">Queue page</a> to publish it now or reorder it.
            </div>
            @else
            <div class="flex items-center gap-2">
                <input type="checkbox" name="publish" value="1" id="publish" {{ $post->status === 'published' ? 'checked' : '' }}>
                <label for="publish" class="text-sm text-gray-700">Published (uncheck to move to draft)</label>
            </div>
            @endif

            <x-primary-button>Save Changes</x-primary-button>
        </form>
    </div>
</x-admin-layout>
