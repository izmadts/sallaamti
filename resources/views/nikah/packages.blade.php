<x-guest-layout title="Nikah Matchmaking Packages — Sallaamti" description="Compare Sallaamti's Nikah matchmaking packages, from self-service verification to fully dedicated personal matchmaking.">

    <div class="py-12 bg-cream">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-gray-800">Nikah Matchmaking Packages</h1>
                <p class="text-gray-500 mt-2 max-w-2xl mx-auto">Whether you want to manage your own search or have a dedicated consultant do the work for you — choose the level of support that fits you.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                @foreach ($packages as $package)
                @php
                $styles = match($package->color) {
                    'green' => ['border' => 'border-green-200', 'badge' => 'bg-green-100 text-green-800', 'button' => 'bg-green-600 hover:bg-green-700'],
                    'amber' => ['border' => 'border-amber-300', 'badge' => 'bg-amber-100 text-amber-800', 'button' => 'bg-amber-500 hover:bg-amber-600'],
                    'blue' => ['border' => 'border-blue-200', 'badge' => 'bg-blue-100 text-blue-800', 'button' => 'bg-blue-600 hover:bg-blue-700'],
                    'red' => ['border' => 'border-red-200', 'badge' => 'bg-red-100 text-red-800', 'button' => 'bg-red-600 hover:bg-red-700'],
                    'purple' => ['border' => 'border-purple-200', 'badge' => 'bg-purple-100 text-purple-800', 'button' => 'bg-purple-600 hover:bg-purple-700'],
                    default => ['border' => 'border-teal-200', 'badge' => 'bg-teal-100 text-teal-800', 'button' => 'bg-teal-600 hover:bg-teal-700'],
                };
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border-2 {{ $styles['border'] }} p-6 flex flex-col h-full">
                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full w-fit {{ $styles['badge'] }}">
                        {{ $package->icon }} {{ $package->name }}
                    </span>
                    @if ($package->tagline)
                    <p class="text-sm text-gray-500 mt-3 italic">{{ $package->tagline }}</p>
                    @endif

                    <div class="mt-4">
                        <span class="text-3xl font-bold text-gray-800">Rs. {{ number_format($package->price) }}</span>
                        <span class="text-sm text-gray-400">{{ $package->isOneTime() ? ' one-time' : ' / ' . $package->duration_days . ' days' }}</span>
                    </div>

                    @if ($package->description)
                    <p class="text-sm text-gray-600 mt-4 leading-relaxed">{{ $package->description }}</p>
                    @endif

                    @if (!empty($package->features))
                    <ul class="mt-5 space-y-2 flex-1">
                        @foreach ($package->features as $feature)
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="text-green-500 mt-0.5">✓</span>
                            <span>{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    <a href="{{ route(auth()->check() ? 'dashboard' : 'register') }}" class="mt-6 block text-center text-white text-sm font-semibold px-4 py-3 rounded-lg transition hover:-translate-y-0.5 shadow-sm {{ $styles['button'] }}">
                        Get Started →
                    </a>
                </div>
                @endforeach
            </div>

            <p class="text-center text-xs text-gray-400 mt-10 max-w-2xl mx-auto">
                Proposal counts are a cap on how many candidates our consultants review and share with you, based on database availability and compatibility with your requirements — not a guarantee of matches. Prices and package details may be updated from time to time.
            </p>
        </div>
    </div>
</x-guest-layout>
