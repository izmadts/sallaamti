{{-- resources/views/index.blade.php --}}
<x-guest-layout>

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
            <img src="{{ str_starts_with($banner->image, 'img/') ? asset($banner->image) : Storage::url($banner->image) }}"
                class="block w-full hero-bg-img" alt="{{ $banner->title }}">
        </div>
        @endforeach

        {{-- Hero Content --}}
        <div class="hero-content absolute inset-0 flex items-center z-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                <div class="grid lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-7">
                        <span class="hero-eyebrow">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ</span>
                        <h1 class="hero-title">
                            Learn Quran.<br>
                            Find Your <span class="text-gold">Match.</span><br>
                            Build Community.
                        </h1>
                        <p class="hero-subtitle">
                            Join thousands of Muslims worldwide learning the Quran with expert teachers, finding halal matches, and growing together in faith.
                        </p>
                        <div class="flex gap-3 flex-wrap mt-6">
                            @auth
                            <a href="{{ route('dashboard') }}" class="btn-gold btn-base text-lg px-8 py-3 font-semibold">
                                Go to Dashboard <i class="fa fa-arrow-right ml-2"></i>
                            </a>
                            @else
                            <a href="{{ route('register') }}" class="btn-gold btn-base text-lg px-8 py-3 font-semibold">
                                Join Free Today <i class="fa fa-arrow-right ml-2"></i>
                            </a>
                            <a href="{{ route('courses.index') }}" class="btn-outline-light btn-base text-lg px-8 py-3">
                                Browse Courses
                            </a>
                            @endauth
                        </div>
                        <div class="hero-trust-bar flex gap-4 mt-4 flex-wrap">
                            <span class="trust-item"><i class="fa fa-check-circle text-gold mr-1"></i>Free to Join</span>
                            <span class="trust-item"><i class="fa fa-check-circle text-gold mr-1"></i>Expert Teachers</span>
                            <span class="trust-item"><i class="fa fa-check-circle text-gold mr-1"></i>Global Access</span>
                            <span class="trust-item"><i class="fa fa-check-circle text-gold mr-1"></i>Certified Courses</span>
                        </div>
                    </div>

                    <div class="lg:col-span-5 hidden lg:block">
                        <div class="hero-card-stack">
                            <div class="floating-card fc-1">
                                <span class="fc-icon">📖</span>
                                <div><strong>Quran Learning</strong><small>Nazrah · Tajweed · Translation</small></div>
                            </div>
                            <div class="floating-card fc-2">
                                <span class="fc-icon">🎥</span>
                                <div><strong>Live Classes</strong><small>One-to-One & Group</small></div>
                            </div>
                            <div class="floating-card fc-3">
                                <span class="fc-icon">💍</span>
                                <div><strong>Nikah Platform</strong><small>Verified · Guardian-mediated</small></div>
                            </div>
                            <div class="floating-card fc-4">
                                <span class="fc-icon">🎓</span>
                                <div><strong>Certificates</strong><small>Internationally recognized</small></div>
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
                    <span class="stat-label">Members Worldwide</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-target="{{ \App\Models\Enrollment::count() }}">0</span>
                    <span class="stat-label">Course Enrollments</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-target="{{ \App\Models\Certificate::count() }}">0</span>
                    <span class="stat-label">Certificates Issued</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-target="{{ \App\Models\NikahProfile::where('verification_status','verified')->count() }}">0</span>
                    <span class="stat-label">Verified Nikah Profiles</span>
                </div>
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
                                <img src="{{ Storage::url($course->thumbnail) }}" alt="{{ $course->title }}">
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
                    <img src="{{ asset('img/about-1.jpg') }}" class="max-w-full rounded-2xl h-full object-cover wow zoomIn" data-wow-delay="0.1s" alt="">
                    <div class="flex flex-col gap-3">
                        <img src="{{ asset('img/about-2.jpg') }}" class="max-w-full rounded-2xl wow zoomIn" data-wow-delay="0.2s" alt="">
                        <img src="{{ asset('img/about-3.jpg') }}" class="max-w-full rounded-2xl wow zoomIn" data-wow-delay="0.3s" alt="">
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
            </div>
            <div class="grid lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ([
                ['fa-book-open', 'text-teal', '📖 Quran Learning', 'Learn with proper Tajweed, understanding, and authentic Islamic teachings for children and adults.', route('courses.index'), 'Explore Courses'],
                ['fa-laptop', 'text-gold', '🎧 Live Quran Classes', 'Join our live Quran classes and enhance your understanding with expert guidance.', route('quran-live.index'), 'View Live Classes'],
                ['fa-heart', 'text-red-500', '💍 Sallaamti Nikah', 'A verified, guardian-mediated Islamic matrimonial platform built on trust and values.', route('nikah.create'), 'Create Profile'],
                ['fa-quran', 'text-teal', '💑 Couples Care', 'Guidance and support for building strong, healthy relationships and families.', '#', 'Coming Soon'],
                ['fa-desktop', 'text-gold', '💻 Digital Skills', 'Comprehensive training in computer skills, design, web development and trades.', '#', 'Coming Soon'],
                ['fa-users', 'text-teal', '👨‍👧‍👦 Parental Coaching', 'Professional guidance for effective parenting and raising the next generation of Muslims.', '#', 'Coming Soon'],
                ] as $activity)
                <div class="activity-card">
                    <i class="fa {{ $activity[0] }} fa-3x {{ $activity[1] }} mb-4"></i>
                    <h5 class="font-bold text-lg mb-2">{{ $activity[2] }}</h5>
                    <p class="text-gray-500 text-sm mb-4">{{ $activity[3] }}</p>
                    <a href="{{ $activity[4] }}"
                        class="{{ $activity[4] === '#' ? 'btn-disabled btn-base text-sm px-4' : 'btn-teal btn-base text-sm px-4' }}">
                        {{ $activity[5] }}
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
    <section class="py-20 bg-cream">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="section-eyebrow">Testimonials</span>
                <h2 class="section-title">What Our Community Says</h2>
            </div>
            <div class="owl-carousel testimonial-carousel wow fadeIn" data-wow-delay="0.1s">
                @foreach ($testimonials as $t)
                <div class="testimonial-item">
                    <div class="testi-stars mb-2">
                        @for ($i = 0; $i < min($t->rating, 5); $i++)<i class="fas fa-star text-gold"></i>@endfor
                    </div>
                    <p class="testi-quote">"{!! $t->content !!}"</p>
                    <div class="testi-author flex items-center mt-4">
                        <img src="{{ $t->photo ? Storage::url($t->photo) : asset('img/testimonial-1.jpg') }}"
                            class="testi-avatar" alt="{{ $t->name }}">
                        <div class="ml-3">
                            <strong>{{ $t->name }}</strong>
                            <small class="block text-gray-500">{{ $t->location }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <p class="text-gray-400 text-center mt-4 text-sm">
                <i class="fa fa-lock mr-1"></i> Some names and details changed to protect privacy.
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