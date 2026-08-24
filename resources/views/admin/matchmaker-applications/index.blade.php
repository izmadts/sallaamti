<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nikah Counselor Applications</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-4">
                <form method="GET" class="flex flex-wrap gap-2 items-end">
                    <div>
                        <label class="text-xs text-gray-500 block mb-1">Stage</label>
                        <select name="status" class="border-gray-300 rounded-md text-sm" onchange="this.form.submit()">
                            <option value="">All</option>
                            @foreach (\App\Models\MatchmakerApplication::STEPS as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="withdrawn" {{ request('status') === 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                        </select>
                    </div>
                </form>
            </div>

            @if ($directRoleUsers->isNotEmpty())
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-amber-50 border-b border-amber-100">
                    <p class="font-semibold text-amber-800 text-sm">⚠️ Assigned Directly by Admin — no application record</p>
                    <p class="text-xs text-amber-700 mt-1">These accounts have the Nikah Counselor role but never went through the 10-stage pipeline below — admin granted it directly from Users → Roles. They still earn commission at the base tier (no counselor code, no certificate, no public level until a formal record exists).</p>
                </div>
                <div class="divide-y">
                    @foreach ($directRoleUsers as $user)
                    <a href="{{ route('admin.users.show', $user) }}" class="p-5 flex justify-between items-center flex-wrap gap-3 hover:bg-gray-50 transition">
                        <div>
                            <p class="font-medium text-gray-800">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $user->email ?: $user->phone }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full font-semibold bg-amber-100 text-amber-800">Role Assigned by Admin</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($applications as $application)
                <a href="{{ route('admin.matchmaker-applications.show', $application) }}" class="p-5 flex justify-between items-start flex-wrap gap-3 hover:bg-gray-50 transition">
                    <div>
                        <p class="font-medium text-gray-800">{{ $application->full_name }}
                            @if ($application->counselor_code)
                            <span class="text-xs font-normal text-gray-400">· {{ $application->counselor_code }}</span>
                            @endif
                            <span class="text-xs px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 font-normal align-middle">Via Application</span>
                        </p>
                        <p class="text-sm text-gray-500">{{ $application->mobile_number }} @if ($application->area) · {{ $application->area }} @endif</p>
                        <p class="text-xs text-gray-400 mt-1">Applied {{ $application->created_at->format('d M Y, h:i A') }}</p>
                    </div>

                    <span class="text-xs px-2 py-1 rounded-full font-semibold
                        {{ match(true) {
                            $application->status === 'certified' => 'bg-green-100 text-green-800',
                            $application->isTerminal() => 'bg-red-100 text-red-800',
                            default => 'bg-amber-100 text-amber-800',
                        } }}">
                        {{ \App\Models\MatchmakerApplication::STEPS[$application->status] ?? ucfirst($application->status) }}
                    </span>
                </a>
                @empty
                <p class="p-5 text-gray-500">No applications yet.</p>
                @endforelse
            </div>

            {{ $applications->links() }}
        </div>
    </div>
</x-admin-layout>
