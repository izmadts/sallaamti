<x-auth-layout>
    <div class="auth-wrapper">
        <div class="auth-card" style="max-width: 560px">

            <div class="text-center mb-8">
                <a href="{{ url('/') }}">
                    <x-application-logo class="h-14 w-auto mx-auto" />
                </a>
                <h2 class="text-2xl font-extrabold text-gray-800 mt-4">{{ __('db.Join Sallaamti') }}</h2>
                <p class="text-gray-500 text-sm mt-1">{{ __('db.Create your free account in under 2 minutes') }}</p>
            </div>

            {{-- Trust bar --}}
            <div class="flex justify-center gap-4 flex-wrap mb-6">
                @foreach (['Free to Join', 'Quran Courses', 'Nikah Platform', 'Live Classes'] as $t)
                <span class="text-xs font-medium flex items-center gap-1" style="color: var(--teal)">
                    <i class="fa fa-check-circle" style="color: var(--gold)"></i> {{ __("db.$t") }}
                </span>
                @endforeach
            </div>

            @include('auth.partials.register-form')

        </div>
    </div>
</x-auth-layout>
