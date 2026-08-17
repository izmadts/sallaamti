<x-auth-layout>
    <div class="auth-wrapper">
        <div class="auth-card">

            {{-- Logo --}}
            <div class="text-center mb-8">
                <a href="{{ url('/') }}">
                    <x-application-logo class="h-14 w-auto mx-auto" atl="sallaamti logo"/>
                </a>
                <h2 class="text-2xl font-extrabold text-gray-800 mt-4">{{ __('db.Welcome Back') }}</h2>
                <p class="text-gray-500 text-sm mt-1">{{ __('db.Sign in to your Sallaamti account') }}</p>
            </div>

            @include('auth.partials.login-form')

        </div>
    </div>
</x-auth-layout>