<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.certificates.index') }}" class="text-gray-400 hover:text-gray-600">Certificates</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Generate</span>
        </div>
    </x-slot>

    <div class="max-w-lg bg-white rounded-lg shadow-sm p-6">
        @if ($errors->any())
        <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.certificates.store') }}" class="space-y-4">
            @csrf
            <div>
                <x-input-label value="Member" />
                <select name="user_id" class="border-gray-300 rounded-md w-full mt-1" required>
                    <option value="">Select a member...</option>
                    @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label value="Certificate Title" />
                <x-text-input name="title" list="title-suggestions" class="w-full mt-1" value="{{ old('title') }}" placeholder="Certificate of Membership" required />
                <datalist id="title-suggestions">
                    <option value="Certificate of Membership">
                    <option value="Certificate of Volunteering">
                    <option value="Certificate of Appreciation">
                </datalist>
            </div>
            <x-primary-button>Generate Certificate</x-primary-button>
        </form>
    </div>
</x-admin-layout>
