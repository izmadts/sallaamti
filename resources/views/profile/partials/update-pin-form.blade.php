<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Login PIN') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Set a 4-digit PIN for faster sign-in on devices you\'ve already logged into with your password — a new or unrecognized device will still need your password.') }}
        </p>
    </header>

    <form method="post" action="{{ route('pin.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        @if (Auth::user()->password)
        <div>
            <x-input-label for="update_pin_current_password" :value="__('Current Password')" />
            <x-text-input id="update_pin_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePin->get('current_password')" class="mt-2" />
        </div>
        @endif

        <div>
            <x-input-label for="update_pin_pin" :value="__('New PIN (4 digits)')" />
            <x-text-input id="update_pin_pin" name="pin" type="text" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" class="mt-1 block w-40 tracking-widest text-center" autocomplete="off" />
            <x-input-error :messages="$errors->updatePin->get('pin')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_pin_pin_confirmation" :value="__('Confirm PIN')" />
            <x-text-input id="update_pin_pin_confirmation" name="pin_confirmation" type="text" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" class="mt-1 block w-40 tracking-widest text-center" autocomplete="off" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ Auth::user()->pin ? __('Update PIN') : __('Set PIN') }}</x-primary-button>

            @if (session('status') === 'pin-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-gray-600"
            >{{ __('Saved.') }}</p>
            @endif

            @if (session('status') === 'pin-removed')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-gray-600"
            >{{ __('PIN removed.') }}</p>
            @endif
        </div>
    </form>

    @if (Auth::user()->pin)
    <form method="post" action="{{ route('pin.destroy') }}" class="mt-4" onsubmit="return confirm('{{ __('Remove your login PIN?') }}')">
        @csrf
        @method('delete')
        <button type="submit" class="text-sm text-red-600 hover:text-red-800">{{ __('Remove PIN') }}</button>
    </form>
    @endif
</section>
