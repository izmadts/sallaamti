<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('db.My Interests') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">


            <div>

                <h3 class="font-semibold text-gray-700 mb-3">{{ __('db.Received Interests') }}</h3>
                <div class="space-y-3">
                    @forelse ($received as $interest)
                    <div class="bg-white rounded-lg shadow-sm p-4 flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            @if ($interest->status === 'accepted' && $interest->sender->photo)
                            <img src="{{ route('nikah.file', [$interest->sender, 'photo']) }}" alt="{{ __('db.:age yrs, :city', ['age' => $interest->sender->age, 'city' => $interest->sender->city]) }}" class="w-16 h-20 object-cover rounded-full">
                            @endif
                            <div>
                                <p class="font-medium">{{ $interest->sender->age }} yrs, {{ $interest->sender->city }}</p>
                                <p class="text-sm text-gray-500">{{ $interest->sender->profession }}</p>
                                @if ($interest->status === 'accepted')
                                <p class="text-sm text-green-600 mt-1">
                                    {{ __('db.Guardian Contact: :contact (:name)', ['contact' => $interest->sender->guardian_contact, 'name' => $interest->sender->guardian_name]) }}
                                </p>
                                @endif
                                <a href="{{ route('nikah.profile.view', $interest->sender) }}" class="text-xs font-semibold mt-1 inline-block" style="color: var(--teal)">{{ __('db.View Full Profile') }} →</a>
                            </div>
                            <div>
                                @if ($interest->status === 'pending')
                                <form method="POST" action="{{ route('nikah.interest.accept', $interest) }}" class="inline">
                                    @csrf
                                    <button class="bg-green-600 text-white text-sm px-3 py-1.5 rounded">{{ __('db.Accept') }}</button>
                                </form>
                                <form method="POST" action="{{ route('nikah.interest.decline', $interest) }}" class="inline">
                                    @csrf
                                    <button class="bg-gray-300 text-gray-700 text-sm px-3 py-1.5 rounded">{{ __('db.Decline') }}</button>
                                </form>
                                @else
                                <span class="text-xs px-2 py-1 rounded-full {{ $interest->status === 'accepted' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($interest->status) }}
                                </span>
                                @endif
                            </div>
                            <div>
                                @if ($interest->status === 'accepted')
                                <a href="{{ route('nikah.messages.show', $interest) }}" class="text-sm text-blue-600 hover:underline">💬 {{ __('db.Messages') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500">{{ __('db.No interests received yet.') }}</p>
                    @endforelse
                </div>

                <div class="mt-8">
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3">{{ __('db.Sent Interests') }}</h3>
                        <div class="space-y-3">
                                    @forelse ($sent as $interest)
                                    <div class="bg-white rounded-lg shadow-sm p-4 flex justify-between items-center">
                                        <div class="flex items-center gap-4">
                                            @if ($interest->status === 'accepted' && $interest->receiver->photo)
                                            <img src="{{ route('nikah.file', [$interest->receiver, 'photo']) }}" alt="{{ __('db.:age yrs, :city', ['age' => $interest->receiver->age, 'city' => $interest->receiver->city]) }}" class="w-20 h-20 object-cover rounded-full">
                                            @endif
                                            <div>
                                                <p class="font-medium">{{ $interest->receiver->age }} yrs, {{ $interest->receiver->city }}</p>
                                                @if ($interest->status === 'accepted')
                                                <p class="text-sm text-green-600 mt-1">
                                                    {{ __('db.Guardian Contact: :contact (:name)', ['contact' => $interest->receiver->guardian_contact, 'name' => $interest->receiver->guardian_name]) }}
                                                </p>
                                                @endif
                                                <a href="{{ route('nikah.profile.view', $interest->receiver) }}" class="text-xs font-semibold mt-1 inline-block" style="color: var(--teal)">{{ __('db.View Full Profile') }} →</a>
                                            </div>
                                        </div>
                                        <span class="text-xs px-2 py-1 rounded-full {{ $interest->status === 'accepted' ? 'bg-green-100 text-green-800' : ($interest->status === 'declined' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($interest->status) }}
                                        </span>
                                    </div>
                                    @empty
                                    <p class="text-gray-500">{{ __('db.No interests sent yet.') }}</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
    </div>
</x-app-layout>