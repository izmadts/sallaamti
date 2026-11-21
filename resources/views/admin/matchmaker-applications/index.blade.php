<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nikah Counselor Applications</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

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
                    <div class="p-5 flex justify-between items-center flex-wrap gap-3 hover:bg-gray-50 transition cursor-pointer" onclick="window.location='{{ route('admin.users.show', $user) }}'">
                        <div>
                            <p class="font-medium text-gray-800">{{ $user->name }}</p>
                            @if ($user->email)
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            @else
                            <p class="text-sm text-gray-500" onclick="event.stopPropagation()"><x-phone-actions :phone="$user->phone" /></p>
                            @endif
                            @if ($stats = $activityStats[$user->id] ?? null)
                            <p class="text-xs text-gray-400 mt-1">{{ $stats['leads'] }} leads · {{ $stats['touched'] }} touched · {{ $stats['status_changes'] }} status changes</p>
                            @endif
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full font-semibold bg-amber-100 text-amber-800">Role Assigned by Admin</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm divide-y">
                @forelse ($applications as $application)
                {{-- Owner (21 Nov 2026): was a whole-row <a> — switched to a
                     clickable <div> so the phone's own WhatsApp/copy link
                     (x-phone-actions) can live in the row without nesting
                     an <a> inside an <a>, same stopPropagation pattern
                     admin/leads/index.blade.php already uses. --}}
                <div class="p-5 flex justify-between items-start flex-wrap gap-3 hover:bg-gray-50 transition cursor-pointer" onclick="window.location='{{ route('admin.matchmaker-applications.show', $application) }}'">
                    <div>
                        <p class="font-medium text-gray-800">{{ $application->full_name }}
                            @if ($application->counselor_code)
                            <span class="text-xs font-normal text-gray-400">· {{ $application->counselor_code }}</span>
                            @endif
                            <span class="text-xs px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 font-normal align-middle">Via Application</span>
                        </p>
                        <p class="text-sm text-gray-500" onclick="event.stopPropagation()">
                            <x-phone-actions :phone="$application->mobile_number" /> @if ($application->area) · {{ $application->area }} @endif
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Applied {{ $application->created_at->format('d M Y, h:i A') }}</p>
                        {{-- Owner (21 Nov 2026): quick-glance activity so admin
                             sees performance right on the list, not just on
                             the counselor's own page. --}}
                        @if ($application->user_id && ($stats = $activityStats[$application->user_id] ?? null))
                        <p class="text-xs text-gray-400 mt-1">{{ $stats['leads'] }} leads · {{ $stats['touched'] }} touched · {{ $stats['status_changes'] }} status changes</p>
                        @endif
                    </div>

                    <span class="flex flex-col items-end gap-1">
                        <span class="text-xs px-2 py-1 rounded-full font-semibold
                            {{ match(true) {
                                $application->status === 'certified' => 'bg-green-100 text-green-800',
                                $application->isTerminal() => 'bg-red-100 text-red-800',
                                default => 'bg-amber-100 text-amber-800',
                            } }}">
                            {{ \App\Models\MatchmakerApplication::STEPS[$application->status] ?? ucfirst($application->status) }}
                        </span>
                        {{-- 'status' above stays 'certified' forever (a historical
                             fact) even after Deactivate Counselor — this is the only
                             thing on this list that actually says whether the
                             account can still log in. --}}
                        @if ($application->status === 'certified' && $application->user && !$application->user->is_active)
                        <span class="text-xs px-2 py-1 rounded-full font-semibold bg-red-100 text-red-800">Deactivated</span>
                        @endif
                    </span>
                </div>
                @empty
                <p class="p-5 text-gray-500">No applications yet.</p>
                @endforelse
            </div>

            {{ $applications->links() }}
        </div>
    </div>
</x-admin-layout>
