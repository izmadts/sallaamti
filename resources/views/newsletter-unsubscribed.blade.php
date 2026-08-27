<x-guest-layout :title="__('db.Unsubscribed')">
    <div class="max-w-md mx-auto py-20 px-6 text-center">
        <div class="text-5xl mb-4">✅</div>
        <h1 class="text-xl font-semibold text-gray-800 mb-2">{{ __('db.You\'ve been unsubscribed') }}</h1>
        <p class="text-gray-500 text-sm">
            {{ __('db.:name, you won\'t receive newsletter emails from Sallaamti anymore. You\'ll still get essential account emails (like password resets or donation receipts).', ['name' => $user->name]) }}
        </p>
        <a href="{{ route('index') }}" class="inline-block mt-6 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">{{ __('db.Back to Sallaamti') }}</a>
    </div>
</x-guest-layout>
