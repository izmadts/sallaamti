<x-guest-layout :title="__('db.Contact Us')" :description="__('db.Get in touch with Sallaamti — questions about courses, Nikah, volunteering or donations.')">
    @section('title', __('db.Contact Sallaamti — Get in Touch'))
    @section('description', __('db.Contact Sallaamti for questions about Quran courses, live classes, Nikah platform or volunteering. We reply within 24 hours, in sha Allah.'))
    @section('canonical', url('/contact'))
    {{-- ============================================================ --}}
    {{-- PAGE HERO --}}
    {{-- ============================================================ --}}
    <section class="page-hero relative overflow-hidden flex items-center"
        style="min-height: 280px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="absolute inset-0 opacity-5 overflow-hidden flex items-center justify-center pointer-events-none"
            style="font-size: 18rem; color: #fff;">✉️</div>
        <div class="max-w-7xl mx-auto px-4 py-16 relative z-10 text-center w-full">
            <span class="section-eyebrow" style="color: rgba(255,255,255,0.7)">{{ __('db.We\'d Love to Hear From You') }}</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-2 mb-3">{{ __('db.Contact Sallaamti') }}</h1>
            <p class="text-white/70 text-lg max-w-xl mx-auto">
                {{ __('db.Questions about our courses, Nikah platform, volunteering or donations? We\'re here to help.') }}
            </p>
            <nav class="flex justify-center gap-2 mt-6 text-sm text-white/50">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">{{ __('db.Home') }}</a>
                <span>/</span>
                <span class="text-white">{{ __('db.Contact') }}</span>
            </nav>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- CONTACT INFO CARDS --}}
    {{-- ============================================================ --}}
    <section class="py-12 bg-cream">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 -mt-10 relative z-10">
                @foreach ([
                ['📍', __('db.Our Address'), setting('site_address', 'Karachi, Pakistan'), url('/contact'), __('db.Get Directions')],
                ['💬', __('db.WhatsApp'), setting('social_whatsapp') ?: setting('site_phone', '+92 314 616 3271'), whatsapp_link(), __('db.WhatsApp Us')],
                ['📞', __('db.Phone'), setting('site_landline') ?: setting('site_phone', '+92 314 616 3271'), 'tel:'.(setting('site_landline') ?: setting('site_phone', '')), __('db.Call Us')],
                ['✉️', __('db.Email Us'), setting('site_email', 'info@sallaamti.com'), 'mailto:'.setting('site_email'), __('db.Send Email')],
                ['🕐', __('db.Office Hours'), __('db.Mon – Sat: 9AM – 8PM (PKT)'), '#', __('db.We reply within 24hrs')],
                ] as $info)
                <div class="bg-white rounded-2xl shadow-md p-6 text-center hover:shadow-lg transition-shadow duration-300 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4"
                        style="background: var(--teal-light)">{{ $info[0] }}</div>
                    <h6 class="font-bold text-gray-800 mb-1">{{ $info[1] }}</h6>
                    <p class="text-gray-500 text-sm mb-3">{{ $info[2] }}</p>
                    <a href="{{ $info[3] }}" target="_blank"
                        class="text-xs font-semibold" style="color: var(--teal)">
                        {{ $info[4] }} →
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- CONTACT FORM + SIDEBAR --}}
    {{-- ============================================================ --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-3 gap-10">

                {{-- ===== FORM ===== --}}
                <div class="lg:col-span-2 wow fadeInLeft" data-wow-delay="0.1s">

                    <div class="mb-8">
                        <span class="section-eyebrow">{{ __('db.Send a Message') }}</span>
                        <h2 class="section-title">{{ __('db.We\'d Love to Hear From You') }}</h2>
                        <p class="text-gray-500 mt-2">{{ __('db.Fill out the form below and our team will get back to you within 24 hours, in sha Allah.') }}</p>
                    </div>

                    @if (session('contact_success'))
                    <div class="mb-6 p-4 rounded-xl flex items-start gap-3 wow fadeIn"
                        style="background: #f0fdf4; border: 1px solid #bbf7d0">
                        <i class="fa fa-check-circle text-green-600 text-xl flex-shrink-0 mt-0.5"></i>
                        <div>
                            <p class="font-semibold text-green-800 mb-0.5">{{ __('db.Message Sent Successfully!') }}</p>
                            <p class="text-green-700 text-sm mb-0">{{ session('contact_success') }}</p>
                        </div>
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl text-red-700 text-sm"
                        style="background: #fef2f2; border: 1px solid #fecaca">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-5">
                        @csrf
                        {{-- Honeypot: hidden from real visitors, bots that auto-fill every field will trip it --}}
                        <div class="absolute -left-[9999px]" aria-hidden="true">
                            <label for="website">Leave this field empty</label>
                            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                        </div>

                        {{-- Subject tabs --}}
                        <div x-data="{ subject: '{{ old('subject_type', 'general') }}' }">
                            <label class="auth-label mb-3">{{ __('db.What is this about?') }}</label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-5">
                                @foreach ([
                                ['general', '💬', __('db.General Query')],
                                ['quran', '📖', __('db.Quran Courses')],
                                ['live_class', '🎥', __('db.Live Classes')],
                                ['skills', '💻', __('db.Digital Skills')],
                                ['nikah', '💍', __('db.Nikah Platform')],
                                ['volunteer', '🤝', __('db.Volunteering')],
                                ['donation', '💝', __('db.Donation')],
                                ] as $sub)
                                <label class="cursor-pointer">
                                    <input type="radio" name="subject_type" value="{{ $sub[0] }}"
                                        x-model="subject"
                                        {{ old('subject_type', 'general') === $sub[0] ? 'checked' : '' }}
                                        class="sr-only">
                                    <div :class="subject === '{{ $sub[0] }}'
                                    ? 'border-teal-600 bg-teal-50 text-teal-700'
                                    : 'border-gray-200 text-gray-500 hover:border-teal-400'"
                                        class="flex flex-col items-center justify-center p-3 rounded-xl border-2 text-center transition-all duration-200 min-h-[72px]">
                                        <span class="text-xl mb-1">{{ $sub[1] }}</span>
                                        <span class="text-xs font-semibold">{{ $sub[2] }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>

                            {{-- Name + Email --}}
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="auth-label">{{ __('db.Full Name') }} <span class="text-red-400">*</span></label>
                                    <div class="auth-input-wrap">
                                        <i class="fa fa-user auth-icon"></i>
                                        <input type="text" name="name"
                                            value="{{ old('name', auth()->user()?->name) }}"
                                            required
                                            class="auth-input @error('name') auth-input-error @enderror"
                                            placeholder="{{ __('db.Your full name') }}">
                                    </div>
                                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                </div>
                                <div>
                                    <label class="auth-label">{{ __('db.Email Address') }} <span class="text-red-400">*</span></label>
                                    <div class="auth-input-wrap">
                                        <i class="fa fa-envelope auth-icon"></i>
                                        <input type="email" name="email"
                                            value="{{ old('email', auth()->user()?->email) }}"
                                            required
                                            class="auth-input @error('email') auth-input-error @enderror"
                                            placeholder="your@email.com">
                                    </div>
                                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div class="mt-4">
                                <label class="auth-label">{{ __('db.Phone / WhatsApp') }} <span class="text-gray-400 font-normal">({{ __('db.optional') }})</span></label>
                                <div class="auth-input-wrap">
                                    <i class="fa fa-phone auth-icon"></i>
                                    <input type="text" name="phone"
                                        value="{{ old('phone', auth()->user()?->phone) }}"
                                        class="auth-input"
                                        placeholder="+92 3XX XXXXXXX">
                                </div>
                            </div>

                            {{-- Subject --}}
                            <div class="mt-4">
                                <label class="auth-label">{{ __('db.Subject') }} <span class="text-red-400">*</span></label>
                                <div class="auth-input-wrap">
                                    <i class="fa fa-tag auth-icon"></i>
                                    <input type="text" name="subject"
                                        value="{{ old('subject') }}"
                                        required
                                        class="auth-input @error('subject') auth-input-error @enderror"
                                        placeholder="{{ __('db.Brief subject of your message') }}">
                                </div>
                                <x-input-error :messages="$errors->get('subject')" class="mt-1" />
                            </div>

                            {{-- Message --}}
                            <div class="mt-4">
                                <label class="auth-label">{{ __('db.Your Message') }} <span class="text-red-400">*</span></label>
                                <textarea name="message" rows="6" required
                                    class="auth-input w-full resize-none @error('message') auth-input-error @enderror"
                                    style="padding-left: 1rem; padding-top: 0.75rem"
                                    placeholder="{{ __('db.Tell us how we can help you...') }}">{{ old('message') }}</textarea>
                                <x-input-error :messages="$errors->get('message')" class="mt-1" />
                            </div>

                            {{-- Hidden subject type --}}
                            <input type="hidden" name="subject_type_value" :value="subject">

                            {{-- Submit --}}
                            <div class="mt-6">
                                <button type="submit"
                                    class="btn-base btn-teal w-full py-4 text-base font-bold">
                                    {{ __('db.Send Message') }} <i class="fa fa-paper-plane ml-2"></i>
                                </button>
                                <p class="text-center text-xs text-gray-400 mt-3">
                                    {{ __('db.We typically respond within 24 hours, in sha Allah.') }}
                                </p>
                            </div>

                        </div>

                    </form>
                </div>

                {{-- ===== SIDEBAR ===== --}}
                <div class="space-y-6 wow fadeInRight" data-wow-delay="0.2s">

                    {{-- WhatsApp CTA --}}
                    <div class="rounded-2xl p-6 text-center" style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%)">
                        <div class="text-4xl mb-3">💬</div>
                        <h5 class="font-bold text-white mb-2">{{ __('db.Prefer WhatsApp?') }}</h5>
                        <p class="text-white/80 text-sm mb-4">{{ __('db.Chat with us directly — faster responses for urgent queries.') }}</p>
                        <a href="{{ whatsapp_link() }}" target="_blank"
                            class="btn-base inline-block px-6 py-2.5 font-semibold text-sm"
                            style="background: #fff; color: #128C7E; border-color: #fff; border-radius: 0.5rem;">
                            <i class="fab fa-whatsapp mr-2"></i>{{ __('db.Chat on WhatsApp') }}
                        </a>
                    </div>

                    {{-- FAQ --}}
                    <div class="bg-white rounded-2xl shadow-sm p-6" x-data="{ open: null }">
                        <h5 class="font-bold text-gray-800 mb-4">{{ __('db.Frequently Asked Questions') }}</h5>
                        <div class="space-y-3">
                            @foreach ([
                            [__('db.Is Sallaamti free to join?'), __('db.Yes! Creating an account and browsing courses is completely free. Some programs like Nikah verification and Live Classes have a small fee.')],
                            [__('db.How do I enroll in a Quran course?'), __('db.Register for a free account, go to Quran Courses, click Enroll, and start learning immediately.')],
                            [__('db.Is the Nikah platform safe?'), __('db.Absolutely. Every profile is CNIC-verified by our team. Photos are private until mutual acceptance.')],
                            [__('db.Can I volunteer from abroad?'), __('db.Yes! We welcome volunteers from any country. Most volunteering is done online.')],
                            [__('db.How long does donation confirmation take?'), __('db.Our team confirms your payment within 24–48 hours after submission.')],
                            ] as $i => $faq)
                            <div class="border border-gray-100 rounded-xl overflow-hidden">
                                <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                                    class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 transition-colors">
                                    <span class="text-sm font-semibold text-gray-800 pr-2">{{ $faq[0] }}</span>
                                    <i :class="open === {{ $i }} ? 'fa-chevron-up' : 'fa-chevron-down'"
                                        class="fa flex-shrink-0 text-xs" style="color: var(--teal)"></i>
                                </button>
                                <div x-show="open === {{ $i }}" x-transition
                                    class="px-4 pb-4 text-sm text-gray-500 leading-relaxed border-t border-gray-50 pt-3">
                                    {{ $faq[1] }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Social Links --}}
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h5 class="font-bold text-gray-800 mb-4">{{ __('db.Follow Us') }}</h5>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ([
                            ['fab fa-facebook-f', __('db.Facebook'), setting('social_facebook', '#'), '#1877F2'],
                            ['fab fa-instagram', __('db.Instagram'), setting('social_instagram', '#'), '#E1306C'],
                            ['fab fa-tiktok', __('db.TikTok'), setting('social_tiktok', '#'), '#000'],
                            ['fab fa-youtube', __('db.YouTube'), setting('social_youtube', '#'), '#FF0000'],
                            ] as $social)
                            <a href="{{ $social[2] }}" target="_blank"
                                class="flex items-center gap-2 p-3 rounded-xl border border-gray-100 hover:shadow-sm transition-all text-sm font-medium text-gray-700 hover:border-gray-200">
                                <i class="{{ $social[0] }} w-4 text-center" style="color: {{ $social[3] }}"></i>
                                {{ $social[1] }}
                            </a>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- MAP SECTION --}}
    {{-- ============================================================ --}}
    <section class="bg-cream">
        <div class="max-w-7xl mx-auto px-4 pb-16">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 flex flex-wrap justify-between items-center gap-4 border-b border-gray-100">
                    <div>
                        <h5 class="font-bold text-gray-800 mb-0.5">📍 {{ __('db.Our Location') }}</h5>
                        <p class="text-gray-500 text-sm mb-0">{{ setting('site_address', 'Karachi, Sindh, Pakistan') }}</p>
                    </div>
                    <a href="https://maps.google.com/?q=Karachi+Pakistan" target="_blank"
                        class="btn-base btn-teal text-sm px-5 py-2">
                        <i class="fa fa-map-marker-alt mr-2"></i>{{ __('db.Get Directions') }}
                    </a>
                </div>
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1724.8751635146577!2d71.49408424767363!3d30.158553456219234!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x393b37000634ba31%3A0x2fad0a0097640519!2sKh%20Faisal%20Home!5e0!3m2!1sen!2s!4v1783452475879!5m2!1sen!2s"
                    class="w-full"
                    style="height: 360px; border: 0; filter: grayscale(20%)"
                    allowfullscreen=""
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- FINAL CTA --}}
    {{-- ============================================================ --}}
    <section class="py-16 final-cta-section">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="text-4xl mb-4">🤲</div>
            <h2 class="final-cta-title">{{ __('db.Ready to Join the Sallaamti Family?') }}</h2>
            <p class="final-cta-sub">{{ __('db.Don\'t just reach out — become part of a global Muslim community learning, growing and connecting together.') }}</p>
            <div class="flex gap-4 justify-center flex-wrap mt-8">
                @guest
                <a href="{{ route('register') }}" class="btn-base btn-gold text-lg px-10 py-4 font-semibold">
                    {{ __('db.Register Free') }} <i class="fa fa-arrow-right ml-2"></i>
                </a>
                <a href="{{ route('wall.index', ['tag' => 'Activity']) }}" class="btn-base btn-outline-light text-lg px-10 py-4">
                    {{ __('db.Explore Activities') }}
                </a>
                @else
                <a href="{{ route('dashboard') }}" class="btn-base btn-gold text-lg px-10 py-4 font-semibold">
                    {{ __('db.Go to Dashboard') }} <i class="fa fa-arrow-right ml-2"></i>
                </a>
                @endguest
            </div>
        </div>
    </section>

</x-guest-layout>