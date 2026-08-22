<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600">Dashboard</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Testimonials</span>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-4">

        <div class="flex justify-end">
            <a href="{{ route('admin.testimonials.create') }}" class="bg-teal-700 text-white text-sm px-4 py-2 rounded hover:bg-teal-800">+ Add Testimonial</a>
        </div>

        <div class="bg-white rounded-lg shadow-sm divide-y">
            @forelse ($testimonials as $t)
            <div class="p-4 flex gap-4 items-start">
                <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-100 flex-shrink-0">
                    @if ($t->photo)
                    <img src="{{ Storage::url($t->photo) }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-lg">👤</div>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-800">
                        {{ $t->name }} <span class="text-gray-400 text-xs font-normal">— {{ $t->location }}</span>
                        @if ($t->user)
                        <span class="text-xs px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 ml-1">Member submission</span>
                        @endif
                    </p>
                    <p class="text-xs text-yellow-500 mb-1">{{ str_repeat('★', $t->rating) }}{{ str_repeat('☆', 5 - $t->rating) }}</p>
                    <p class="text-sm text-gray-600 line-clamp-2">{{ $t->content }}</p>
                    @if ($t->status === 'rejected' && $t->rejection_reason)
                    <p class="text-xs text-red-600 mt-1">Rejected: {{ $t->rejection_reason }}</p>
                    @endif
                </div>
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    <div class="flex gap-1.5">
                        <span class="text-xs px-2 py-0.5 rounded-full
                            {{ $t->status === 'approved' ? 'bg-green-100 text-green-700' : ($t->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($t->status) }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $t->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $t->is_active ? 'Showing' : 'Hidden' }}
                        </span>
                    </div>
                    <div class="flex gap-2">
                        @if ($t->status === 'pending')
                        <form method="POST" action="{{ route('admin.testimonials.approve', $t) }}">
                            @csrf
                            <button class="text-xs text-green-600 hover:underline font-semibold">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('admin.testimonials.reject', $t) }}" onsubmit="return promptRejectReason(this)">
                            @csrf
                            <input type="hidden" name="rejection_reason">
                            <button type="submit" class="text-xs text-red-600 hover:underline font-semibold">Reject</button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('admin.testimonials.toggle', $t) }}">
                            @csrf
                            <button class="text-xs {{ $t->is_active ? 'text-orange-500' : 'text-green-600' }} hover:underline">
                                {{ $t->is_active ? 'Hide' : 'Show' }}
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('admin.testimonials.edit', $t) }}" class="text-xs text-blue-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.testimonials.destroy', $t) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <p class="p-5 text-gray-400">No testimonials yet.</p>
            @endforelse
        </div>
    </div>

    <script>
        function promptRejectReason(form) {
            const reason = prompt('Reason for rejecting this testimonial (optional, shown to the member):');
            if (reason === null) return false;
            form.querySelector('[name=rejection_reason]').value = reason;
            return true;
        }
    </script>
</x-admin-layout>