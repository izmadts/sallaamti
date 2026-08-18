<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.community-posts.index') }}" class="text-gray-400 hover:text-gray-600">Community Posts</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">New Post</span>
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

        <form method="POST" action="{{ route('admin.community-posts.store') }}" enctype="multipart/form-data" class="space-y-4" x-data="{ tags: '' }">
            @csrf

            <div>
                <x-input-label value="Title" />
                <x-text-input name="title" class="w-full mt-1" value="{{ old('title') }}" placeholder="Ramadan Food Drive" required />
            </div>

            <div>
                <x-input-label value="Event Date (optional)" />
                <x-text-input name="event_at" type="datetime-local" class="w-full mt-1" value="{{ old('event_at') }}" />
            </div>

            <div>
                <x-input-label value="Body" />
                <textarea name="body" rows="6" class="w-full mt-1 border-gray-300 rounded-lg text-sm" required>{{ old('body') }}</textarea>
            </div>

            <div>
                <x-input-label value="Photo (optional)" />
                <input type="file" name="photo" accept="image/*" class="w-full mt-1">
            </div>

            <div>
                <x-input-label value="Tags" />
                <input type="text" name="tags" x-model="tags" class="w-full mt-1 border-gray-300 rounded-lg text-sm"
                    value="{{ old('tags') }}" placeholder="Activity, Ramadan, Youth">
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

            {{-- Social posting — not functional yet, no credentials are
                 configured anywhere in this app. Visible so the workflow is
                 ready to wire up once accounts are connected, but disabled
                 so nothing pretends to work. --}}
            <div class="p-4 rounded-lg bg-gray-50 border border-gray-200">
                <p class="text-sm font-semibold text-gray-500 mb-1">📣 Share to social media</p>
                <p class="text-xs text-gray-400 mb-3">Connect your account in Settings first — nothing is sent yet.</p>
                <div class="flex flex-wrap gap-3">
                    @foreach (['Facebook', 'Instagram', 'YouTube', 'TikTok'] as $platform)
                    <label class="flex items-center gap-1.5 text-sm text-gray-400 cursor-not-allowed">
                        <input type="checkbox" disabled class="rounded border-gray-300">
                        {{ $platform }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="publish" value="1" id="publish" checked>
                <label for="publish" class="text-sm text-gray-700">Publish now (uncheck to save as draft)</label>
            </div>

            <x-primary-button>Save Post</x-primary-button>
        </form>
    </div>
</x-admin-layout>
