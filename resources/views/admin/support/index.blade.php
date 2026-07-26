<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Support Queries</h2>
    </x-slot>
    <div class="space-y-6 max-w-6xl">

        @if (session('status'))
        <div class="p-4 bg-green-50 text-green-700 rounded-xl text-sm">✅ {{ session('status') }}</div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([
            ['New', $stats['new'], 'yellow', route('admin.support.index', ['status' => 'new'])],
            ['Assigned', $stats['assigned'], 'blue', route('admin.support.index', ['status' => 'assigned'])],
            ['In Progress', $stats['in_progress'], 'purple', route('admin.support.index', ['status' => 'in_progress'])],
            ['Resolved', $stats['resolved'], 'green', route('admin.support.index', ['status' => 'resolved'])],
            ] as $stat)
            <a href="{{ $stat[3] }}" class="bg-white rounded-xl shadow-sm p-5 text-center border-t-4 border-{{ $stat[2] }}-400 hover:shadow-md transition-shadow">
                <div class="text-3xl font-bold text-gray-800">{{ $stat[1] }}</div>
                <div class="text-xs text-gray-500 mt-1 uppercase tracking-wider">{{ $stat[0] }}</div>
            </a>
            @endforeach
        </div>

        {{-- Filters --}}
        <form method="GET" class="bg-white rounded-xl shadow-sm p-4 flex flex-wrap gap-3 items-end">
            <div>
                <label class="text-xs font-semibold text-gray-500 block mb-1">Status</label>
                <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500">
                    <option value="">All</option>
                    @foreach (['new', 'assigned', 'in_progress', 'resolved', 'closed'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 block mb-1">Category</label>
                <select name="category" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500">
                    <option value="">All</option>
                    @foreach (['marital', 'parenting', 'financial', 'legal', 'spiritual', 'other'] as $c)
                    <option value="{{ $c }}" {{ request('category') === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500 block mb-1">Priority</label>
                <select name="priority" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500">
                    <option value="">All</option>
                    @foreach (['high', 'medium', 'low'] as $p)
                    <option value="{{ $p }}" {{ request('priority') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            <button class="px-4 py-2 rounded-lg text-white text-sm font-semibold" style="background: #0d6b6b">Filter</button>
            @if (request()->hasAny(['status', 'category', 'priority']))
            <a href="{{ route('admin.support.index') }}" class="text-sm text-gray-400 hover:text-gray-600">✕ Clear</a>
            @endif
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Subject</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Priority</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Submitted</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($queries as $query)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-800 max-w-xs">
                                <p class="truncate">{{ $query->subject }}</p>
                                <p class="text-xs text-gray-400">{{ $query->responses()->count() }} response(s)</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $query->is_anonymous ? '🎭 Anonymous' : $query->user?->name }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">{{ ucfirst($query->category) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-semibold {{ $query->priority === 'high' ? 'text-red-600' : ($query->priority === 'medium' ? 'text-yellow-600' : 'text-green-600') }}">
                                    {{ ucfirst($query->priority) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                        {{ $query->status === 'resolved' ? 'bg-green-100 text-green-700' :
                                           ($query->status === 'in_progress' ? 'bg-purple-100 text-purple-700' :
                                           ($query->status === 'assigned' ? 'bg-blue-100 text-blue-700' :
                                           ($query->status === 'closed' ? 'bg-gray-100 text-gray-500' : 'bg-yellow-100 text-yellow-700'))) }}">
                                    {{ ucfirst(str_replace('_', ' ', $query->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-400">{{ $query->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.support.show', $query) }}" class="text-xs px-3 py-1 rounded border border-gray-200 text-gray-600 hover:bg-gray-50">
                                    Open →
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                                <div class="text-3xl mb-2">🤝</div>
                                No support queries found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-100">{{ $queries->links() }}</div>
        </div>
    </div>
</x-admin-layout>