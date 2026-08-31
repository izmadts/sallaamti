{{-- Shared site footer — included from both the guest layout and the authenticated app layout
     so logged-in users keep access to the public pages, donate/volunteer CTAs and newsletter signup. --}}
<div class="w-full bg-[--text-dark] pt-12">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 pb-8">
            <div class="lg:col-span-7">
                <h2 class="text-white text-2xl font-semibold mb-0">{{ __('db.Subscribe our newsletter') }}</h2>
                <p class="text-gray-400">{{ __('db.Get the latest news and other tips') }}</p>
            </div>
            <div class="lg:col-span-5">
                <div class="relative mx-auto">
                    <form id="subscribeForm" action="{{ route('subscribe') }}" method="POST">
                        @csrf
                        {{-- Honeypot: hidden from real visitors, bots that auto-fill every field will trip it --}}
                        <div class="absolute -left-[9999px]" aria-hidden="true">
                            <label for="subscribe_website">Leave this field empty</label>
                            <input type="text" name="website" id="subscribe_website" tabindex="-1" autocomplete="off">
                        </div>
                        <div class="relative mx-auto">
                            <input name="email"
                                type="email"
                                required
                                placeholder="{{ __('db.Enter your email') }}"
                                class="w-full rounded-md border-0 py-3 pl-4 pr-28 focus:ring-2 focus:ring-[--teal] focus:outline-none">
                            <button type="submit"
                                class="absolute top-1/2 -translate-y-1/2 right-2 inline-flex items-center rounded-md bg-[--teal] text-white py-2 px-4 hover:bg-[--teal-dark] transition">
                                {{ __('db.Subscribe') }}
                            </button>
                        </div>
                    </form>
                    @if(isset($errors) && $errors->has('email'))
                    <small class="text-red-400 block mt-2">{{ $errors->first('email') }}</small>
                    @endif
                    <div id="subscribe-message" class="mt-2"></div>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-700"></div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 pt-8">
            <div>
                <img src="{{ asset('img/logo-w.png')}}" class="max-w-full h-auto">
                <p class="my-4 text-gray-400">{{ __('db.Sallaamti (سلامتی) is an organization dedicated to spreading peace, knowledge, and compassion through the teachings of the Quran and Hadith.') }}</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ url('/donate') }}" class="inline-flex items-center rounded-md bg-[--teal] text-white font-medium py-2 px-4 hover:bg-[--teal-dark] transition">{{ __('db.Donate Now') }}</a>
                    <a href="{{ url('/volunteer') }}" class="inline-flex items-center rounded-md border border-gray-600 text-white font-medium py-2 px-4 hover:border-[--teal] hover:bg-white/5 transition">{{ __('db.Become Volunteer') }}</a>
                    <a href="{{ url('/nikah-counselor/apply') }}" class="inline-flex items-center rounded-md border border-gray-600 text-white font-medium py-2 px-4 hover:border-[--teal] hover:bg-white/5 transition">{{ __('db.Become a Nikah Counselor') }}</a>
                </div>
                <div class="flex flex-wrap items-center gap-3 mt-5 pt-4 border-t border-gray-700">
                    <span class="text-gray-400 text-sm">{{ __('db.Follow Us:') }}</span>
                    <a class="text-gray-300 hover:text-white" href="{{ setting('social_facebook') }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a class="text-gray-300 hover:text-white" href="{{ setting('social_tiktok') }}" target="_blank"><i class="fab fa-tiktok"></i></a>
                    <a class="text-gray-300 hover:text-white" href="{{ setting('social_youtube') }}" target="_blank"><i class="fab fa-youtube"></i></a>
                    <a class="text-gray-300 hover:text-white" href="{{ setting('social_instagram') }}" target="_blank"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-4">{{ __('db.Contact') }}</h4>
                <div class="flex flex-col">
                    <h6 class="text-gray-400 mb-0 text-sm">{{ __('db.Our Address') }}</h6>
                    <div class="flex items-center border-b border-gray-700 py-4">
                        <span class="shrink-0 flex items-center justify-center w-12 h-12 bg-[--teal] mr-3">
                            <i class="fa fa-map-marker-alt text-white"></i>
                        </span>
                        <a href="{{ url('/contact') }}" class="text-gray-300 hover:text-white">{{ setting('site_address') }}</a>
                    </div>
                    <h6 class="text-gray-400 mt-4 mb-0 text-sm">{{ __('db.Our Mobile') }}</h6>
                    <div class="flex items-center border-b border-gray-700 py-4">
                        <span class="shrink-0 flex items-center justify-center w-12 h-12 bg-[--teal] mr-3">
                            <i class="fa fa-phone-alt text-white"></i>
                        </span>
                        <a href="{{ whatsapp_link() }}" class="text-gray-300 hover:text-white" target="_blank">{{ setting('social_whatsapp', setting('site_phone')) }}</a>
                    </div>
                    @if (setting('site_landline'))
                    <h6 class="text-gray-400 mt-4 mb-0 text-sm">{{ __('db.Our Landline') }}</h6>
                    <div class="flex items-center border-b border-gray-700 py-4">
                        <span class="shrink-0 flex items-center justify-center w-12 h-12 bg-[--teal] mr-3">
                            <i class="fa fa-phone text-white"></i>
                        </span>
                        <a href="tel:{{ setting('site_landline') }}" class="text-gray-300 hover:text-white">{{ setting('site_landline') }}</a>
                    </div>
                    @endif
                    <h6 class="text-gray-400 mt-4 mb-0 text-sm">{{ __('db.Our Email') }}</h6>
                    <div class="flex items-center py-4">
                        <span class="shrink-0 flex items-center justify-center w-12 h-12 bg-[--teal] mr-3">
                            <i class="far fa-envelope text-white"></i>
                        </span>
                        <a href="mailto:{{ setting('site_email') }}" class="text-gray-300 hover:text-white">{{ setting('site_email') }}</a>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">{{ __('db.Explore Link') }}</h4>
                <div class="flex flex-col items-start">
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ route('index') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Home') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/about') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.About Us') }}</a>
                    @if (!auth()->check() || !auth()->user()->hasAnyRole(['admin', 'teacher', 'family_counselor', 'matchmaker', 'manager', 'blogger']))
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ route('wall.index') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Sallaamti Wall') }}</a>
                    @endif
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ route('blog.index') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Blog') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/team') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Team') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/testimonial') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Testimonials') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/contact') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Contact us') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/donate') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Donations') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/volunteer') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Become Volunteer') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/nikah-counselor/apply') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Become a Nikah Counselor') }}</a>
                </div>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">{{ __('db.Our Programs') }}</h4>
                <div class="flex flex-col items-start">
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ route('courses.index') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Quran Courses') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ route('quran-live.index') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Live Quran Classes') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ route('skills.index') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Digital Skills') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ route('nikah.create') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Sallaamti Nikah') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ auth()->check() ? route('counseling.book.start') : route('register') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Family Support') }}</a>
                </div>
            </div>
            <div>
                <h5 class="text-white mb-4 font-semibold">{{ __('db.Latest Posts') }}</h5>

                @if (isset($footerPosts) && $footerPosts->isNotEmpty())
                <div class="flex flex-col gap-0">
                    @foreach ($footerPosts as $post)
                    <a href="{{ url('/blog/' . $post->slug) }}"
                        class="flex items-start gap-3 py-4 border-b border-gray-700 last:border-0 group">

                        {{-- Thumbnail --}}
                        <div class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-gray-700">
                            @if ($post->cover_image)
                            <img src="{{ Storage::url($post->cover_image) }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                alt="{{ $post->title }}">
                            @else
                            <div class="w-full h-full flex items-center justify-center text-2xl">📝</div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-500 text-xs mb-1 flex items-center gap-1">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $post->published_at?->format('d M Y') }}
                                @if ($post->category)
                                <span class="text-gray-600">·</span>
                                <span>{{ $post->category }}</span>
                                @endif
                            </p>
                            <p class="text-gray-300 text-sm leading-snug group-hover:text-white transition-colors line-clamp-2">
                                {{ $post->title }}
                            </p>
                        </div>

                    </a>
                    @endforeach
                </div>

                <a href="{{ url('/blog') }}"
                    class="inline-flex items-center gap-1 text-xs mt-3 transition-colors"
                    style="color: #b8962e">
                    {{ __('db.View all posts') }}
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>

                @else
                {{-- No posts yet — show placeholder --}}
                <div class="flex flex-col gap-0">
                    @foreach ([
                    ['📖', __('db.How to Learn Quran Effectively Online'), __('db.15 Jan 2025')],
                    ['💍', __('db.Islamic Guide to Finding a Halal Spouse'), __('db.10 Jan 2025')],
                    ] as $placeholder)
                    <div class="flex items-start gap-3 py-4 border-b border-gray-700 last:border-0 opacity-40">
                        <div class="flex-shrink-0 w-16 h-16 rounded-lg bg-gray-700 flex items-center justify-center text-2xl">
                            {{ $placeholder[0] }}
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-500 text-xs mb-1">{{ $placeholder[2] }}</p>
                            <p class="text-gray-400 text-sm leading-snug">{{ $placeholder[1] }}</p>
                        </div>
                    </div>
                    @endforeach
                    <p class="text-xs text-gray-600 mt-3">{{ __('db.Blog articles coming soon...') }}</p>
                </div>
                @endif

            </div>
        </div>
    </div>
    <div class="border-t border-gray-700">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-3 text-gray-400 text-sm">
                <div class="text-center md:text-left">
                    &copy; <a class="border-b border-gray-500" href="{{ url('/') }}">www.sallaamti.com</a>, {{ __('db.All Right Reserved.') }}
                    <span class="mx-2 text-gray-600">·</span>
                    <a class="hover:text-white" href="{{ route('privacy-policy') }}">{{ __('db.Privacy Policy') }}</a>
                    <span class="mx-2 text-gray-600">·</span>
                    <a class="hover:text-white" href="{{ route('terms-of-service') }}">{{ __('db.Terms of Service') }}</a>
                    <span class="mx-2 text-gray-600">·</span>
                    <a class="hover:text-white" href="{{ route('nikah-counselor.code-of-conduct') }}">{{ __('db.Nikah Counselor Code of Conduct') }}</a>
                </div>
                <div class="text-center md:text-right">
                    {{ __('db.Designed & Developed By') }} <a class="border-b border-gray-500" href="https://izmadts.com">IZMAdts</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Footer -->
<!-- Back to Top -->
<a href="#"
    x-data="{ show: false }"
    x-init="window.addEventListener('scroll', () => show = window.scrollY > 300)"
    x-show="show"
    x-cloak
    x-transition
    class="fixed bottom-6 right-6 z-50 inline-flex items-center justify-center w-11 h-11 rounded-md bg-[--teal] text-white border-2 border-white shadow-lg hover:bg-[--teal-dark] transition">
    <i class="fa fa-arrow-up"></i>
</a>
<!-- Newsletter subscribe AJAX (jQuery removed — vanilla fetch) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('subscribeForm');
        const msg = document.getElementById('subscribe-message');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: new FormData(form)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        msg.innerHTML = '<span class="text-green-400">' + data.message + '</span>';
                        form.reset();
                        window.dataLayer = window.dataLayer || [];
                        dataLayer.push({
                            event: 'newsletter_signup'
                        });
                    } else {
                        msg.innerHTML = '<span class="text-yellow-400">' + data.message + '</span>';
                    }
                })
                .catch(() => {
                    msg.innerHTML = '<span class="text-red-400">' + {{ Js::from(__('db.Invalid email address.')) }} + '</span>';
                });
        });
    });
</script>