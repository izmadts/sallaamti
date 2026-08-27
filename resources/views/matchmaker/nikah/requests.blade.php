<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('db.My Contact Requests') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">{{ __('db.Profile') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('db.Status') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('db.Requested') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('db.Decided') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('db.Notes') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($requests as $req)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('matchmaker.nikah.show', $req->nikah_profile_id) }}" class="text-teal-700 hover:underline font-medium">
                                    {{ __('db.:age yrs, :gender · :city', ['age' => $req->profile?->age, 'gender' => ucfirst($req->profile?->user?->gender ?? '—'), 'city' => $req->profile?->city]) }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ match($req->status) {
                                        'approved' => 'bg-green-100 text-green-800',
                                        'denied' => 'bg-red-100 text-red-800',
                                        default => 'bg-yellow-100 text-yellow-800',
                                    } }}">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $req->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $req->decided_at?->format('d M Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $req->admin_notes ?: '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                {{ __('db.No requests yet.') }} <a href="{{ route('matchmaker.nikah.index') }}" class="text-teal-600 hover:underline">{{ __('db.Browse profiles') }}</a> {{ __('db.to request contact for a good match.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $requests->links() }}
        </div>
    </div>
</x-app-layout>
