{{-- resources/views/index.blade.php --}}
<x-guest-layout :description="'Quran education, halal matrimonial matching, and community programs for the Muslim Ummah — ' . setting('site_tagline')">
    @section('title', 'Sallaamti — Learn Quran Online | Live Classes | Islamic Matrimonial')
    @section('description', 'Join Sallaamti to learn Quran online with expert teachers. Self-paced Quran courses, live classes, halal matrimonial platform and family counseling. Free to join.')
    @section('keywords', 'learn quran online pakistan, online quran classes, quran teacher online, islamic matrimonial pakistan, nikah platform')
    @section('og_title', 'Sallaamti — Learn Quran Online | Live Classes | Islamic Matrimonial')
    @section('og_description', 'Learn Quran with expert teachers. Join thousands of Muslims worldwide.')
    @section('og_image', asset('img/og-home.jpg'))

    @push('schema')
    @php
    $websiteSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => 'Sallaamti',
    'url' => config('app.url'),
    'potentialAction' => [
    '@type' => 'SearchAction',
    'target' => config('app.url') . '/courses?search={search_term_string}',
    'query-input' => 'required name=search_term_string',
    ],
    ];
    @endphp
    <script type="application/ld+json">
        {
            !!json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!
        }
    </script>
    @endpush
    {{-- rest of page content --}}
    {{-- ============================================================ --}}
    {{-- HERO --}}
    {{-- ============================================================ --}}
    <section class="hero-section relative overflow-hidden"
        x-data="{
        active: 0,
        total: {{ count($banners) }},
        next() { this.active = (this.active + 1) % this.total },
        prev() { this.active = (this.active - 1 + this.total) % this.total }
    }"
        x-init="setInterval(() => next(), 5000)">

        {{-- Slides --}}
        @foreach ($banners as $i => $banner)
        <div class="absolute inset-0 transition-opacity duration-700"
            :class="{ 'opacity-100 z-10': active === {{ $i }}, 'opacity-0 z-0': active !== {{ $i }} }">
            <div class="hero-bg-overlay absolute inset-0"></div>
            @if ($i === 0)
            <img src="{{ str_starts_with($banner->image, 'img/') ? asset($banner->image) : Storage::url($banner->image) }}" class="block w-full hero-bg-img" fetchpriority="high" alt="{{ $banner->title }}">
            @else
            {{-- Other slides — lazy load --}}
            <img src="{{ str_starts_with($banner->image, 'img/') ? asset($banner->image) : Storage::url($banner->image) }}" class="block w-full hero-bg-img" loading="lazy" alt="{{ $banner->title }}">
            @endif
            <!-- <img src="{{ str_starts_with($banner->image, 'img/') ? asset($banner->image) : Storage::url($banner->image) }}"
                class="block w-full hero-bg-img" alt="{{ $banner->title }}" fetchpriority="high"> -->
        </div>
        @endforeach

        {{-- Hero Content --}}
        <div class="hero-content absolute inset-0 flex items-center z-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                <div class="grid lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-7">
                        <span class="hero-eyebrow">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ</span>
                        <h1 class="hero-title">
                            {{ __('db.Learn Quran.') }}<br>
                            {{ __('db.Find Your') }} <span class="text-gold">{{ __('db.Match.') }}</span><br>
                            {{ __('db.Build Community.') }}
                        </h1>
                        <p class="hero-subtitle">
                            {{ __('db.Join thousands of Muslims worldwide learning the Quran with expert teachers, finding halal matches, and growing together in faith.') }}
                        </p>
                        <div class="flex gap-3 flex-wrap mt-6">
                            @auth
                            <a href="{{ route('dashboard') }}" class="btn-gold btn-base text-lg px-8 py-3 font-semibold">
                                {{ __('db.Go to Dashboard') }} <i class="fa fa-arrow-right ml-2"></i>
                            </a>
                            @else
                            <a href="{{ route('register') }}" class="btn-gold btn-base text-lg px-8 py-3 font-semibold">
                                {{ __('db.Register Today') }} <i class="fa fa-arrow-right ml-2"></i>
                            </a>
                            {{-- Browse Courses CTA hidden for now --}}
                            {{-- <a href="{{ route('courses.index') }}" class="btn-outline-light btn-base text-lg px-8 py-3">
                                {{ __('db.Browse Courses') }}
                            </a> --}}
                            @endauth
                        </div>
                        <div class="hero-trust-bar flex gap-4 mt-4 flex-wrap">
                            <span class="trust-item"><i class="fa fa-check-circle text-gold mr-1"></i>{{ __('db.Free to Join') }}</span>
                            <span class="trust-item"><i class="fa fa-check-circle text-gold mr-1"></i>{{ __('db.Expert Teachers') }}</span>
                            <span class="trust-item"><i class="fa fa-check-circle text-gold mr-1"></i>{{ __('db.Global Access') }}</span>
                            <span class="trust-item"><i class="fa fa-check-circle text-gold mr-1"></i>{{ __('db.Certified Courses') }}</span>
                        </div>
                    </div>

                    <div class="lg:col-span-5 hidden lg:block">
                        <div class="hero-card-stack">
                            <div class="floating-card fc-1">
                                <span class="fc-icon">📖</span>
                                <div><strong>{{ __('db.Quran Learning') }}</strong><small>{{ __('db.Nazrah · Tajweed · Translation') }}</small></div>
                            </div>
                            <div class="floating-card fc-2">
                                <span class="fc-icon">🎥</span>
                                <div><strong>{{ __('db.Live Classes') }}</strong><small>{{ __('db.One-to-One & Group') }}</small></div>
                            </div>
                            <div class="floating-card fc-3">
                                <span class="fc-icon">💍</span>
                                <div><strong>{{ __('db.Nikah Platform') }}</strong><small>{{ __('db.Verified · Guardian-mediated') }}</small></div>
                            </div>
                            <div class="floating-card fc-4">
                                <span class="fc-icon">🎓</span>
                                <div><strong>{{ __('db.Certificates') }}</strong><small>{{ __('db.Internationally recognized') }}</small></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Controls --}}
        <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 z-30 w-10 h-10 rounded-full bg-white/20 text-white flex items-center justify-center hover:bg-white/40 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 z-30 w-10 h-10 rounded-full bg-white/20 text-white flex items-center justify-center hover:bg-white/40 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        {{-- Indicators --}}
        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 z-30 flex gap-2">
            @foreach ($banners as $i => $banner)
            <button @click="active = {{ $i }}"
                :class="active === {{ $i }} ? 'bg-white w-6' : 'bg-white/40 w-2'"
                class="h-2 rounded-full transition-all duration-300"></button>
            @endforeach
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- LIVE STATS TICKER --}}
    {{-- ============================================================ --}}
    <section class="stats-ticker bg-teal py-6">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 text-center text-white">
                <div class="stat-item">
                    <span class="stat-number" data-target="{{ \App\Models\User::count() }}">0</span>
                    <span class="stat-label">{{ __('db.Members Worldwide') }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-target="{{ \App\Models\Enrollment::count() }}">0</span>
                    <span class="stat-label">{{ __('db.Course Enrollments') }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-target="{{ \App\Models\Certificate::count() }}">0</span>
                    <span class="stat-label">{{ __('db.Certificates Issued') }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-target="{{ \App\Models\NikahProfile::where('verification_status','verified')->count() }}">0</span>
                    <span class="stat-label">{{ __('db.Verified Nikah Profiles') }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- FLAGSHIP: NIKAH & FAMILY COUNSELING --}}
    {{-- ============================================================ --}}
    <section class="py-16" style="background: linear-gradient(135deg, #0d6b6b 0%, #0a5555 100%)">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-10">
                <span class="text-gold text-sm font-semibold uppercase tracking-widest">{{ __('db.Our Most-Loved Services') }}</span>
                <h2 class="text-white text-3xl font-bold mt-2">{{ __('db.Find a Match. Find Support.') }}</h2>
            </div>
            <div class="grid md:grid-cols-2 gap-6">
                <a href="{{ route('nikah.create') }}" class="group bg-white rounded-2xl p-8 flex flex-col hover:-translate-y-1 transition-transform shadow-xl">
                    <span class="text-4xl mb-3">💍</span>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ __('db.Nikah Matchmaking') }}</h3>
                    <p class="text-gray-500 mb-5 flex-1">{{ __('db.A guided, step-by-step profile builder — verified, guardian-mediated, and built for serious intentions.') }}</p>
                    <span class="inline-flex items-center gap-2 font-semibold text-teal-700 group-hover:gap-3 transition-all">
                        {{ __('db.Create Your Profile') }} <i class="fa fa-arrow-right"></i>
                    </span>
                </a>
                <a href="{{ route('counseling.book.start') }}" class="group bg-white rounded-2xl p-8 flex flex-col hover:-translate-y-1 transition-transform shadow-xl">
                    <span class="text-4xl mb-3">🤝</span>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ __('db.Family Counseling') }}</h3>
                    <p class="text-gray-500 mb-5 flex-1">{{ __('db.Book a private session with a qualified counselor in a few simple steps — marital, parenting, or spiritual guidance.') }}</p>
                    <span class="inline-flex items-center gap-2 font-semibold text-teal-700 group-hover:gap-3 transition-all">
                        {{ __('db.Book a Session') }} <i class="fa fa-arrow-right"></i>
                    </span>
                </a>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- WHO IS THIS FOR --}}
    {{-- ============================================================ --}}
    <section class="py-20 bg-cream">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="section-eyebrow">For Every Muslim</span>
                <h2 class="section-title">Sallaamti is Built For You</h2>
                <p class="section-subtitle">Whether you're a parent, student, professional, or seeking a spouse — we have something meaningful for you.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="persona-card">
                    <div class="persona-icon">👨‍👩‍👧</div>
                    <h4>Parents & Families</h4>
                    <p>Enroll your children in structured Quran classes with qualified teachers. Get monthly progress reports.</p>
                    <ul class="persona-list">
                        <li>Supervised live classes</li>
                        <li>Male & Female teachers available</li>
                        <li>Monthly progress reports</li>
                        <li>Parent dashboard access</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn-teal-outline btn-base mt-4 inline-block">Enroll Your Child →</a>
                </div>
                <div class="persona-card persona-featured">
                    <div class="persona-badge">Most Popular</div>
                    <div class="persona-icon">🧑‍🎓</div>
                    <h4>Adults & Youth</h4>
                    <p>Learn at your own pace with video courses or join live classes. Earn certificates upon completion.</p>
                    <ul class="persona-list">
                        <li>Self-paced video courses</li>
                        <li>Live online classes</li>
                        <li>Quizzes & assessments</li>
                        <li>Completion certificates</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn-gold btn-base mt-4 inline-block">Start Learning Free →</a>
                </div>
                <div class="persona-card">
                    <div class="persona-icon">🌍</div>
                    <h4>International Muslims</h4>
                    <p>Access Quran education and Islamic matrimonial services from anywhere in the world.</p>
                    <ul class="persona-list">
                        <li>Pakistan & international pricing</li>
                        <li>All timezones supported</li>
                        <li>Urdu & English classes</li>
                        <li>Global Nikah platform</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn-teal-outline btn-base mt-4 inline-block">Join From Abroad →</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- OUR PROGRAMS --}}
    {{-- ============================================================ --}}
    <section class="py-20 bg-white" x-data="{ tab: 'quran' }">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="section-eyebrow">What We Offer</span>
                <h2 class="section-title">Our Programs & Services</h2>
            </div>

            {{-- Tab Nav --}}
            <div class="flex flex-wrap justify-center gap-3 mb-12">
                <button @click="tab = 'quran'" :class="tab === 'quran' ? 'program-tab-active' : ''" class="program-tab">📖 Quran Courses</button>
                <button @click="tab = 'live'" :class="tab === 'live' ? 'program-tab-active' : ''" class="program-tab">🎥 Live Classes</button>
                <button @click="tab = 'nikah'" :class="tab === 'nikah' ? 'program-tab-active' : ''" class="program-tab">💍 Nikah</button>
                <button @click="tab = 'skills'" :class="tab === 'skills' ? 'program-tab-active' : ''" class="program-tab">💻 Skills</button>
            </div>

            {{-- Quran Tab --}}
            <div x-show="tab === 'quran'" x-transition>
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <span class="section-eyebrow">Self-Paced Learning</span>
                        <h3 class="text-2xl font-bold mb-4">Learn Quran at Your Own Pace</h3>
                        <p class="text-gray-500 mb-6">Our structured courses take you from basic Arabic alphabet all the way to deep Tafseer. Video lessons, quizzes, and a certificate upon completion.</p>
                        <div class="learning-path my-6">
                            @foreach ([['Level 1', 'Nazrah Quran', '6-12 months', '📖'], ['Level 2', 'Tajweed', '6-8 months', '🎵'], ['Level 3', 'Translation', '6-8 months', '📝'], ['Level 4', 'Arabic Grammar', '4-6 months', '🔤']] as $step)
                            <div class="learning-step">
                                <span class="step-icon">{{ $step[3] }}</span>
                                <div class="step-content">
                                    <strong>{{ $step[0] }}: {{ $step[1] }}</strong>
                                    <small>Duration: {{ $step[2] }}</small>
                                </div>
                            </div>
                            @if (!$loop->last)<div class="step-arrow">↓</div>@endif
                            @endforeach
                        </div>
                        <div class="flex gap-3 flex-wrap">
                            <a href="{{ route('courses.index') }}" class="btn-teal btn-base px-6">Browse All Courses</a>
                            @guest
                            <a href="{{ route('register') }}" class="btn-gold btn-base px-6">Join Free</a>
                            @endguest
                        </div>
                    </div>
                    <div>
                        <div class="course-preview-grid">
                            @php $previewCourses = \App\Models\Course::where('is_published', true)->take(4)->get(); @endphp
                            @foreach ($previewCourses as $course)
                            <div class="course-preview-card">
                                @if ($course->thumbnail)
                                <img src="{{ Storage::url($course->thumbnail) }}" loading="lazy" alt="{{ $course->title }}">
                                @else
                                <div class="course-preview-placeholder">📖</div>
                                @endif
                                <div class="course-preview-info">
                                    <strong>{{ $course->title }}</strong>
                                    <small>{{ $course->category }}</small>
                                </div>
                            </div>
                            @endforeach
                            @if ($previewCourses->isEmpty())
                            <div class="col-span-2 text-center p-8 text-gray-400">
                                <div class="text-5xl mb-2">📖</div>
                                <p class="text-sm">Courses launching soon</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Live Tab --}}
            <div x-show="tab === 'live'" x-transition>
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <span class="section-eyebrow">Online Live Sessions</span>
                        <h3 class="text-2xl font-bold mb-4">Live Quran Classes with Expert Teachers</h3>
                        <p class="text-gray-500 mb-6">One-to-one and group live classes via Zoom. Fixed schedule, monthly subscription, personal attention for every student.</p>
                        <div class="feature-list my-6">
                            @foreach (['5 classes per week, 30–45 mins each', 'One-to-one OR group class options', 'Male or Female teachers available', 'Weekly tests + Monthly progress reports', 'PKR 3,000–5,000/month | $25–$50 international'] as $f)
                            <div class="feature-item">
                                <span class="feature-check">✅</span> {{ $f }}
                            </div>
                            @endforeach
                        </div>
                        <a href="{{ route('quran-live.index') }}" class="btn-teal btn-base px-6">View Live Courses</a>
                    </div>
                    <div>
                        <div class="zoom-mock">
                            <div class="zoom-header">
                                <span class="zoom-dot red"></span>
                                <span class="zoom-dot yellow"></span>
                                <span class="zoom-dot green"></span>
                                <span class="zoom-title">Sallaamti Live Class — Tajweed Level 2</span>
                            </div>
                            <div class="zoom-body">
                                <div class="zoom-participant teacher">
                                    <div class="participant-avatar">👩‍🏫</div><span>Teacher</span>
                                </div>
                                <div class="zoom-participant">
                                    <div class="participant-avatar">🧑‍🎓</div><span>Student 1</span>
                                </div>
                                <div class="zoom-participant">
                                    <div class="participant-avatar">👦</div><span>Student 2</span>
                                </div>
                                <div class="zoom-participant">
                                    <div class="participant-avatar">👧</div><span>Student 3</span>
                                </div>
                            </div>
                            <div class="zoom-footer">
                                <span class="live-badge">🔴 LIVE</span>
                                <span class="text-gray-400 text-xs">Sallaamti.com · Secure Session</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Nikah Tab --}}
            <div x-show="tab === 'nikah'" x-transition>
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <span class="section-eyebrow">Islamic Matrimonial</span>
                        <h3 class="text-2xl font-bold mb-4">Find a Halal Match — Properly</h3>
                        <p class="text-gray-500 mb-6">No casual swiping. Every profile is CNIC-verified. Guardian-mediated contact only. Your photo is private until both sides accept.</p>
                        <div class="nikah-trust-list my-6">
                            @foreach ([['🔒', 'CNIC Verified', 'Every profile reviewed by our team'], ['👨‍👩‍👧', 'Guardian Mediated', 'Contact only through guardian channels'], ['📸', 'Private Photos', 'Photos visible only after mutual acceptance'], ['💳', 'Serious Members Only', 'Small verification fee filters out non-serious'], ['🌍', 'Global Platform', 'Muslims from Pakistan and worldwide']] as $item)
                            <div class="nikah-trust-item">
                                <span class="nikah-icon">{{ $item[0] }}</span>
                                <div><strong>{{ $item[1] }}</strong><small>{{ $item[2] }}</small></div>
                            </div>
                            @endforeach
                        </div>
                        <a href="{{ route('register') }}" class="btn-teal btn-base px-6">Create Your Profile</a>
                    </div>
                    <div>
                        <div class="nikah-preview-card">
                            <div class="nikah-card-header"><span class="verified-badge">✅ Verified Profile</span></div>
                            <div class="nikah-card-body">
                                <div class="nikah-avatar">👤</div>
                                <div class="nikah-info">
                                    <h5>28 yrs · Karachi</h5>
                                    <p>Education: Masters · Profession: Engineer</p>
                                    <p>Sect: Sunni · Family: Nuclear</p>
                                </div>
                            </div>
                            <div class="nikah-card-footer">
                                <div class="match-score">
                                    <div class="match-bar" style="width:78%"></div><span>78% Match</span>
                                </div>
                                <span class="private-photo">🔒 Photo Private</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Skills Tab --}}
            <div x-show="tab === 'skills'" x-transition>
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <span class="section-eyebrow">Coming Soon</span>
                        <h3 class="text-2xl font-bold mb-4">Skills Training for Dignified Livelihood</h3>
                        <p class="text-gray-500 mb-6">Empowering every Muslim with practical skills that lead to halal income and independence.</p>
                        <div class="skills-grid my-6">
                            @foreach (['💻 Computer Skills', '🎨 Graphic Design', '🌐 Web Development', '📱 Mobile Apps', '📈 Digital Marketing', '✂️ Tailoring', '⚡ Electrician', '🔧 Plumbing'] as $skill)
                            <span class="skill-tag">{{ $skill }}</span>
                            @endforeach
                        </div>
                        <a href="{{ route('volunteer.create') }}" class="btn-teal btn-base px-6">Join as Instructor Volunteer</a>
                    </div>
                    <div class="text-center">
                        <div class="coming-soon-badge inline-block">
                            <div class="cs-icon">🚀</div>
                            <h4>Launching Soon</h4>
                            <p class="text-gray-500 mb-4">Register now to be notified when Skills Training launches</p>
                            <a href="{{ route('register') }}" class="btn-gold btn-base px-6">Get Early Access</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- HOW IT WORKS --}}
    {{-- ============================================================ --}}
    <section class="py-20 bg-teal-light">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="section-eyebrow">Simple Process</span>
                <h2 class="section-title">How to Get Started</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="how-step relative">
                    <div class="how-number">1</div>
                    <div class="how-icon">📝</div>
                    <h5 class="font-bold text-lg mb-2">Register Free</h5>
                    <p class="text-gray-500">Create your account in under 2 minutes. Name, email, phone — that's all we need.</p>
                </div>
                <div class="how-step how-step-featured relative">
                    <div class="how-number">2</div>
                    <div class="how-icon">🎯</div>
                    <h5 class="font-bold text-lg mb-2">Choose Your Path</h5>
                    <p>Pick Quran courses, live classes, Nikah profile, or all three.</p>
                </div>
                <div class="how-step relative">
                    <div class="how-number">3</div>
                    <div class="how-icon">🌟</div>
                    <h5 class="font-bold text-lg mb-2">Grow & Earn</h5>
                    <p class="text-gray-500">Learn Quran, earn certificates, find a match, join a global community.</p>
                </div>
            </div>
            <div class="text-center mt-12">
                @guest
                <a href="{{ route('register') }}" class="btn-gold btn-base text-lg px-10 py-4 font-semibold">
                    Start Your Journey — It's Free <i class="fa fa-arrow-right ml-2"></i>
                </a>
                @else
                <a href="{{ route('dashboard') }}" class="btn-gold btn-base text-lg px-10 py-4 font-semibold">
                    Go to Dashboard <i class="fa fa-arrow-right ml-2"></i>
                </a>
                @endguest
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- ABOUT --}}
    {{-- ============================================================ --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid xl:grid-cols-2 gap-12 items-center">
                <div class="grid grid-cols-2 gap-3">
                    <img src="{{ asset('img/about-1.jpg') }}" loading="lazy" class="max-w-full rounded-2xl h-full object-cover wow zoomIn" data-wow-delay="0.1s" alt="">
                    <div class="flex flex-col gap-3">
                        <img src="{{ asset('img/about-2.jpg') }}" loading="lazy" class="max-w-full rounded-2xl wow zoomIn" data-wow-delay="0.2s" alt="">
                        <img src="{{ asset('img/about-3.jpg') }}" loading="lazy" class="max-w-full rounded-2xl wow zoomIn" data-wow-delay="0.3s" alt="">
                    </div>
                </div>
                <div>
                    <span class="section-eyebrow">About Sallaamti</span>
                    <h2 class="section-title mb-6">{{ setting('about_heading', 'Allah Helps Those Who Help Themselves') }}</h2>
                    <p class="text-gray-600 mb-6">{{ setting('about_text', 'Sallaamti is dedicated to spreading the teachings of the Quran and Hadith, enlightening individuals with Islamic wisdom.') }}</p>
                    <div class="grid sm:grid-cols-2 gap-4 mt-4">
                        <div class="vision-card">
                            <i class="fa fa-eye text-teal mr-2"></i>
                            <div>
                                <h6 class="font-semibold">Our Vision</h6>
                                <p class="text-sm text-gray-500 mb-0">{{ setting('vision_text', 'A world where every Muslim lives by Quran and Sunnah.') }}</p>
                            </div>
                        </div>
                        <div class="vision-card">
                            <i class="fa fa-flag text-gold mr-2"></i>
                            <div>
                                <h6 class="font-semibold">Our Mission</h6>
                                <p class="text-sm text-gray-500 mb-0">{{ setting('mission_text', 'Empowering communities through knowledge, compassion and action.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- SYLLABUS --}}
    {{-- ============================================================ --}}
    <section class="py-20 bg-cream">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="section-eyebrow">Complete Curriculum</span>
                <h2 class="section-title">A Structured Path to Quranic Knowledge</h2>
                <p class="section-subtitle">Our 4-level syllabus takes you from basics to deep understanding</p>
            </div>
            <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-6">
                @foreach ([
                ['Level 1', 'Nazrah Quran', '6–12 months', '#0d6b6b', ['Arabic Alphabet (Makharij)', 'Zabar, Zair, Pesh', 'Basic Tajweed Rules', 'Fluency & Accuracy'], 'Student reads Quran fluently with correct pronunciation.'],
                ['Level 2', 'Tajweed', '6–8 months', '#b8962e', ['Makharij Detail', 'Sifaat (Letter Characteristics)', 'Noon Sakinah & Tanween', 'Qalqalah, Ghunna, Idgham'], 'Student recites Quran beautifully with proper Tajweed.'],
                ['Level 3', 'Translation', '6–8 months', '#1a5276', ['100 Common Quran Words', 'Word by Word Translation', 'Short Surah Translation', 'Easy Tafseer'], 'Student understands Quran with translation & simple Tafseer.'],
                ['Level 4', 'Arabic Grammar', '4–6 months', '#922b21', ['Arabic Alphabets Revision', 'Nouns, Verbs, Letters', 'Masculine, Feminine, Plural', 'Sentence Structure'], 'Student connects with Quranic Arabic language.']
                ] as $level)
                <div class="syllabus-card" style="border-top: 4px solid {{ $level[3] }}">
                    <div class="syllabus-header">
                        <span class="syllabus-badge" style="background: {{ $level[3] }}">{{ $level[0] }}</span>
                        <h5>{{ $level[1] }}</h5>
                        <small class="text-gray-500">Duration: {{ $level[2] }}</small>
                    </div>
                    <ul class="syllabus-topics">
                        @foreach ($level[4] as $topic)<li>{{ $topic }}</li>@endforeach
                    </ul>
                    <div class="syllabus-outcome"><strong>Outcome:</strong> {{ $level[5] }}</div>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-8 flex gap-3 justify-center flex-wrap">
                <a href="{{ route('courses.index') }}" class="btn-teal btn-base px-8">View All Courses</a>
                <a href="{{ route('register') }}" class="btn-gold btn-base px-8">Register & Start</a>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- NIKAH AWARENESS --}}
    {{-- ============================================================ --}}
    <section class="py-20 nikah-awareness-section">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="section-eyebrow text-gold">Islamic Matrimonial</span>
                    <h2 class="section-title text-white">Marriage Is Half of Your Deen</h2>
                    <p class="text-white/60 mb-4">The Prophet ﷺ said: "Whoever Allah blesses with a righteous spouse, He has helped him with half of his religion." — Ibn Hibban</p>
                    <p class="text-white/60 mb-6">Sallaamti's Nikah platform is built on Islamic values: verified profiles, guardian-mediated communication, complete privacy protection.</p>
                    <div class="nikah-stats-row">
                        <div class="nikah-stat">
                            <span class="nikah-stat-n">{{ \App\Models\NikahProfile::where('verification_status','verified')->count() }}+</span>
                            <span>Verified Profiles</span>
                        </div>
                        <div class="nikah-stat">
                            <span class="nikah-stat-n">100%</span>
                            <span>CNIC Verified</span>
                        </div>
                        <div class="nikah-stat">
                            <span class="nikah-stat-n">🔒</span>
                            <span>Private Photos</span>
                        </div>
                    </div>
                    @guest
                    <a href="{{ route('register') }}" class="btn-gold btn-base px-8 py-4 mt-6 inline-block font-semibold">Create Your Profile →</a>
                    @else
                    <a href="{{ route('nikah.show') }}" class="btn-gold btn-base px-8 py-4 mt-6 inline-block font-semibold">Go to Nikah Profile →</a>
                    @endguest
                </div>
                <div>
                    <div class="nikah-process-steps">
                        @foreach ([
                        ['1', 'Register & Create Profile', 'Fill your profile with personal, educational and family details'],
                        ['2', 'Pay Verification Fee', 'A small fee confirms you are serious and funds the platform'],
                        ['3', 'CNIC Verification', 'Our team verifies your identity for a trusted community'],
                        ['4', 'Browse & Express Interest', 'Find compatible matches and send an interest request'],
                        ['5', 'Guardian Contact', 'After mutual acceptance, guardian contact details are shared'],
                        ] as $step)
                        <div class="nikah-process-step">
                            <div class="process-num">{{ $step[0] }}</div>
                            <div>
                                <strong>{{ $step[1] }}</strong>
                                <p class="text-sm text-white/60 mb-0">{{ $step[2] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- ACTIVITIES --}}
    {{-- ============================================================ --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="section-eyebrow">Activities</span>
                <h2 class="section-title">What We Do at Sallaamti</h2>
                <p class="section-subtitle">From Quranic education to matrimonial services, family support to community giving — everything in one trusted platform.</p>
            </div>
            <div class="grid lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ([
                [
                'icon' => 'fa-book-open',
                'color' => 'text-teal',
                'emoji' => '📖',
                'title' => 'Quran Learning',
                'desc' => 'Structured self-paced Quran courses — Nazrah, Tajweed, Translation and Arabic Grammar — with quizzes and certificates.',
                'url' => route('courses.index'),
                'btn' => 'Explore Courses',
                'badge' => null,
                ],
                [
                'icon' => 'fa-laptop',
                'color' => 'text-gold',
                'emoji' => '🎥',
                'title' => 'Live Quran Classes',
                'desc' => 'One-to-one and group live online classes with qualified teachers. Flexible timings for Pakistan and international students.',
                'url' => route('quran-live.index'),
                'btn' => 'View Live Classes',
                'badge' => null,
                ],
                [
                'icon' => 'fa-heart',
                'color' => 'text-red-500',
                'emoji' => '💍',
                'title' => 'Sallaamti Nikah',
                'desc' => 'A verified, CNIC-checked Islamic matrimonial platform. Guardian-mediated, photo-private and built on Islamic values.',
                'url' => route('nikah.create'),
                'btn' => 'Create Profile',
                'badge' => null,
                ],
                [
                'icon' => 'fa-hands-helping',
                'color' => 'text-purple-500',
                'emoji' => '💑',
                'title' => 'Family Support',
                'desc' => 'Submit a confidential query and our qualified counselors will guide you on marital, parenting, financial or spiritual matters.',
                'url' => auth()->check() ? route('support.create') : route('register'),
                'btn' => auth()->check() ? 'Get Support' : 'Join & Get Support',
                'badge' => 'Live',
                ],
                [
                'icon' => 'fa-hand-holding-heart',
                'color' => 'text-gold',
                'emoji' => '💝',
                'title' => 'Donate & Support',
                'desc' => 'Fund Quran education for orphans, support needy families and keep Sallaamti free for students worldwide.',
                'url' => route('donate.create'),
                'btn' => 'Donate Now',
                'badge' => null,
                ],
                [
                'icon' => 'fa-users',
                'color' => 'text-teal',
                'emoji' => '🤝',
                'title' => 'Volunteer with Us',
                'desc' => 'Join as a Quran teacher, counselor, developer or outreach volunteer. Earn Sadaqah Jariyah while serving the Ummah.',
                'url' => route('volunteer.create'),
                'btn' => 'Apply as Volunteer',
                'badge' => null,
                ],
                ] as $activity)

                <div class="activity-card group relative">

                    {{-- Live / Coming Soon badge --}}
                    @if ($activity['badge'])
                    <div class="absolute top-4 right-4">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full text-white"
                            style="background: #16a34a">
                            ✅ {{ $activity['badge'] }}
                        </span>
                    </div>
                    @endif

                    {{-- Icon --}}
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4 text-2xl transition-transform duration-300 group-hover:scale-110"
                        style="background: var(--teal-light)">
                        {{ $activity['emoji'] }}
                    </div>

                    {{-- Title --}}
                    <h5 class="font-bold text-lg text-gray-800 mb-2">{{ $activity['title'] }}</h5>

                    {{-- Description --}}
                    <p class="text-gray-500 text-sm leading-relaxed mb-5 flex-1">{{ $activity['desc'] }}</p>

                    {{-- CTA --}}
                    <a href="{{ $activity['url'] }}"
                        class="btn-base text-sm px-5 py-2 font-semibold inline-block
                    {{ $activity['url'] === '#' ? 'btn-disabled' : 'btn-teal' }}">
                        {{ $activity['btn'] }}
                        @if ($activity['url'] !== '#')
                        <i class="fa fa-arrow-right ml-1 text-xs"></i>
                        @endif
                    </a>

                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- TESTIMONIALS --}}
    {{-- ============================================================ --}}
    @if ($testimonials->isNotEmpty())
    <section class="py-20 overflow-hidden" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%)">
        <div class="max-w-7xl mx-auto px-4">

            {{-- Header --}}
            <div class="text-center mb-14">
                <span class="section-eyebrow" style="color: rgba(255,255,255,0.6)">Community Stories</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-white mt-2">What Our Community Says</h2>
                <p class="text-white/50 text-sm mt-3">Real experiences from students, parents and families around the world.</p>
            </div>

            {{-- Alpine Slider --}}
            <div
                x-data="{
                active: 0,
                total: {{ $testimonials->count() }},
                timer: null,
                startAutoplay() {
                    this.timer = setInterval(() => {
                        this.active = (this.active + 1) % this.total;
                    }, 5000);
                },
                stopAutoplay() {
                    clearInterval(this.timer);
                },
                next() {
                    this.stopAutoplay();
                    this.active = (this.active + 1) % this.total;
                    this.startAutoplay();
                },
                prev() {
                    this.stopAutoplay();
                    this.active = (this.active - 1 + this.total) % this.total;
                    this.startAutoplay();
                },
                goTo(i) {
                    this.stopAutoplay();
                    this.active = i;
                    this.startAutoplay();
                }
            }"
                x-init="startAutoplay()"
                @mouseenter="stopAutoplay()"
                @mouseleave="startAutoplay()"
                class="relative">
                {{-- Giant quote watermark --}}
                <div class="absolute -top-8 left-1/2 -translate-x-1/2 text-9xl font-serif select-none pointer-events-none z-0 leading-none"
                    style="color: rgba(184,150,46,0.15)">"</div>

                {{-- Slides --}}
                <div class="relative overflow-hidden">
                    <div class="flex transition-transform duration-700 ease-in-out"
                        :style="`transform: translateX(-${active * 100}%)`">

                        @foreach ($testimonials as $index => $t)
                        <div class="w-full flex-shrink-0 px-4 sm:px-16 md:px-28 lg:px-48">
                            <div class="rounded-3xl p-8 md:p-12 text-center relative z-10 border border-white/10"
                                style="background: rgba(255,255,255,0.07); backdrop-filter: blur(12px)">

                                {{-- Stars --}}
                                <div class="flex justify-center gap-1.5 mb-6">
                                    @for ($i = 0; $i < 5; $i++)
                                        <svg class="w-5 h-5 transition-opacity {{ $i < $t->rating ? 'opacity-100' : 'opacity-20' }}"
                                        viewBox="0 0 20 20" fill="{{ $i < $t->rating ? '#b8962e' : '#ffffff' }}">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        @endfor
                                </div>

                                {{-- Quote text --}}
                                <p class="text-white/90 text-lg md:text-xl leading-relaxed italic font-light mb-8">
                                    "{{ $t->content }}"
                                </p>

                                {{-- Divider --}}
                                <div class="w-12 h-px mx-auto mb-6" style="background: rgba(184,150,46,0.5)"></div>

                                {{-- Author --}}
                                <div class="flex items-center justify-center gap-4">
                                    <div class="relative flex-shrink-0">
                                        <img src="{{ $t->photo ? Storage::url($t->photo) : asset('img/testimonial-1.jpg') }}"
                                            loading="lazy" class="w-14 h-14 rounded-full object-cover border-2 border-white/20"
                                            alt="{{ $t->name }}">
                                        {{-- Verified badge --}}
                                        <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center"
                                            style="background: #b8962e; border: 2px solid #1a1a2e">
                                            <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="text-left">
                                        <p class="font-bold text-white text-sm">{{ $t->name }}</p>
                                        @if ($t->location)
                                        <p class="text-white/40 text-xs mt-0.5 flex items-center gap-1">
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ $t->location }}
                                        </p>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>

                {{-- Prev Arrow --}}
                <button @click="prev()"
                    class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-0 sm:-translate-x-2 w-10 h-10 rounded-full flex items-center justify-center text-white transition-all duration-200 border border-white/20 hover:border-white/50 hover:bg-white/10 focus:outline-none z-20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                {{-- Next Arrow --}}
                <button @click="next()"
                    class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-0 sm:translate-x-2 w-10 h-10 rounded-full flex items-center justify-center text-white transition-all duration-200 border border-white/20 hover:border-white/50 hover:bg-white/10 focus:outline-none z-20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                {{-- Dots + Counter --}}
                <div class="mt-10 flex flex-col items-center gap-3">

                    {{-- Dot indicators --}}
                    <div class="flex items-center gap-2">
                        @foreach ($testimonials as $i => $t)
                        <button
                            @click="goTo({{ $i }})"
                            :class="active === {{ $i }}
                            ? 'w-7 opacity-100 scale-100'
                            : 'w-2 opacity-30 hover:opacity-60 scale-75'"
                            class="h-2 rounded-full transition-all duration-400 focus:outline-none"
                            style="background: #b8962e"
                            :aria-label="`Go to testimonial {{ $i + 1 }}`">
                        </button>
                        @endforeach
                    </div>

                    {{-- Counter --}}
                    <p class="text-white/25 text-xs">
                        <span x-text="active + 1"></span>&thinsp;/&thinsp;{{ $testimonials->count() }}
                    </p>

                </div>

                {{-- Progress bar --}}
                <div class="mt-4 max-w-xs mx-auto h-0.5 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.1)">
                    <div class="h-full rounded-full transition-all duration-700"
                        style="background: #b8962e"
                        :style="`width: ${((active + 1) / total) * 100}%`">
                    </div>
                </div>

            </div>

            {{-- Privacy note --}}
            <p class="text-center text-white/20 text-xs mt-10 flex items-center justify-center gap-1.5">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Some names and details changed to protect privacy.
            </p>

        </div>
    </section>
    @endif
    {{-- ============================================================ --}}
    {{-- FINAL CTA --}}
    {{-- ============================================================ --}}
    <section class="final-cta-section py-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="final-cta-icon">🕌</div>
            <h2 class="final-cta-title">Ready to Begin Your Journey?</h2>
            <p class="final-cta-sub">Join thousands of Muslims already learning, growing, and connecting on Sallaamti. It's free to join.</p>
            <div class="flex gap-4 justify-center flex-wrap mt-8">
                @guest
                <a href="{{ route('register') }}" class="btn-gold btn-base text-lg px-10 py-4 font-semibold">
                    Register Free Now <i class="fa fa-arrow-right ml-2"></i>
                </a>
                <a href="{{ route('courses.index') }}" class="btn-outline-light btn-base text-lg px-10 py-4">
                    Browse Courses First
                </a>
                @else
                <a href="{{ route('dashboard') }}" class="btn-gold btn-base text-lg px-10 py-4 font-semibold">
                    Go to My Dashboard <i class="fa fa-arrow-right ml-2"></i>
                </a>
                @endguest
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- STYLES — all custom component CSS unchanged, + button base --}}
    {{-- ============================================================ --}}

    <script>
        // Animated counter
        function animateCounters() {
            document.querySelectorAll('.stat-number[data-target]').forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                if (target === 0) {
                    counter.textContent = '0';
                    return;
                }
                const step = target / (1500 / 16);
                let current = 0;
                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = Math.floor(current).toLocaleString();
                }, 16);
            });
        }
        const statsSection = document.querySelector('.stats-ticker');
        if (statsSection) {
            new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                    }
                });
            }, {
                threshold: 0.3
            }).observe(statsSection);
        }
    </script>

</x-guest-layout>