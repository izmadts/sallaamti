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
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8 pt-8">
            <div>
                <img src="{{ asset('img/logo-w.png')}}" class="max-w-full h-auto">
                <p class="my-4 text-gray-400">{{ __('db.Sallaamti (سلامتی) is an organization dedicated to spreading peace, knowledge, and compassion through the teachings of the Quran and Hadith.') }}</p>
                <a href="{{ url('/donate') }}" class="inline-flex items-center rounded-md bg-[--teal] text-white font-medium py-2 px-4 hover:bg-[--teal-dark] transition">{{ __('db.Donate Now') }}</a>
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
                    <div class="flex items-center py-4">
                        <span class="shrink-0 flex items-center justify-center w-12 h-12 bg-[--teal] mr-3">
                            <i class="fa fa-phone-alt text-white"></i>
                        </span>
                        <a href="https://wa.me/{{ setting('social_whatsapp') }}" class="text-gray-300 hover:text-white" target="_blank">{{ setting('site_phone') }}</a>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">{{ __('db.Explore Link') }}</h4>
                <div class="flex flex-col items-start">
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ route('index') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Home') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/about') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.About Us') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/activities') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Activities') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ route('blog.index') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Blog') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/events') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Events') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/sermons') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Sermons') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/testimonial') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Testimonials') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/team') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Our Team') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/contact') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Contact us') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/donate') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Donations') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ url('/volunteer') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Become Volunteer') }}</a>
                    <a class="text-gray-300 hover:text-white mb-2" href="{{ route('certificate.verify') }}"><i class="fa fa-check text-[--teal] mr-2"></i>{{ __('db.Verify Certificate / ID') }}</a>
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
                            @if ($post->thumbnail)
                            <img src="{{ Storage::url($post->thumbnail) }}"
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
                        ['📖', 'How to Learn Quran Effectively Online', '15 Jan 2025'],
                        ['💍', 'Islamic Guide to Finding a Halal Spouse', '10 Jan 2025'],
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
                    msg.innerHTML = '<span class="text-red-400">Invalid email address.</span>';
                });
        });
    });
</script>
