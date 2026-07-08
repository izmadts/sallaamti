<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.activity-posts.index') }}" class="text-gray-400 hover:text-gray-600">Community Activities</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Add Activity</span>
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

        <form method="POST" action="{{ route('admin.activity-posts.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <x-input-label value="Title" />
                <x-text-input name="title" class="w-full mt-1" value="{{ old('title') }}" placeholder="Ramadan Food Drive" required />
            </div>
            <div>
                <x-input-label value="Date" />
                <x-text-input name="activity_date" type="date" class="w-full mt-1" value="{{ old('activity_date') }}" />
            </div>
            <div>
                <x-input-label value="Description" />
                <input id="trix-description" type="hidden" name="description" value="{{ old('description') }}">
                <trix-editor input="trix-description"></trix-editor>
            </div>
            <div>
                <x-input-label value="Photo (optional)" />
                <input type="file" name="photo" accept="image/*" class="w-full mt-1">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" checked>
                <label for="is_active" class="text-sm text-gray-700">Show on activities page</label>
            </div>
            <x-primary-button>Add Activity</x-primary-button>
        </form>
    </div>
</x-admin-layout>
