<x-guest-layout :title="__('db.Volunteer With Us')" :description="__('db.Serve the Muslim Ummah as a Sallaamti volunteer — teaching, tech, counseling, outreach and more.')">

    {{-- Page Hero --}}
    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 280px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="absolute inset-0 opacity-5 overflow-hidden" style="font-size: 20rem; color: #fff; display: flex; align-items: center; justify-content: center; pointer-events: none;">🤝</div>
        <div class="max-w-7xl mx-auto px-4 py-16 relative z-10 text-center w-full">
            <span class="section-eyebrow" style="color: rgba(255,255,255,0.7)">{{ __('db.Join Our Team') }}</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-2 mb-3">🤝 {{ __('db.Volunteer with Sallaamti') }}</h1>
            <p class="text-white/70 text-lg max-w-xl mx-auto">{{ __('db."The best of people are those who are most beneficial to people." — Prophet Muhammad ﷺ') }}</p>
        </div>
    </section>

    <section class="py-16 bg-cream">
        <div class="max-w-5xl mx-auto px-4">


            <div class="grid lg:grid-cols-3 gap-8">

                {{-- Form --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm p-8">
                        <h3 class="font-bold text-xl text-gray-800 mb-6">{{ __('db.Volunteer Application Form') }}</h3>

                        @if ($errors->any())
                        <div class="mb-6 p-4 rounded-xl text-red-700 text-sm" style="background: #fef2f2; border: 1px solid #fecaca">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('volunteer.store') }}" class="space-y-5">
                            @csrf
                            {{-- Honeypot: hidden from real visitors, bots that auto-fill every field will trip it --}}
                            <div class="absolute -left-[9999px]" aria-hidden="true">
                                <label for="website">Leave this field empty</label>
                                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                            </div>

                            {{-- Name + Email --}}
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="volunteer-name" class="auth-label">{{ __('db.Full Name') }} <span class="text-red-400">*</span></label>
                                    <div class="auth-input-wrap">
                                        <i class="fa fa-user auth-icon"></i>
                                        <input id="volunteer-name" type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required
                                            class="auth-input @error('name') auth-input-error @enderror"
                                            placeholder="{{ __('db.Your full name') }}">
                                    </div>
                                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                </div>
                                <div>
                                    <label for="volunteer-email" class="auth-label">{{ __('db.Email Address') }} <span class="text-red-400">*</span></label>
                                    <div class="auth-input-wrap">
                                        <i class="fa fa-envelope auth-icon"></i>
                                        <input id="volunteer-email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required
                                            class="auth-input @error('email') auth-input-error @enderror"
                                            placeholder="your@email.com">
                                    </div>
                                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                                </div>
                            </div>

                            {{-- Phone + City --}}
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="volunteer-phone" class="auth-label">{{ __('db.Phone / WhatsApp') }} <span class="text-red-400">*</span></label>
                                    <div class="auth-input-wrap">
                                        <i class="fa fa-phone auth-icon"></i>
                                        <input id="volunteer-phone" type="text" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" required
                                            class="auth-input @error('phone') auth-input-error @enderror"
                                            placeholder="+92 3XX XXXXXXX">
                                    </div>
                                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                                </div>
                                <div>
                                    <label for="volunteer-city" class="auth-label">{{ __('db.City / Country') }}</label>
                                    <div class="auth-input-wrap">
                                        <i class="fa fa-map-marker-alt auth-icon"></i>
                                        <input id="volunteer-city" type="text" name="city" value="{{ old('city', auth()->user()?->city) }}"
                                            class="auth-input" placeholder="{{ __('db.Karachi, London, Dubai...') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Area of Interest --}}
                            <fieldset>
                                <legend class="auth-label">{{ __('db.Area of Interest') }} <span class="text-red-400">*</span></legend>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-2">
                                    @foreach ([
                                    [__('db.Teaching (Quran)'), '👩‍🏫', 'teaching'],
                                    [__('db.Tech / Web Dev'), '💻', 'tech'],
                                    [__('db.Family Counseling'), '🤝', 'counseling'],
                                    [__('db.Fundraising'), '💰', 'fundraising'],
                                    [__('db.Events & Outreach'), '📢', 'events'],
                                    [__('db.Graphic Design'), '🎨', 'design'],
                                    [__('db.Social Media'), '📱', 'social_media'],
                                    [__('db.Administration'), '📋', 'admin'],
                                    [__('db.Other'), '✨', 'other'],
                                    ] as $area)
                                    <label class="interest-option">
                                        <input type="radio" name="area_of_interest" value="{{ $area[2] }}"
                                            {{ old('area_of_interest') === $area[2] ? 'checked' : '' }}
                                            class="sr-only interest-radio">
                                        <span class="interest-label">
                                            <span class="text-lg">{{ $area[1] }}</span>
                                            <span class="text-xs font-semibold mt-1 text-center">{{ $area[0] }}</span>
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('area_of_interest')" class="mt-1" />
                            </fieldset>

                            {{-- Availability --}}
                            <div>
                                <label for="volunteer-availability" class="auth-label">{{ __('db.Weekly Availability') }}</label>
                                <div class="auth-input-wrap">
                                    <i class="fa fa-clock auth-icon"></i>
                                    <select id="volunteer-availability" name="availability" class="auth-input">
                                        <option value="">{{ __('db.Select your availability') }}</option>
                                        <option value="1-2 hours" {{ old('availability') === '1-2 hours' ? 'selected' : '' }}>{{ __('db.1–2 hours per week') }}</option>
                                        <option value="3-5 hours" {{ old('availability') === '3-5 hours' ? 'selected' : '' }}>{{ __('db.3–5 hours per week') }}</option>
                                        <option value="5-10 hours" {{ old('availability') === '5-10 hours' ? 'selected' : '' }}>{{ __('db.5–10 hours per week') }}</option>
                                        <option value="full-time" {{ old('availability') === 'full-time' ? 'selected' : '' }}>{{ __('db.Full-time volunteer') }}</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Skills --}}
                            <div>
                                <label for="volunteer-skills" class="auth-label">{{ __('db.Your Skills / Qualifications') }}</label>
                                <div class="auth-input-wrap">
                                    <i class="fa fa-star auth-icon"></i>
                                    <input id="volunteer-skills" type="text" name="skills" value="{{ old('skills') }}"
                                        class="auth-input"
                                        placeholder="{{ __('db.e.g. Hafiz, Web Developer, Graphic Designer, Counselor...') }}">
                                </div>
                            </div>

                            {{-- Message --}}
                            <div>
                                <label for="volunteer-message" class="auth-label">{{ __('db.Why do you want to volunteer?') }} <span class="text-red-400">*</span></label>
                                <textarea id="volunteer-message" name="message" rows="4" required
                                    class="auth-input w-full resize-none @error('message') auth-input-error @enderror"
                                    style="padding-left: 1rem"
                                    placeholder="{{ __("db.Tell us about yourself, your motivation, and how you'd like to contribute to the Sallaamti mission...") }}">{{ old('message') }}</textarea>
                                <x-input-error :messages="$errors->get('message')" class="mt-1" />
                            </div>

                            {{-- Declaration --}}
                            <div class="p-4 rounded-xl" style="background: var(--cream); border: 1px solid #e5e5e5">
                                <div class="flex items-start gap-3">
                                    <input type="checkbox" name="declaration" id="declaration" required class="auth-checkbox mt-0.5 flex-shrink-0">
                                    <label for="declaration" class="text-sm text-gray-600 leading-relaxed">
                                        {{ __('db.I confirm that all information provided is accurate. I understand that volunteering with Sallaamti is a commitment to serve the Muslim Ummah with sincerity, Islamic values and professionalism. I agree to maintain confidentiality and respect in all my interactions.') }}
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn-base btn-teal w-full py-4 text-base font-bold">
                                {{ __('db.Submit Volunteer Application') }} <i class="fa fa-arrow-right ml-2"></i>
                            </button>

                            <p class="text-center text-xs text-gray-400">
                                {{ __('db.Our team will review your application and contact you within 3–5 business days.') }}
                            </p>
                        </form>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-4">

                    {{-- Why Volunteer --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h5 class="font-bold text-gray-800 mb-4">{{ __('db.Why Volunteer?') }}</h5>
                        <div class="space-y-3">
                            @foreach ([
                            ['🌍', __('db.Global Impact'), __('db.Reach Muslims in 20+ countries')],
                            ['📜', __('db.Official Certificate'), __('db.Volunteer recognition certificate')],
                            ['🤝', __('db.Muslim Community'), __('db.Join a dedicated team of believers')],
                            ['🎓', __('db.Skill Development'), __('db.Grow while serving the Ummah')],
                            ['🤲', __('db.Sadaqah Jariyah'), __('db.Ongoing reward even after you stop')],
                            ] as $w)
                            <div class="flex items-start gap-3">
                                <span class="text-xl flex-shrink-0">{{ $w[0] }}</span>
                                <div>
                                    <strong class="text-sm text-gray-800">{{ $w[1] }}</strong>
                                    <p class="text-xs text-gray-500 mb-0">{{ $w[2] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Roles we need --}}
                    <div class="rounded-2xl p-6 shadow-sm" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%)">
                        <h5 class="font-bold text-white mb-4">{{ __('db.Roles We Need') }}</h5>
                        <div class="space-y-2">
                            @foreach ([
                            ['👩‍🏫', __('db.Quran Teachers (Male & Female)')],
                            ['💻', __('db.Web & App Developers')],
                            ['🎨', __('db.Graphic Designers')],
                            ['📢', __('db.Community Outreach')],
                            ['📋', __('db.Admin & Coordination')],
                            ['🤝', __('db.Islamic Counselors')],
                            ] as $role)
                            <div class="flex items-center gap-2 text-white/80 text-sm">
                                <span>{{ $role[0] }}</span> {{ $role[1] }}
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h5 class="font-bold text-gray-800 mb-3">{{ __('db.Questions?') }}</h5>
                        <p class="text-gray-500 text-sm mb-4">{{ __('db.Reach out to us before applying if you have questions.') }}</p>
                        <a href="{{ whatsapp_link() }}" target="_blank"
                            class="btn-base btn-teal w-full py-2.5 text-sm text-center block">
                            <i class="fab fa-whatsapp mr-2"></i>{{ __('db.WhatsApp Us') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <style>
        /* Interest selector */
        .interest-option {
            cursor: pointer;
            display: block;
        }

        .interest-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px 8px;
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            transition: all 0.2s;
            text-align: center;
            height: 100%;
            min-height: 72px;
            color: #6b7280;
        }

        .interest-radio:checked+.interest-label {
            border-color: var(--teal);
            background: var(--teal-light);
            color: var(--teal);
        }

        .interest-label:hover {
            border-color: var(--teal);
        }
    </style>

    <x-faq-section module="volunteer" />
</x-guest-layout>