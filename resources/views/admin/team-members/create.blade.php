<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.team-members.index') }}" class="text-gray-400 hover:text-gray-600">Team</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Add Member</span>
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

        <form method="POST" action="{{ route('admin.team-members.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <x-input-label value="Name" />
                <x-text-input name="name" class="w-full mt-1" value="{{ old('name') }}" required />
            </div>
            <div>
                <x-input-label value="Role / Title" />
                <x-text-input name="role" class="w-full mt-1" value="{{ old('role') }}" placeholder="Lead Quran Teacher" required />
            </div>
            <div>
                <x-input-label value="Bio (optional)" />
                <input id="trix-bio" type="hidden" name="bio" value="{{ old('bio') }}">
                <trix-editor input="trix-bio"></trix-editor>
            </div>
            <div>
                <x-input-label value="Photo (optional)" />
                <input type="file" name="photo" accept="image/*" class="w-full mt-1">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" checked>
                <label for="is_active" class="text-sm text-gray-700">Show on team page</label>
            </div>
            <x-primary-button>Add Team Member</x-primary-button>
        </form>
    </div>
</x-admin-layout>
