<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('db.Create Your Nikah Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <x-wizard-progress :steps="$steps" :titles="$stepTitles" :current="$step" />

                <div id="verification-errors" class="mb-4 p-4 bg-red-50 text-red-700 rounded {{ $errors->any() ? '' : 'hidden' }}">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>

                <form id="verification-form" method="POST" action="{{ route('nikah.create.step.save', 'verification') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <x-nikah-section :title="__('db.Verification (Required)')" icon="🪪" color="rose" :description="__('db.Your CNIC will only be used for verification and is never shown publicly.')">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="cnic_number" :value="__('db.CNIC Number')" />
                                <x-text-input id="cnic_number" name="cnic_number" type="text" class="w-full mt-1" :value="old('cnic_number', $data['cnic_number'] ?? '')" required
                                    placeholder="{{ __('db.e.g. 12345-1234567-1') }}" title="{{ __('db.Your 13-digit CNIC number, exactly as printed on your card.') }}" />
                                <p class="text-xs text-gray-400 mt-1">{{ __('db.Must be unique — one CNIC can only be used for one profile.') }}</p>
                            </div>
                            <div></div>
                            <div>
                                <x-input-label for="cnic_front_image" :value="__('db.CNIC Photo (Front)')" />
                                <x-photo-upload-field name="cnic_front_image" :required="empty($data['cnic_front_image'])" />
                                @if (!empty($data['cnic_front_image']))
                                <p class="text-xs text-green-600 mt-1">✓ {{ __('db.Already uploaded — choose a file only if you want to replace it.') }}</p>
                                @endif
                            </div>
                            <div>
                                <x-input-label for="cnic_back_image" :value="__('db.CNIC Photo (Back)')" />
                                <x-photo-upload-field name="cnic_back_image" :required="empty($data['cnic_back_image'])" />
                                @if (!empty($data['cnic_back_image']))
                                <p class="text-xs text-green-600 mt-1">✓ {{ __('db.Already uploaded — choose a file only if you want to replace it.') }}</p>
                                @endif
                            </div>
                        </div>
                        <hr class="mt-4 border-rose-200">
                        <div class="mt-4">
                            <x-input-label for="photo" :value="__('db.Your Nikah Profile Photo (private, shared only if you allow, hidden until mutual interest is accepted)')" />
                            <input id="photo" name="photo" type="file" accept="image/*" class="w-full mt-1" />
                            @if (!empty($data['photo']))
                            <p class="text-xs text-green-600 mt-1">✓ {{ __('db.Already uploaded — choose a file only if you want to replace it.') }}</p>
                            @endif
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <input type="checkbox" id="allow_photo_sharing" name="allow_photo_sharing" value="1" {{ old('allow_photo_sharing', $data['allow_photo_sharing'] ?? true) ? 'checked' : '' }} class="rounded"
                                title="{{ __('db.Your photo stays private until both sides accept interest in each other — this just decides what happens after that.') }}">
                            <x-input-label for="allow_photo_sharing" :value="__('db.Allow my photo to be shared with a match after mutual interest is accepted')" />
                        </div>
                    </x-nikah-section>

                    <x-nikah-section :title="__('db.Profile Visibility')" icon="👁️" color="teal">
                        @php $vis = old('visibility', $data['visibility'] ?? 'public'); @endphp
                        <select id="visibility" name="visibility" required class="border-gray-300 rounded-md shadow-sm w-full"
                            title="{{ __('db.You can change this anytime after your profile goes live.') }}">
                            <option value="public" {{ $vis === 'public' ? 'selected' : '' }}>{{ __('db.Anyone can find me (shows in search, and in Google results)') }}</option>
                            <option value="members_only" {{ $vis === 'members_only' ? 'selected' : '' }}>{{ __('db.Sallaamti members only (shows in search, but not on Google)') }}</option>
                            <option value="matchmaker_assisted" {{ $vis === 'matchmaker_assisted' ? 'selected' : '' }}>{{ __('db.Only my matchmaker can find me (hidden from everyone browsing, a matchmaker can still suggest me)') }}</option>
                            <option value="confidential" {{ $vis === 'confidential' ? 'selected' : '' }}>{{ __('db.Nobody can find me by searching (fully hidden — only someone who already knows my exact profile, like a matchmaker you\'ve spoken to, can work with it)') }}</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">{{ __('db.Whatever you choose, a link you personally share always shows your profile to whoever opens it.') }}</p>
                    </x-nikah-section>

                    <div class="flex justify-between">
                        <a href="{{ route('nikah.create.step', 'about') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← {{ __('db.Back') }}</a>
                        <x-primary-button id="verification-submit">{{ __('db.Review My Profile') }} →</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- Submits via fetch() instead of a plain page-navigation POST
         specifically so a browser-level upload failure — most notably
         Chrome's ERR_UPLOAD_FILE_CHANGED, thrown when a selected file was
         touched by something like OneDrive sync between selection and
         submit — surfaces as this page's own friendly message instead of
         replacing the whole page with Chrome's network-error screen.
         Falls back to a normal form submission if JS fails to attach
         (progressive enhancement, not a hard dependency on this script). --}}
    @php
        // Assigned to plain PHP vars before @json() — Blade's directive-argument
        // parser was found to truncate mid-string on this particular long,
        // punctuation-heavy translated string when passed directly to
        // @json(__(...)), silently corrupting the compiled JS (and crashing
        // the whole page with a PHP ParseError). Passing a simple variable
        // to @json() sidesteps that parser edge case.
        $uploadChangedMessageText = __("db.We couldn't upload your file. This can happen if a selected photo was moved, renamed, or changed since you picked it — for example, by OneDrive or another sync tool. Please choose your CNIC/photo files again and submit.");
        $uploadingLabelText = __('db.Uploading…');
    @endphp
    <script>
        (function () {
            const form = document.getElementById('verification-form');
            const errorsBox = document.getElementById('verification-errors');
            const submitBtn = document.getElementById('verification-submit');
            if (!form || !errorsBox || !submitBtn) return;

            const uploadChangedMessage = @json($uploadChangedMessageText);
            const uploadingLabel = @json($uploadingLabelText);
            const originalLabel = submitBtn.innerHTML;

            function showErrors(html) {
                errorsBox.innerHTML = html;
                errorsBox.classList.remove('hidden');
                errorsBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                errorsBox.classList.add('hidden');
                errorsBox.innerHTML = '';
                submitBtn.disabled = true;
                submitBtn.innerHTML = uploadingLabel;

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });

                    if (response.ok) {
                        const data = await response.json();
                        window.location.href = data.redirect;
                        return; // keep the button disabled while the browser navigates away
                    }

                    if (response.status === 422) {
                        const data = await response.json();
                        const messages = Object.values(data.errors || {}).flat();
                        showErrors('<ul class="list-disc list-inside text-sm">' +
                            messages.map((m) => '<li>' + m + '</li>').join('') + '</ul>');
                    } else {
                        throw new Error('Unexpected response status ' + response.status);
                    }
                } catch (err) {
                    // fetch() rejects (rather than resolving with a bad status)
                    // for the underlying network/file-read failure this whole
                    // script exists to handle gracefully.
                    showErrors('<p>' + uploadChangedMessage + '</p>');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalLabel;
                }
            });
        })();
    </script>
</x-app-layout>
