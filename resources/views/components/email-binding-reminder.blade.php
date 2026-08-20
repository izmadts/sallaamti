{{--
    Shown on every page for a logged-in account that has a phone number but
    no email — deliberately no dismiss button, so it keeps reminding until
    an email is actually bound (same non-dismissible pattern as
    profile-completion-banner). Registering with only a phone or only an
    email means two people who are really the same person can end up with
    two separate accounts with no way to tell — binding an email closes
    that gap and also unlocks password-reset-by-email if they lose SMS/WhatsApp access.
--}}
@auth
@if (!auth()->user()->email && auth()->user()->phone)
<div class="bg-amber-50 border-b border-amber-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-wrap items-center gap-3">
        <div class="flex-1 min-w-[220px]">
            <p class="text-sm text-amber-800 font-medium">
                {{ __('db.Add an email address to secure your account') }}
            </p>
            <p class="text-xs text-amber-700 mt-0.5">
                {{ __("db.Your account currently only has a phone number. Adding an email helps us verify it's really you and lets you recover your account if you lose access to your phone.") }}
            </p>
        </div>
        <form method="POST" action="{{ route('profile.update') }}" class="flex gap-2 items-start">
            @csrf
            @method('PATCH')
            <input type="hidden" name="name" value="{{ auth()->user()->name }}">
            <div>
                <input type="email" name="email" required placeholder="{{ __('db.you@example.com') }}"
                    class="text-sm border-amber-300 rounded-md focus:border-amber-500 focus:ring-amber-500 py-1.5">
                @error('email')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 px-3 py-2 rounded-md transition whitespace-nowrap">
                {{ __('db.Add Email') }}
            </button>
        </form>
    </div>
</div>
@endif
@endauth
