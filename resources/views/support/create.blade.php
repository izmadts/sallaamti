<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Submit a Support Query</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Info banner --}}
            <div class="rounded-xl p-5 mb-6 flex gap-4" style="background: var(--teal-light); border: 1px solid #0d6b6b33">
                <div class="text-3xl">🤝</div>
                <div>
                    <h4 class="font-bold text-gray-800 mb-1">We're Here to Help</h4>
                    <p class="text-gray-600 text-sm">Submit your query and our qualified counselors will respond within 24–48 hours, in sha Allah. All queries are kept confidential.</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-8">
                <form method="POST" action="{{ route('support.store') }}" class="space-y-5">
                    @csrf

                    {{-- Category --}}
                    <fieldset>
                        <legend class="auth-label">Category <span class="text-red-400">*</span></legend>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-2">
                            @foreach ([
                            ['marital', '💑', 'Marital Issues'],
                            ['parenting', '👨‍👩‍👧', 'Parenting'],
                            ['financial', '💰', 'Financial'],
                            ['legal', '⚖️', 'Legal Guidance'],
                            ['spiritual', '🕌', 'Spiritual'],
                            ['other', '💬', 'Other'],
                            ] as $cat)
                            <label class="cursor-pointer" x-data>
                                <input type="radio" name="category" value="{{ $cat[0] }}"
                                    {{ old('category') === $cat[0] ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="flex flex-col items-center justify-center p-3 rounded-xl border-2 border-gray-200 text-center transition-all duration-200 min-h-[72px] peer-checked:border-teal-600 peer-checked:bg-teal-50 peer-checked:text-teal-700 hover:border-teal-400 text-gray-500 cursor-pointer">
                                    <span class="text-xl mb-1">{{ $cat[1] }}</span>
                                    <span class="text-xs font-semibold">{{ $cat[2] }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('category')" class="mt-1" />
                    </fieldset>

                    {{-- Subject --}}
                    <div>
                        <label for="support-subject" class="auth-label">Subject <span class="text-red-400">*</span></label>
                        <div class="auth-input-wrap">
                            <i class="fa fa-tag auth-icon"></i>
                            <input id="support-subject" type="text" name="subject" value="{{ old('subject') }}" required
                                class="auth-input @error('subject') auth-input-error @enderror"
                                placeholder="Brief description of your query">
                        </div>
                        <x-input-error :messages="$errors->get('subject')" class="mt-1" />
                    </div>

                    {{-- Priority --}}
                    <fieldset>
                        <legend class="auth-label">Priority</legend>
                        <div class="flex gap-3 mt-2">
                            @foreach (['low' => ['🟢', 'Low'], 'medium' => ['🟡', 'Medium'], 'high' => ['🔴', 'Urgent']] as $val => $label)
                            <label class="cursor-pointer flex-1">
                                <input type="radio" name="priority" value="{{ $val }}"
                                    {{ old('priority', 'medium') === $val ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="text-center py-2 px-3 rounded-xl border-2 border-gray-200 text-sm font-semibold transition-all peer-checked:border-teal-600 peer-checked:bg-teal-50 peer-checked:text-teal-700 hover:border-gray-300 text-gray-500 cursor-pointer">
                                    {{ $label[0] }} {{ $label[1] }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </fieldset>

                    {{-- Description --}}
                    <div>
                        <label for="support-description" class="auth-label">Describe Your Situation <span class="text-red-400">*</span></label>
                        <textarea id="support-description" name="description" rows="6" required
                            class="auth-input w-full resize-none @error('description') auth-input-error @enderror"
                            style="padding-left: 1rem; padding-top: 0.75rem"
                            placeholder="Please describe your situation in detail. The more information you provide, the better we can help you. All information is kept strictly confidential.">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>

                    {{-- Anonymous --}}
                    <div class="flex items-start gap-2 p-4 rounded-xl" style="background: var(--cream)">
                        <input type="checkbox" name="is_anonymous" value="1" id="anon"
                            class="auth-checkbox mt-0.5 flex-shrink-0"
                            {{ old('is_anonymous') ? 'checked' : '' }}>
                        <label for="anon" class="text-sm text-gray-600 leading-relaxed">
                            <strong>Submit anonymously</strong> — your name will not be shown to the counselor, only your query.
                        </label>
                    </div>

                    <button type="submit" class="btn-base btn-teal w-full py-4 text-base font-bold">
                        Submit Query <i class="fa fa-paper-plane ml-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>