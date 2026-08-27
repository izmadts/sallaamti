<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('db.Your Interests') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('db.Choose which parts of Sallaamti you want to see in your menu and dashboard. You can change this anytime — nothing you\'ve already set up is affected.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.modules.update') }}" class="mt-6 space-y-4">
        @csrf
        @method('patch')

        <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50"
            title="{{ __('db.Turn this on and Nikah counseling shows in your menu. Turn it off and it stays hidden until you switch it back on — nothing is deleted either way.') }}">
            <input type="checkbox" name="nikah_module_enabled" value="1" {{ $user->nikah_module_enabled ? 'checked' : '' }}
                class="mt-1 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
            <span>
                <span class="block font-medium text-gray-800">💍 {{ __('db.Nikah') }}</span>
                <span class="block text-xs text-gray-500">{{ __('db.Nikah Counseling profiles, browsing, and interests.') }}</span>
            </span>
        </label>

        <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50"
            title="{{ __('db.Turn this on and Quran courses & live classes show in your menu. Turn it off and it stays hidden until you switch it back on — nothing is deleted either way.') }}">
            <input type="checkbox" name="quran_module_enabled" value="1" {{ $user->quran_module_enabled ? 'checked' : '' }}
                class="mt-1 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
            <span>
                <span class="block font-medium text-gray-800">📖 {{ __('db.Quran Learning') }}</span>
                <span class="block text-xs text-gray-500">{{ __('db.Courses, live classes, and certificates.') }}</span>
            </span>
        </label>

        <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50"
            title="{{ __('db.Turn this on and Family Counseling booking shows in your menu. Turn it off and it stays hidden until you switch it back on — nothing is deleted either way.') }}">
            <input type="checkbox" name="counseling_module_enabled" value="1" {{ $user->counseling_module_enabled ? 'checked' : '' }}
                class="mt-1 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
            <span>
                <span class="block font-medium text-gray-800">🤝 {{ __('db.Family Support') }}</span>
                <span class="block text-xs text-gray-500">{{ __('db.Counseling session booking and your support queries.') }}</span>
            </span>
        </label>

        <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50"
            title="{{ __('db.Turn this on and Digital Skills courses show in your menu. Turn it off and it stays hidden until you switch it back on — nothing is deleted either way.') }}">
            <input type="checkbox" name="skills_module_enabled" value="1" {{ $user->skills_module_enabled ? 'checked' : '' }}
                class="mt-1 rounded border-gray-300 text-teal-600 focus:ring-teal-500">
            <span>
                <span class="block font-medium text-gray-800">💻 {{ __('db.Digital Skills') }}</span>
                <span class="block text-xs text-gray-500">{{ __('db.Web development, design, marketing, and more — presented by IZMA.') }}</span>
            </span>
        </label>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('db.Save') }}</x-primary-button>

            @if (session('status') === 'modules-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600">
                {{ __('db.Saved.') }}
            </p>
            @endif
        </div>
    </form>
</section>
