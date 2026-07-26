<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">My Support Queries</h2>
            <a href="{{ route('support.create') }}" class="btn-base btn-teal text-sm px-4 py-2">+ New Query</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-xl">✅ {{ session('status') }}</div>
            @endif
            @forelse ($queries as $query)
            <a href="{{ route('support.show', $query) }}"
                class="block bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start gap-3">
                    <div>
                        <h4 class="font-semibold text-gray-800">{{ $query->subject }}</h4>
                        <p class="text-sm text-gray-500 mt-0.5">
                            {{ ucfirst($query->category) }} ·
                            {{ $query->responses()->count() }} response(s) ·
                            {{ $query->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full flex-shrink-0
                            {{ $query->status === 'resolved' ? 'bg-green-100 text-green-700' :
                               ($query->status === 'in_progress' ? 'bg-purple-100 text-purple-700' :
                               ($query->status === 'assigned' ? 'bg-blue-100 text-blue-700' :
                               ($query->status === 'closed' ? 'bg-gray-100 text-gray-500' : 'bg-yellow-100 text-yellow-700'))) }}">
                        {{ ucfirst(str_replace('_', ' ', $query->status)) }}
                    </span>
                </div>
            </a>
            @empty
            <div class="text-center py-16 bg-white rounded-2xl shadow-sm">
                <div class="text-5xl mb-3">🤝</div>
                <h3 class="font-bold text-gray-700 mb-2">No Queries Yet</h3>
                <p class="text-gray-400 text-sm mb-6">Submit a query and our counselors will help you.</p>
                <a href="{{ route('support.create') }}" class="btn-base btn-teal px-6 py-2">Submit Your First Query</a>
            </div>
            @endforelse
            {{ $queries->links() }}
        </div>
    </div>
</x-app-layout>