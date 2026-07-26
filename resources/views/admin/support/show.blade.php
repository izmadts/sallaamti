<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">{{ $query->subject }}</h2>
            <a href="{{ route('admin.support.index') }}" class="text-sm text-gray-500 hover:underline">← All Queries</a>
        </div>
    </x-slot>
    <div class="max-w-5xl space-y-5">

        @if (session('status'))
        <div class="p-4 bg-green-50 text-green-700 rounded-xl text-sm">✅ {{ session('status') }}</div>
        @endif

        <div class="grid lg:grid-cols-3 gap-5">

            {{-- Thread --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Original query --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="font-bold text-gray-800">{{ $query->is_anonymous ? '🎭 Anonymous User' : $query->user?->name }}</p>
                            <p class="text-xs text-gray-400">{{ $query->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">{{ ucfirst($query->category) }}</span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full
                                {{ $query->priority === 'high' ? 'bg-red-100 text-red-700' :
                                   ($query->priority === 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                {{ ucfirst($query->priority) }}
                            </span>
                        </div>
                    </div>
                    <p class="text-gray-600 leading-relaxed">{{ $query->description }}</p>
                </div>

                {{-- Responses --}}
                @foreach ($query->responses as $response)
                @php $isAdmin = $response->responder->hasRole('admin') || $response->responder->hasRole('counselor'); @endphp
                @if (!$response->is_internal || auth()->user()->hasRole('admin'))
                <div class="flex {{ $isAdmin ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-lg rounded-2xl p-4 text-sm {{ $isAdmin ? 'text-white' : 'bg-white text-gray-800 shadow-sm' }} {{ $response->is_internal ? 'border-2 border-dashed border-orange-300' : '' }}"
                        style="{{ $isAdmin ? 'background: #0d6b6b' : '' }}">
                        @if ($response->is_internal)
                        <p class="text-xs font-bold text-orange-500 mb-1">🔒 Internal Note</p>
                        @endif
                        <p class="font-semibold text-xs mb-1 {{ $isAdmin ? 'text-teal-100' : 'text-gray-400' }}">
                            {{ $response->responder->name }}
                        </p>
                        <p class="leading-relaxed">{{ $response->message }}</p>
                        <p class="text-xs mt-2 {{ $isAdmin ? 'text-teal-200' : 'text-gray-400' }}">
                            {{ $response->created_at->format('d M, h:i A') }}
                        </p>
                    </div>
                </div>
                @endif
                @endforeach

                {{-- Reply Form --}}
                <div class="bg-white rounded-2xl shadow-sm p-5">
                    <h5 class="font-semibold text-gray-700 mb-3 text-sm">Reply</h5>
                    <form method="POST" action="{{ route('admin.support.reply', $query) }}" class="space-y-3">
                        @csrf
                        <textarea name="message" rows="4" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-teal-500 resize-none"
                            placeholder="Write your response..."></textarea>
                        <div class="flex justify-between items-center">
                            <label class="flex items-center gap-2 text-sm text-gray-500 cursor-pointer">
                                <input type="checkbox" name="is_internal" value="1" class="auth-checkbox">
                                Internal note only (admin/counselor only — user won't see)
                            </label>
                            <button class="btn-base btn-teal text-sm px-5 py-2 font-semibold">
                                Send Response
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-4">

                {{-- Status --}}
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h5 class="font-semibold text-gray-700 mb-3 text-sm">Update Status</h5>
                    <form method="POST" action="{{ route('admin.support.status', $query) }}" class="space-y-2">
                        @csrf
                        <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500">
                            @foreach (['new', 'assigned', 'in_progress', 'resolved', 'closed'] as $s)
                            <option value="{{ $s }}" {{ $query->status === $s ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $s)) }}
                            </option>
                            @endforeach
                        </select>
                        <button class="w-full py-2 rounded-lg text-white text-sm font-semibold" style="background: #0d6b6b">
                            Update Status
                        </button>
                    </form>
                </div>

                {{-- Assign --}}
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h5 class="font-semibold text-gray-700 mb-3 text-sm">Assign Counselor</h5>
                    <form method="POST" action="{{ route('admin.support.assign', $query) }}" class="space-y-2">
                        @csrf
                        <select name="assigned_to" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500">
                            <option value="">-- Select Counselor --</option>
                            @foreach ($counselors as $c)
                            <option value="{{ $c->id }}" {{ $query->assigned_to === $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                            @endforeach
                        </select>
                        <button class="w-full py-2 rounded-lg text-white text-sm font-semibold" style="background: #b8962e">
                            Assign
                        </button>
                    </form>
                    @if ($query->assignedTo)
                    <p class="text-xs text-gray-500 mt-2">Currently: {{ $query->assignedTo->name }}</p>
                    @endif
                </div>

                {{-- Query Info --}}
                <div class="bg-white rounded-xl shadow-sm p-5 text-sm">
                    <h5 class="font-semibold text-gray-700 mb-3">Query Details</h5>
                    <div class="space-y-2 text-gray-600">
                        <div class="flex justify-between"><span class="text-gray-400">User</span><span>{{ $query->is_anonymous ? '🎭 Anonymous' : $query->user?->name }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-400">Category</span><span>{{ ucfirst($query->category) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-400">Priority</span><span>{{ ucfirst($query->priority) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-400">Responses</span><span>{{ $query->responses->count() }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-400">Submitted</span><span>{{ $query->created_at->format('d M Y') }}</span></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-admin-layout>