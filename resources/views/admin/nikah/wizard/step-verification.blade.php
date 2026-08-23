<x-dynamic-component :component="$routePrefix === 'matchmaker' ? 'matchmaker-layout' : 'admin-layout'">
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route($routePrefix === 'matchmaker' ? 'matchmaker.nikah.index' : 'admin.nikah.verifications') }}" class="text-gray-400 hover:text-gray-600">Nikah Profiles</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Create Profile</span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <x-wizard-progress :steps="$steps" :titles="$stepTitles" :current="$step" />

                <div id="verification-errors" class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg {{ $errors->any() ? '' : 'hidden' }}">
                    <p class="font-medium text-sm mb-1">Please fix the following:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>

                <form id="verification-form" method="POST" action="{{ route($routePrefix . '.nikah.profiles.create.step.save', 'verification') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <x-nikah-section title="Verification" icon="🪪" color="rose" description="Required — held to the same bar as a self-created profile.">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="cnic_number" value="CNIC Number" />
                                <x-text-input id="cnic_number" name="cnic_number" type="text" class="w-full mt-1" :value="old('cnic_number', $data['cnic_number'] ?? '')" required placeholder="e.g. 12345-1234567-1" />
                            </div>
                            <div></div>
                            <div>
                                <x-input-label for="cnic_front_image" value="CNIC Photo (Front)" />
                                <x-photo-upload-field name="cnic_front_image" :required="empty($data['cnic_front_image'])" />
                                @if (!empty($data['cnic_front_image']))
                                <p class="text-xs text-green-600 mt-1">✓ Already uploaded — choose a file only if you want to replace it.</p>
                                @endif
                            </div>
                            <div>
                                <x-input-label for="cnic_back_image" value="CNIC Photo (Back)" />
                                <x-photo-upload-field name="cnic_back_image" :required="empty($data['cnic_back_image'])" />
                                @if (!empty($data['cnic_back_image']))
                                <p class="text-xs text-green-600 mt-1">✓ Already uploaded — choose a file only if you want to replace it.</p>
                                @endif
                            </div>
                            <div>
                                <x-input-label for="photo" value="Profile Photo (optional)" />
                                <input id="photo" name="photo" type="file" accept="image/*" class="w-full mt-1">
                                @if (!empty($data['photo']))
                                <p class="text-xs text-green-600 mt-1">✓ Already uploaded — choose a file only if you want to replace it.</p>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <input type="checkbox" id="allow_photo_sharing" name="allow_photo_sharing" value="1" {{ old('allow_photo_sharing', $data['allow_photo_sharing'] ?? false) ? 'checked' : '' }} class="rounded">
                            <x-input-label for="allow_photo_sharing" value="Allow photo to be shared with a match after mutual interest is accepted" />
                        </div>
                    </x-nikah-section>

                    <x-nikah-section title="Profile Visibility" icon="👁️" color="teal">
                        @php $vis = old('visibility', $data['visibility'] ?? 'public'); @endphp
                        <select id="visibility" name="visibility" required class="border-gray-300 rounded-md shadow-sm w-full">
                            <option value="public" {{ $vis === 'public' ? 'selected' : '' }}>Public (shows in browse + Google)</option>
                            <option value="members_only" {{ $vis === 'members_only' ? 'selected' : '' }}>Members Only (shows in browse, not Google)</option>
                            <option value="matchmaker_assisted" {{ $vis === 'matchmaker_assisted' ? 'selected' : '' }}>Matchmaker-Assisted Only (hidden from member browse, matchmakers can still find it)</option>
                            <option value="confidential" {{ $vis === 'confidential' ? 'selected' : '' }}>Confidential (hidden from all search/browse — ID-only access)</option>
                        </select>
                    </x-nikah-section>

                    <div class="flex justify-between pt-2">
                        <a href="{{ route($routePrefix . '.nikah.profiles.create.step', 'about') }}" class="btn-base text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">← Back</a>
                        <x-primary-button id="verification-submit">Next: Payment →</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- Same fetch()-based submit as the member wizard's verification step
         — see that view's comment for why: turns a browser-level upload
         failure (Chrome's ERR_UPLOAD_FILE_CHANGED) into this page's own
         friendly message instead of Chrome's network-error screen.
         Falls back to a normal form submission if this script fails to
         attach. --}}
    <script>
        (function () {
            const form = document.getElementById('verification-form');
            const errorsBox = document.getElementById('verification-errors');
            const submitBtn = document.getElementById('verification-submit');
            if (!form || !errorsBox || !submitBtn) return;

            const uploadChangedMessage = 'We couldn\'t upload your file. This can happen if a selected photo was moved, renamed, or changed since you picked it — for example, by OneDrive or another sync tool. Please choose the CNIC/photo files again and submit.';
            const uploadingLabel = 'Uploading…';
            const originalLabel = submitBtn.innerHTML;

            function showErrors(html) {
                errorsBox.innerHTML = '<p class="font-medium text-sm mb-1">Please fix the following:</p>' + html;
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
                        return;
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
                    showErrors('<p>' + uploadChangedMessage + '</p>');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalLabel;
                }
            });
        })();
    </script>
</x-dynamic-component>
