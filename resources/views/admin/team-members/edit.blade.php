<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.team-members.index') }}" class="text-gray-400 hover:text-gray-600">Team</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Edit Member</span>
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

        <form method="POST" action="{{ route('admin.team-members.update', $teamMember) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <x-input-label value="Name" />
                <x-text-input name="name" class="w-full mt-1" value="{{ old('name', $teamMember->name) }}" required />
            </div>
            <div>
                <x-input-label value="Role / Title" />
                <x-text-input name="role" class="w-full mt-1" value="{{ old('role', $teamMember->role) }}" required />
            </div>
            <div>
                <x-input-label value="Bio (optional)" />
                <input id="trix-bio" type="hidden" name="bio" value="{{ old('bio', $teamMember->bio) }}">
                <trix-editor input="trix-bio"></trix-editor>
            </div>
            <div>
                <x-input-label value="Photo" />
                @if ($teamMember->photo)
                <div class="mt-1 mb-2">
                    <img src="{{ Storage::url($teamMember->photo) }}" class="w-16 h-16 rounded-full object-cover">
                </div>
                @endif
                <input type="file" name="photo" accept="image/*" class="w-full mt-1">
                <p class="text-xs text-gray-400 mt-1">Shown in a square frame — a centered headshot works best.</p>
            </div>

            <div class="border-t pt-4">
                <x-input-label value="Social Links (optional)" />
                <div class="grid grid-cols-2 gap-3 mt-1">
                    <x-text-input name="facebook_url" class="w-full" value="{{ old('facebook_url', $teamMember->facebook_url) }}" placeholder="Facebook URL" />
                    <x-text-input name="instagram_url" class="w-full" value="{{ old('instagram_url', $teamMember->instagram_url) }}" placeholder="Instagram URL" />
                    <x-text-input name="tiktok_url" class="w-full" value="{{ old('tiktok_url', $teamMember->tiktok_url) }}" placeholder="TikTok URL" />
                    <x-text-input name="whatsapp_number" class="w-full" value="{{ old('whatsapp_number', $teamMember->whatsapp_number) }}" placeholder="WhatsApp number (e.g. 92300...)" />
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_founder" value="1" id="is_founder" {{ old('is_founder', $teamMember->is_founder) ? 'checked' : '' }}>
                <label for="is_founder" class="text-sm text-gray-700">Feature as Founder (shown large at the top of the team page — only one at a time)</label>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $teamMember->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="text-sm text-gray-700">Show on team page</label>
            </div>
            <x-primary-button>Save Changes</x-primary-button>
        </form>
    </div>
</x-admin-layout>
