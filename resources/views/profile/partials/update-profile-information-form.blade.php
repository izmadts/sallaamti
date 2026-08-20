<section>
 <header>
 <h2 class="text-lg font-medium text-gray-900">
 {{ __('Profile Information') }}
 </h2>

 <p class="mt-1 text-sm text-gray-600">
 {{ __("Update your account's profile information and email address.") }}
 </p>
 </header>

 <form id="send-verification" method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
 @csrf

 </form>

 <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
 @csrf
 @method('patch')
 <div>
 <x-input-label for=" avatar" value="Profile Photo" />
 <div class="flex items-center gap-4 mt-2">
 <img src="{{ $user->avatarUrl() }}" class="w-16 h-16 rounded-full object-cover">
 <input type="file" id="avatar" name="avatar" accept="image/*" class="text-sm">
 </div>
 <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
 </div>
 <div>
 <x-input-label for="name" :value="__('Name')" />
 <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
 <x-input-error class="mt-2" :messages="$errors->get('name')" />
 </div>

 <div>
 <x-input-label for="email" :value="__('Email')" />
 <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
 <x-input-error class="mt-2" :messages="$errors->get('email')" />

 @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
 <div>
 <p class="text-sm mt-2 text-gray-800">
 {{ __('Your email address is unverified.') }}

 <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
 {{ __('Click here to re-send the verification email.') }}
 </button>
 </p>

 @if (session('status') === 'verification-link-sent')
 <p class="mt-2 font-medium text-sm text-green-600">
 {{ __('A new verification link has been sent to your email address.') }}
 </p>
 @endif
 </div>
 @endif
 </div>

 <div>
 <x-input-label for="phone" :value="__('db.WhatsApp / Phone Number')" />
 <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" />
 <x-input-error class="mt-2" :messages="$errors->get('phone')" />
 <label class="flex items-center gap-2 mt-2 text-sm text-gray-600">
 <input type="checkbox" name="whatsapp_notify_opt_in" value="1" {{ old('whatsapp_notify_opt_in', $user->whatsapp_notify_opt_in) ? 'checked' : '' }} class="rounded border-gray-300 text-teal-600">
 {{ __('db.Notify me on WhatsApp about new community posts') }}
 </label>
 </div>

 <div class="grid grid-cols-2 gap-4">
 <div>
 <x-input-label for="gender" :value="__('db.Gender')" />
 <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
 <option value="">{{ __('db.Select') }}</option>
 <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>{{ __('db.Male') }}</option>
 <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>{{ __('db.Female') }}</option>
 </select>
 <x-input-error class="mt-2" :messages="$errors->get('gender')" />
 </div>
 <div>
 <x-input-label for="city" :value="__('db.City')" />
 <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $user->city ?: optional($user->nikahProfile)->city)" />
 <x-input-error class="mt-2" :messages="$errors->get('city')" />
 </div>
 </div>

 <div class="border-t pt-6 mt-2">
 <h3 class="text-sm font-semibold text-gray-700 mb-1">Public Author Profile</h3>
 <p class="text-xs text-gray-500 mb-4">Shown on any Community Post you publish, and on your public author page — only relevant if you write one.</p>
 <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
 <div>
 <x-input-label for="username" value="Public Username (optional)" />
 <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)"
 placeholder="e.g. yourname" title="Auto-generated from your name the first time you publish a post, if left blank. Letters, numbers, - and _ only." />
 <x-input-error class="mt-2" :messages="$errors->get('username')" />
 </div>
 <div>
 <x-input-label for="public_bio" value="Short Public Bio (optional)" />
 <x-text-input id="public_bio" name="public_bio" type="text" class="mt-1 block w-full" :value="old('public_bio', $user->public_bio)" maxlength="300" />
 <x-input-error class="mt-2" :messages="$errors->get('public_bio')" />
 </div>
 </div>
 </div>

 <div class="flex items-center gap-4">
 <x-primary-button>{{ __('Save') }}</x-primary-button>

 @if (session('status') === 'profile-updated')
 <p
 x-data="{ show: true }"
 x-show="show"
 x-transition
 x-init="setTimeout(() => show = false, 2000)"
 class="text-sm text-gray-600">{{ __('Saved.') }}</p>
 @endif
 </div>
 </form>
</section>