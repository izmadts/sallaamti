<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800">
                    {{ __('Welcome') }}, {{ Auth::user()->name }} 👋
                </h3>
                <p class="text-gray-600 mt-1">
                    {{ __('What would you like to do today?') }}
                </p>
               
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Quran Learning -->
                <a href="{{ route('quran-live.index') }}" class="block bg-white rounded-lg shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                    <div class="text-3xl mb-3">📖</div>
                    <h4 class="font-semibold text-gray-800 text-lg">{{ __('Quran Learning') }}</h4>
                    <p class="text-gray-500 text-sm mt-1">{{ __('Naazira, Tarjuma, Arabic Grammar, Seerah & Hadith courses.') }}</p>
                </a>

                <!-- Nikah Matchmaking -->
                <a href="{{ Auth::user()->nikahProfile ? route('nikah.show') : route('nikah.create') }}" class="block bg-white rounded-lg shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                    <div class="text-3xl mb-3">💍</div>
                    <h4 class="font-semibold text-gray-800 text-lg">{{ __('Find a Match (Nikah)') }}</h4>
                    <p class="text-gray-500 text-sm mt-1">{{ __('Create your verified profile and search for a suitable match.') }}</p>
                </a>

                <!-- Family Counseling -->
                <a href="#" class="block bg-white rounded-lg shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                    <div class="text-3xl mb-3">🤝</div>
                    <h4 class="font-semibold text-gray-800 text-lg">{{ __('Family Support') }}</h4>
                    <p class="text-gray-500 text-sm mt-1">{{ __('Submit a query and get guidance from our counselors.') }}</p>
                </a>

                <!-- Parenting Coaching -->
                <a href="#" class="block bg-white rounded-lg shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                    <div class="text-3xl mb-3">👨‍👩‍👧</div>
                    <h4 class="font-semibold text-gray-800 text-lg">{{ __('Parenting Coaching') }}</h4>
                    <p class="text-gray-500 text-sm mt-1">{{ __('Guidance for raising children with Islamic values.') }}</p>
                </a>

                <!-- Skills -->
                <a href="#" class="block bg-white rounded-lg shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                    <div class="text-3xl mb-3">💻</div>
                    <h4 class="font-semibold text-gray-800 text-lg">{{ __('Skills Training') }}</h4>
                    <p class="text-gray-500 text-sm mt-1">{{ __('Computer skills, design, trades, and vocational courses.') }}</p>
                </a>

                <!-- Employment -->
                <a href="#" class="block bg-white rounded-lg shadow-sm hover:shadow-md transition p-6 border border-gray-100">
                    <div class="text-3xl mb-3">🛠️</div>
                    <h4 class="font-semibold text-gray-800 text-lg">{{ __('Employment Support') }}</h4>
                    <p class="text-gray-500 text-sm mt-1">{{ __('Find help starting a small business or earning a livelihood.') }}</p>
                </a>

            </div>

        </div>
    </div>
</x-app-layout>