{{-- resources/views/index.blade.php --}}
<x-guest-layout>

    {{-- ============================================================ --}}
    {{-- HERO --}}
    {{-- ============================================================ --}}
    <section class="hero-section position-relative overflow-hidden">
        {{-- Background carousel --}}
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-inner">
                @foreach ($banners as $i => $banner)
                <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                    <div class="hero-bg-overlay"></div>
                    <img src="{{ str_starts_with($banner->image, 'img/') ? asset($banner->image) : Storage::url($banner->image) }}"
                        class="d-block w-100 hero-bg-img" alt="{{ $banner->title }}">
                </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        {{-- Hero Content --}}
        <div class="hero-content position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-7" data-aos="fade-right">
                        <span class="hero-eyebrow">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ</span>
                        <h1 class="hero-title">
                            Learn Quran.<br>
                            Find Your <span class="text-gold">Match.</span><br>
                            Build Community.
                        </h1>
                        <p class="hero-subtitle">
                            Join thousands of Muslims worldwide learning the Quran with expert teachers, finding halal matches, and growing together in faith.
                        </p>
                        <div class="d-flex gap-3 flex-wrap mt-4">
                            @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-gold btn-lg px-5 py-3 fw-semibold">
                                Go to Dashboard <i class="fa fa-arrow-right ms-2"></i>
                            </a>
                            @else
                            <a href="{{ route('register') }}" class="btn btn-gold btn-lg px-5 py-3 fw-semibold">
                                Join Free Today <i class="fa fa-arrow-right ms-2"></i>
                            </a>
                            <a href="{{ route('courses.index') }}" class="btn btn-outline-light btn-lg px-5 py-3">
                                Browse Courses
                            </a>
                            @endauth
                        </div>
                        <div class="hero-trust-bar d-flex gap-4 mt-4 flex-wrap">
                            <span class="trust-item"><i class="fa fa-check-circle text-gold me-1"></i>Free to Join</span>
                            <span class="trust-item"><i class="fa fa-check-circle text-gold me-1"></i>Expert Teachers</span>
                            <span class="trust-item"><i class="fa fa-check-circle text-gold me-1"></i>Global Access</span>
                            <span class="trust-item"><i class="fa fa-check-circle text-gold me-1"></i>Certified Courses</span>
                        </div>
                    </div>

                    <div class="col-lg-5 d-none d-lg-block" data-aos="fade-left" data-aos-delay="200">
                        <div class="hero-card-stack">
                            <div class="floating-card fc-1">
                                <span class="fc-icon">📖</span>
                                <div>
                                    <strong>Quran Learning</strong>
                                    <small>Nazrah · Tajweed · Translation</small>
                                </div>
                            </div>
                            <div class="floating-card fc-2">
                                <span class="fc-icon">🎥</span>
                                <div>
                                    <strong>Live Classes</strong>
                                    <small>One-to-One & Group</small>
                                </div>
                            </div>
                            <div class="floating-card fc-3">
                                <span class="fc-icon">💍</span>
                                <div>
                                    <strong>Nikah Platform</strong>
                                    <small>Verified · Guardian-mediated</small>
                                </div>
                            </div>
                            <div class="floating-card fc-4">
                                <span class="fc-icon">🎓</span>
                                <div>
                                    <strong>Certificates</strong>
                                    <small>Internationally recognized</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Carousel Indicators --}}
        <div class="carousel-indicators hero-indicators">
            @foreach ($banners as $i => $banner)
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $i }}"
                class="{{ $i === 0 ? 'active' : '' }}"></button>
            @endforeach
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- LIVE STATS TICKER --}}
    {{-- ============================================================ --}}
    <section class="stats-ticker bg-teal py-4">
        <div class="container">
            <div class="row g-4 text-center text-white">
                <div class="col-6 col-lg-3">
                    <div class="stat-item">
                        <span class="stat-number" data-target="{{ \App\Models\User::count() }}">0</span>
                        <span class="stat-label">Members Worldwide</span>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-item">
                        <span class="stat-number" data-target="{{ \App\Models\Enrollment::count() }}">0</span>
                        <span class="stat-label">Course Enrollments</span>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-item">
                        <span class="stat-number" data-target="{{ \App\Models\Certificate::count() }}">0</span>
                        <span class="stat-label">Certificates Issued</span>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-item">
                        <span class="stat-number" data-target="{{ \App\Models\NikahProfile::where('verification_status','verified')->count() }}">0</span>
                        <span class="stat-label">Verified Nikah Profiles</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- WHO IS THIS FOR --}}
    {{-- ============================================================ --}}
    <section class="py-5 bg-cream">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="section-eyebrow">For Every Muslim</span>
                <h2 class="section-title">Sallaamti is Built For You</h2>
                <p class="section-subtitle">Whether you're a parent, student, professional, or seeking a spouse — we have something meaningful for you.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="persona-card">
                        <div class="persona-icon">👨‍👩‍👧</div>
                        <h4>Parents & Families</h4>
                        <p>Enroll your children in structured Quran classes with qualified teachers. Get monthly progress reports and see your child's learning journey.</p>
                        <ul class="persona-list">
                            <li>Supervised live classes</li>
                            <li>Male & Female teachers available</li>
                            <li>Monthly progress reports</li>
                            <li>Parent dashboard access</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-teal-outline">Enroll Your Child →</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="persona-card persona-featured">
                        <div class="persona-badge">Most Popular</div>
                        <div class="persona-icon">🧑‍🎓</div>
                        <h4>Adults & Youth</h4>
                        <p>Learn at your own pace with our self-paced video courses, or join live classes with teachers from around the world. Earn certificates upon completion.</p>
                        <ul class="persona-list">
                            <li>Self-paced video courses</li>
                            <li>Live online classes</li>
                            <li>Quizzes & assessments</li>
                            <li>Completion certificates</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-gold">Start Learning Free →</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="persona-card">
                        <div class="persona-icon">🌍</div>
                        <h4>International Muslims</h4>
                        <p>Access Quran education and Islamic matrimonial services from anywhere in the world. Flexible timings for every timezone.</p>
                        <ul class="persona-list">
                            <li>Pakistan & international pricing</li>
                            <li>All timezones supported</li>
                            <li>Urdu & English classes</li>
                            <li>Global Nikah platform</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-teal-outline">Join From Abroad →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- OUR PROGRAMS --}}
    {{-- ============================================================ --}}
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="section-eyebrow">What We Offer</span>
                <h2 class="section-title">Our Programs & Services</h2>
            </div>

            {{-- Tab Navigation --}}
            <ul class="program-tabs nav justify-content-center mb-5" id="programTabs" role="tablist">
                <li class="nav-item"><button class="program-tab active" data-bs-toggle="tab" data-bs-target="#tab-quran">📖 Quran Courses</button></li>
                <li class="nav-item"><button class="program-tab" data-bs-toggle="tab" data-bs-target="#tab-live">🎥 Live Classes</button></li>
                <li class="nav-item"><button class="program-tab" data-bs-toggle="tab" data-bs-target="#tab-nikah">💍 Nikah</button></li>
                <li class="nav-item"><button class="program-tab" data-bs-toggle="tab" data-bs-target="#tab-skills">💻 Skills</button></li>
            </ul>

            <div class="tab-content">
                {{-- Quran Courses Tab --}}
                <div class="tab-pane fade show active" id="tab-quran">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-6">
                            <span class="section-eyebrow">Self-Paced Learning</span>
                            <h3 class="mb-3">Learn Quran at Your Own Pace</h3>
                            <p class="text-muted">Our structured Quran courses take you from basic Arabic alphabet all the way to deep Tafseer understanding. Each course has video lessons, interactive quizzes, and a certificate upon completion.</p>
                            <div class="learning-path my-4">
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
                            <div class="d-flex gap-3">
                                <a href="{{ route('courses.index') }}" class="btn btn-teal px-4">Browse All Courses</a>
                                @guest
                                <a href="{{ route('register') }}" class="btn btn-gold px-4">Join Free</a>
                                @endguest
                            </div>
                        </div>
                        <div class="col-lg-6">
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
                                <div class="course-preview-empty text-center p-4">
                                    <div class="text-5xl mb-2">📖</div>
                                    <p class="text-muted small">Courses launching soon</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Live Classes Tab --}}
                <div class="tab-pane fade" id="tab-live">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-6">
                            <span class="section-eyebrow">Online Live Sessions</span>
                            <h3 class="mb-3">Live Quran Classes with Expert Teachers</h3>
                            <p class="text-muted">One-to-one and group live classes via Zoom with experienced, qualified teachers. Fixed schedule, monthly subscription, and personal attention for every student.</p>
                            <div class="feature-list my-4">
                                @foreach (['5 classes per week, 30–45 mins each', 'One-to-one OR group class options', 'Male or Female teachers available', 'Weekly tests + Monthly progress reports', 'PKR 3,000–5,000/month | $25–$50 international'] as $f)
                                <div class="feature-item">
                                    <span class="feature-check">✅</span> {{ $f }}
                                </div>
                                @endforeach
                            </div>
                            <a href="{{ route('quran-live.index') }}" class="btn btn-teal px-4">View Live Courses</a>
                        </div>
                        <div class="col-lg-6">
                            <div class="live-class-showcase">
                                <div class="zoom-mock">
                                    <div class="zoom-header">
                                        <span class="zoom-dot red"></span>
                                        <span class="zoom-dot yellow"></span>
                                        <span class="zoom-dot green"></span>
                                        <span class="zoom-title">Sallaamti Live Class — Tajweed Level 2</span>
                                    </div>
                                    <div class="zoom-body">
                                        <div class="zoom-participant teacher">
                                            <div class="participant-avatar">👩‍🏫</div>
                                            <span>Teacher</span>
                                        </div>
                                        <div class="zoom-participant">
                                            <div class="participant-avatar">🧑‍🎓</div>
                                            <span>Student 1</span>
                                        </div>
                                        <div class="zoom-participant">
                                            <div class="participant-avatar">👦</div>
                                            <span>Student 2</span>
                                        </div>
                                        <div class="zoom-participant">
                                            <div class="participant-avatar">👧</div>
                                            <span>Student 3</span>
                                        </div>
                                    </div>
                                    <div class="zoom-footer">
                                        <span class="live-badge">🔴 LIVE</span>
                                        <span class="text-muted small">Sallaamti.com · Secure Session</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Nikah Tab --}}
                <div class="tab-pane fade" id="tab-nikah">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-6">
                            <span class="section-eyebrow">Islamic Matrimonial</span>
                            <h3 class="mb-3">Find a Halal Match — Properly</h3>
                            <p class="text-muted">Our Nikah platform is built differently. No casual swiping. Every profile is CNIC-verified by our team. Guardian-mediated contact only. Your photo is private until both sides accept.</p>
                            <div class="nikah-trust-list my-4">
                                @foreach ([['🔒', 'CNIC Verified', 'Every profile reviewed by our team'], ['👨‍👩‍👧', 'Guardian Mediated', 'Contact only through guardian channels'], ['📸', 'Private Photos', 'Photos visible only after mutual acceptance'], ['💳', 'Serious Members Only', 'Small verification fee filters out non-serious'], ['🌍', 'Global Platform', 'Muslims from Pakistan and worldwide']] as $item)
                                <div class="nikah-trust-item">
                                    <span class="nikah-icon">{{ $item[0] }}</span>
                                    <div>
                                        <strong>{{ $item[1] }}</strong>
                                        <small>{{ $item[2] }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <a href="{{ route('register') }}" class="btn btn-teal px-4">Create Your Profile</a>
                        </div>
                        <div class="col-lg-6">
                            <div class="nikah-preview-card">
                                <div class="nikah-card-header">
                                    <span class="verified-badge">✅ Verified Profile</span>
                                </div>
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
                                        <div class="match-bar" style="width:78%"></div>
                                        <span>78% Match</span>
                                    </div>
                                    <span class="private-photo">🔒 Photo Private</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Skills Tab --}}
                <div class="tab-pane fade" id="tab-skills">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-6">
                            <span class="section-eyebrow">Coming Soon</span>
                            <h3 class="mb-3">Skills Training for Dignified Livelihood</h3>
                            <p class="text-muted">We believe in empowering every Muslim with practical skills that lead to halal income and independence. Our skills training program is launching soon.</p>
                            <div class="skills-grid my-4">
                                @foreach (['💻 Computer Skills', '🎨 Graphic Design', '🌐 Web Development', '📱 Mobile Apps', '📈 Digital Marketing', '✂️ Tailoring', '⚡ Electrician', '🔧 Plumbing'] as $skill)
                                <span class="skill-tag">{{ $skill }}</span>
                                @endforeach
                            </div>
                            <a href="{{ route('volunteer.create') }}" class="btn btn-teal px-4">Join as Instructor Volunteer</a>
                        </div>
                        <div class="col-lg-6 text-center">
                            <div class="coming-soon-badge">
                                <div class="cs-icon">🚀</div>
                                <h4>Launching Soon</h4>
                                <p class="text-muted">Register now to be notified when Skills Training launches</p>
                                <a href="{{ route('register') }}" class="btn btn-gold mt-2">Get Early Access</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- HOW IT WORKS --}}
    {{-- ============================================================ --}}
    <section class="py-5 bg-teal-light">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="section-eyebrow">Simple Process</span>
                <h2 class="section-title">How to Get Started</h2>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="how-step">
                        <div class="how-number">1</div>
                        <div class="how-icon">📝</div>
                        <h5>Register Free</h5>
                        <p>Create your account in under 2 minutes. Name, email, phone — that's all we need to start.</p>
                    </div>
                </div>
                <div class="how-arrow d-none d-md-flex">→</div>
                <div class="col-md-4">
                    <div class="how-step how-step-featured">
                        <div class="how-number">2</div>
                        <div class="how-icon">🎯</div>
                        <h5>Choose Your Path</h5>
                        <p>Pick Quran courses, live classes, Nikah profile, or all three. Each module is tailored to your needs.</p>
                    </div>
                </div>
                <div class="how-arrow d-none d-md-flex">→</div>
                <div class="col-md-4">
                    <div class="how-step">
                        <div class="how-number">3</div>
                        <div class="how-icon">🌟</div>
                        <h5>Grow & Earn</h5>
                        <p>Learn Quran, earn certificates, find a match, and become part of a thriving global Muslim community.</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                @guest
                <a href="{{ route('register') }}" class="btn btn-gold btn-lg px-5 py-3 fw-semibold">
                    Start Your Journey — It's Free <i class="fa fa-arrow-right ms-2"></i>
                </a>
                @else
                <a href="{{ route('dashboard') }}" class="btn btn-gold btn-lg px-5 py-3 fw-semibold">
                    Go to Dashboard <i class="fa fa-arrow-right ms-2"></i>
                </a>
                @endguest
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- ABOUT SECTION --}}
    {{-- ============================================================ --}}
    <section class="container-fluid about py-5 bg-white">
        <div class="container py-5">
            <div class="row g-5 mb-5 align-items-center">
                <div class="col-xl-6">
                    <div class="row g-3">
                        <div class="col-6"><img src="{{ asset('img/about-1.jpg') }}" class="img-fluid rounded-3 h-100 object-fit-cover wow zoomIn" data-wow-delay="0.1s" alt=""></div>
                        <div class="col-6">
                            <img src="{{ asset('img/about-2.jpg') }}" class="img-fluid rounded-3 mb-3 wow zoomIn" data-wow-delay="0.2s" alt="">
                            <img src="{{ asset('img/about-3.jpg') }}" class="img-fluid rounded-3 wow zoomIn" data-wow-delay="0.3s" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <span class="section-eyebrow">About Sallaamti</span>
                    <h2 class="section-title mb-4">{{ setting('about_heading', 'Allah Helps Those Who Help Themselves') }}</h2>
                    <p>{{ setting('about_text', 'Sallaamti is dedicated to spreading the teachings of the Quran and Hadith, enlightening individuals with Islamic wisdom.') }}</p>
                    <div class="row g-4 mt-2">
                        <div class="col-sm-6">
                            <div class="vision-card">
                                <i class="fa fa-eye text-teal me-2"></i>
                                <div>
                                    <h6>Our Vision</h6>
                                    <p class="small text-muted mb-0">{{ setting('vision_text', 'A world where every Muslim lives by Quran and Sunnah.') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="vision-card">
                                <i class="fa fa-flag text-gold me-2"></i>
                                <div>
                                    <h6>Our Mission</h6>
                                    <p class="small text-muted mb-0">{{ setting('mission_text', 'Empowering communities through knowledge, compassion and action.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- QURAN SYLLABUS HIGHLIGHT --}}
    {{-- ============================================================ --}}
    <section class="py-5 bg-cream">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="section-eyebrow">Complete Curriculum</span>
                <h2 class="section-title">A Structured Path to Quranic Knowledge</h2>
                <p class="section-subtitle">Our 4-level syllabus designed by expert Islamic scholars takes you from basics to deep understanding</p>
            </div>
            <div class="row g-4">
                @foreach ([
                ['Level 1', 'Nazrah Quran', '6–12 months', '#0d6b6b', ['Arabic Alphabet (Makharij)', 'Zabar, Zair, Pesh', 'Basic Tajweed Rules', 'Fluency & Accuracy'], 'Student reads Quran fluently with correct pronunciation.'],
                ['Level 2', 'Tajweed', '6–8 months', '#b8962e', ['Makharij Detail', 'Sifaat (Letter Characteristics)', 'Noon Sakinah & Tanween', 'Qalqalah, Ghunna, Idgham'], 'Student recites Quran beautifully with proper Tajweed.'],
                ['Level 3', 'Translation', '6–8 months', '#1a5276', ['100 Common Quran Words', 'Word by Word Translation', 'Short Surah Translation', 'Easy Tafseer'], 'Student understands Quran with translation & simple Tafseer.'],
                ['Level 4', 'Arabic Grammar', '4–6 months', '#922b21', ['Arabic Alphabets Revision', 'Nouns, Verbs, Letters', 'Masculine, Feminine, Plural', 'Sentence Structure'], 'Student connects with Quranic Arabic language.']
                ] as $level)
                <div class="col-md-6 col-xl-3">
                    <div class="syllabus-card" style="--level-color: {{ $level[2] }}; border-top: 4px solid {{ $level[3] }}">
                        <div class="syllabus-header">
                            <span class="syllabus-badge" style="background: {{ $level[3] }}">{{ $level[0] }}</span>
                            <h5>{{ $level[1] }}</h5>
                            <small class="text-muted">Duration: {{ $level[2] }}</small>
                        </div>
                        <ul class="syllabus-topics">
                            @foreach ($level[4] as $topic)
                            <li>{{ $topic }}</li>
                            @endforeach
                        </ul>
                        <div class="syllabus-outcome">
                            <strong>Outcome:</strong> {{ $level[5] }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('courses.index') }}" class="btn btn-teal px-5">View All Courses</a>
                <a href="{{ route('register') }}" class="btn btn-gold px-5 ms-2">Register & Start</a>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- NIKAH AWARENESS --}}
    {{-- ============================================================ --}}
    <section class="py-5 nikah-awareness-section">
        <div class="container py-4">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <span class="section-eyebrow text-gold">Islamic Matrimonial</span>
                    <h2 class="section-title text-white">Marriage Is Half of Your Deen</h2>
                    <p class="text-white-50">
                        The Prophet ﷺ said: "Whoever Allah blesses with a righteous spouse, He has helped him with half of his religion." — Ibn Hibban
                    </p>
                    <p class="text-white-50">
                        Sallaamti's Nikah platform is built on Islamic values: verified profiles, guardian-mediated communication, and complete privacy protection. No casual browsing — every member is serious.
                    </p>
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
                    <a href="{{ route('register') }}" class="btn btn-gold px-5 py-3 mt-3 fw-semibold">Create Your Profile →</a>
                    @else
                    <a href="{{ route('nikah.show') }}" class="btn btn-gold px-5 py-3 mt-3 fw-semibold">Go to Nikah Profile →</a>
                    @endguest
                </div>
                <div class="col-lg-6">
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
                                <p class="small text-white-50 mb-0">{{ $step[2] }}</p>
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
    <section class="container-fluid activities py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="section-eyebrow">Activities</span>
                <h2 class="section-title">What We Do at Sallaamti</h2>
            </div>
            <div class="row g-4">
                @foreach ([
                ['fa-book-open', 'text-teal', '📖 Quran Learning', 'Learn with proper Tajweed, understanding, and authentic Islamic teachings for children and adults.', route('courses.index'), 'Explore Courses'],
                ['fa-laptop', 'text-gold', '🎧 Live Quran Classes', 'Join our live Quran classes and enhance your understanding with expert guidance.', route('quran-live.index'), 'View Live Classes'],
                ['fa-heart', 'text-danger', '💍 Sallaamti Nikah', 'A verified, guardian-mediated Islamic matrimonial platform built on trust and values.', route('nikah.create'), 'Create Profile'],
                ['fa-quran', 'text-teal', '💑 Couples Care', 'Guidance and support for building strong, healthy relationships and families.', '#', 'Coming Soon'],
                ['fa-desktop', 'text-gold', '💻 Digital Skills', 'Comprehensive training in computer skills, design, web development and trades.', '#', 'Coming Soon'],
                ['fa-users', 'text-teal', '👨‍👧‍👦 Parental Coaching', 'Professional guidance for effective parenting and raising the next generation of Muslims.', '#', 'Coming Soon'],
                ] as $activity)
                <div class="col-lg-6 col-xl-4">
                    <div class="activity-card">
                        <i class="fa {{ $activity[0] }} fa-3x {{ $activity[1] }} mb-3"></i>
                        <h5>{{ $activity[2] }}</h5>
                        <p class="text-muted small">{{ $activity[3] }}</p>
                        <a href="{{ $activity[4] }}"
                            class="btn btn-sm {{ $activity[4] === '#' ? 'btn-outline-secondary disabled' : 'btn-teal' }}">
                            {{ $activity[5] }}
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- TESTIMONIALS --}}
    {{-- ============================================================ --}}
    @if ($testimonials->isNotEmpty())
    <section class="container-fluid testimonial py-5 bg-cream">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="section-eyebrow">Testimonials</span>
                <h2 class="section-title">What Our Community Says</h2>
            </div>
            <div class="owl-carousel testimonial-carousel wow fadeIn" data-wow-delay="0.1s">
                @foreach ($testimonials as $t)
                <div class="testimonial-item">
                    <div class="testi-stars mb-2">
                        @for ($i = 0; $i < min($t->rating, 5); $i++)
                            <i class="fas fa-star text-gold"></i>
                            @endfor
                    </div>
                    <p class="testi-quote">"{{ $t->content }}"</p>
                    <div class="testi-author d-flex align-items-center mt-3">
                        <img src="{{ $t->photo ? Storage::url($t->photo) : asset('img/testimonial-1.jpg') }}"
                            class="testi-avatar" alt="{{ $t->name }}">
                        <div class="ms-3">
                            <strong>{{ $t->name }}</strong>
                            <small class="d-block text-muted">{{ $t->location }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <p class="text-muted text-center mt-3 small">
                <i class="fa fa-lock me-1"></i> Some names and details changed to protect privacy.
            </p>
        </div>
    </section>
    @endif

    {{-- ============================================================ --}}
    {{-- FINAL CTA --}}
    {{-- ============================================================ --}}
    <section class="final-cta-section py-5">
        <div class="container py-4 text-center">
            <div class="final-cta-icon">🕌</div>
            <h2 class="final-cta-title">Ready to Begin Your Journey?</h2>
            <p class="final-cta-sub">Join thousands of Muslims already learning, growing, and connecting on Sallaamti. It's free to join.</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap mt-4">
                @guest
                <a href="{{ route('register') }}" class="btn btn-gold btn-lg px-5 py-3 fw-semibold">
                    Register Free Now <i class="fa fa-arrow-right ms-2"></i>
                </a>
                <a href="{{ route('courses.index') }}" class="btn btn-outline-light btn-lg px-5 py-3">
                    Browse Courses First
                </a>
                @else
                <a href="{{ route('dashboard') }}" class="btn btn-gold btn-lg px-5 py-3 fw-semibold">
                    Go to My Dashboard <i class="fa fa-arrow-right ms-2"></i>
                </a>
                @endguest
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- STYLES --}}
    {{-- ============================================================ --}}
    <style>
        /* ===== TOKENS ===== */
        :root {
            --teal: #0d6b6b;
            --teal-dark: #095555;
            --teal-light: #e8f5f5;
            --gold: #b8962e;
            --gold-light: #fdf6e3;
            --cream: #fdfaf3;
            --text-dark: #1a1a2e;
            --text-muted: #6c757d;
        }

        /* ===== TYPOGRAPHY ===== */
        .section-eyebrow {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gold);
            display: block;
            margin-bottom: 8px;
        }

        .section-title {
            font-size: clamp(1.6rem, 3vw, 2.4rem);
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.2;
        }

        .section-subtitle {
            color: var(--text-muted);
            max-width: 600px;
            margin: 10px auto 0;
        }

        /* ===== BG ===== */
        .bg-cream {
            background: var(--cream);
        }

        .bg-teal {
            background: var(--teal);
        }

        .bg-teal-light {
            background: var(--teal-light);
        }

        .text-teal {
            color: var(--teal) !important;
        }

        .text-gold {
            color: var(--gold) !important;
        }

        /* ===== BUTTONS ===== */
        .btn-gold {
            background: var(--gold);
            border-color: var(--gold);
            color: #fff;
        }

        .btn-gold:hover {
            background: #9a7b25;
            border-color: #9a7b25;
            color: #fff;
        }

        .btn-teal {
            background: var(--teal);
            border-color: var(--teal);
            color: #fff;
        }

        .btn-teal:hover {
            background: var(--teal-dark);
            border-color: var(--teal-dark);
            color: #fff;
        }

        .btn-teal-outline {
            border: 2px solid var(--teal);
            color: var(--teal);
            background: transparent;
        }

        .btn-teal-outline:hover {
            background: var(--teal);
            color: #fff;
        }

        /* ===== HERO ===== */
        .hero-section {
            min-height: 90vh;
            position: relative;
        }

        .hero-bg-img {
            min-height: 90vh;
            object-fit: cover;
            object-position: center;
        }

        .hero-bg-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(13, 107, 107, 0.85) 0%, rgba(26, 26, 46, 0.75) 100%);
            z-index: 1;
        }

        .hero-content {
            z-index: 2;
        }

        .hero-eyebrow {
            font-family: 'Scheherazade New', serif;
            font-size: 1.1rem;
            color: var(--gold);
            display: block;
            margin-bottom: 12px;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .hero-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.1;
            margin-bottom: 20px;
        }

        .hero-subtitle {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.85);
            max-width: 500px;
            line-height: 1.7;
        }

        .hero-trust-bar .trust-item {
            color: rgba(255, 255, 255, 0.8);
            font-size: 13px;
        }

        .hero-indicators {
            bottom: 20px !important;
        }

        /* Floating Cards */
        .hero-card-stack {
            position: relative;
            padding: 20px;
        }

        .floating-card {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 12px 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            margin-bottom: 12px;
            backdrop-filter: blur(10px);
        }

        .floating-card:nth-child(even) {
            margin-left: 30px;
        }

        .fc-icon {
            font-size: 1.8rem;
        }

        .floating-card strong {
            display: block;
            font-size: 14px;
            color: var(--text-dark);
        }

        .floating-card small {
            color: var(--text-muted);
            font-size: 11px;
        }

        .fc-1 {
            animation: float1 3s ease-in-out infinite;
        }

        .fc-2 {
            animation: float2 3.5s ease-in-out infinite;
        }

        .fc-3 {
            animation: float1 4s ease-in-out infinite;
        }

        .fc-4 {
            animation: float2 3.2s ease-in-out infinite;
        }

        @keyframes float1 {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(-8px)
            }
        }

        @keyframes float2 {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(-5px)
            }
        }

        /* ===== STATS TICKER ===== */
        .stats-ticker {
            border-top: 3px solid var(--gold);
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .stat-number {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            line-height: 1;
            color: var(--gold);
        }

        .stat-label {
            font-size: 12px;
            letter-spacing: 1px;
            opacity: 0.8;
            margin-top: 4px;
            text-transform: uppercase;
        }

        /* ===== PERSONA CARDS ===== */
        .persona-card {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.06);
            height: 100%;
            border: 2px solid transparent;
            transition: all 0.3s;
        }

        .persona-card:hover {
            border-color: var(--teal);
            transform: translateY(-4px);
        }

        .persona-featured {
            border-color: var(--gold);
            background: linear-gradient(135deg, #fdf6e3 0%, #fff 100%);
            position: relative;
        }

        .persona-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--gold);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 16px;
            border-radius: 20px;
            letter-spacing: 1px;
            white-space: nowrap;
        }

        .persona-icon {
            font-size: 2.5rem;
            margin-bottom: 16px;
            display: block;
        }

        .persona-card h4 {
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 12px;
        }

        .persona-list {
            list-style: none;
            padding: 0;
            margin: 16px 0;
        }

        .persona-list li {
            padding: 4px 0;
            font-size: 14px;
            color: var(--text-muted);
        }

        .persona-list li::before {
            content: '✓ ';
            color: var(--teal);
            font-weight: 700;
        }

        /* ===== PROGRAM TABS ===== */
        .program-tabs {
            gap: 8px;
            border: none !important;
        }

        .program-tab {
            background: #f0f0f0;
            border: 2px solid transparent;
            border-radius: 30px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s;
        }

        .program-tab.active,
        .program-tab:hover {
            background: var(--teal);
            color: #fff;
            border-color: var(--teal);
        }

        /* Learning Path */
        .learning-path {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .learning-step {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--cream);
            border-radius: 10px;
            padding: 12px 16px;
        }

        .step-icon {
            font-size: 1.5rem;
        }

        .step-content strong {
            display: block;
            font-size: 14px;
        }

        .step-content small {
            color: var(--text-muted);
            font-size: 12px;
        }

        .step-arrow {
            text-align: center;
            color: var(--teal);
            font-size: 1.2rem;
            padding: 2px 0;
        }

        /* Course Preview Grid */
        .course-preview-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .course-preview-card {
            border-radius: 10px;
            overflow: hidden;
            background: var(--cream);
            border: 1px solid #eee;
        }

        .course-preview-card img {
            width: 100%;
            height: 80px;
            object-fit: cover;
        }

        .course-preview-placeholder {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .course-preview-info {
            padding: 8px;
        }

        .course-preview-info strong {
            display: block;
            font-size: 12px;
            color: var(--text-dark);
        }

        .course-preview-info small {
            color: var(--text-muted);
            font-size: 11px;
        }

        /* Zoom Mock */
        .zoom-mock {
            background: #1c1c1c;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .zoom-header {
            background: #2d2d2d;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .zoom-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .zoom-dot.red {
            background: #ff5f57;
        }

        .zoom-dot.yellow {
            background: #febc2e;
        }

        .zoom-dot.green {
            background: #28c840;
        }

        .zoom-title {
            color: #aaa;
            font-size: 11px;
            margin-left: 6px;
        }

        .zoom-body {
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .zoom-participant {
            background: #2d2d2d;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            color: #ccc;
            font-size: 12px;
        }

        .zoom-participant.teacher {
            border: 2px solid var(--teal);
        }

        .participant-avatar {
            font-size: 2rem;
            margin-bottom: 4px;
        }

        .zoom-footer {
            background: #2d2d2d;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .live-badge {
            background: #ff3b30;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
        }

        /* Feature List */
        .feature-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }

        /* Nikah Trust */
        .nikah-trust-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .nikah-trust-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nikah-icon {
            font-size: 1.5rem;
            width: 40px;
            text-align: center;
        }

        .nikah-trust-item strong {
            display: block;
            font-size: 14px;
        }

        .nikah-trust-item small {
            color: var(--text-muted);
            font-size: 12px;
        }

        .nikah-preview-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .nikah-card-header {
            background: var(--teal);
            padding: 12px 20px;
        }

        .verified-badge {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .nikah-card-body {
            padding: 20px;
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .nikah-avatar {
            font-size: 4rem;
        }

        .nikah-info h5 {
            margin-bottom: 4px;
        }

        .nikah-info p {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 2px;
        }

        .nikah-card-footer {
            padding: 12px 20px;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .match-score {
            flex: 1;
        }

        .match-bar {
            height: 6px;
            background: var(--teal);
            border-radius: 3px;
            margin-bottom: 2px;
        }

        .match-score span,
        .private-photo {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Skills */
        .skills-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .skill-tag {
            background: var(--teal-light);
            color: var(--teal);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }

        .coming-soon-badge {
            background: var(--cream);
            border: 2px dashed var(--gold);
            border-radius: 16px;
            padding: 40px;
            display: inline-block;
        }

        .cs-icon {
            font-size: 3rem;
            margin-bottom: 12px;
        }

        /* ===== HOW IT WORKS ===== */
        .how-step {
            text-align: center;
            padding: 32px 24px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.06);
            height: 100%;
            position: relative;
        }

        .how-step-featured {
            background: var(--teal);
            color: #fff;
        }

        .how-step-featured p {
            color: rgba(255, 255, 255, 0.8);
        }

        .how-number {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--gold);
            color: #fff;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        .how-icon {
            font-size: 2.5rem;
            margin: 8px 0 16px;
        }

        .how-arrow {
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--teal);
            padding: 0 10px;
        }

        /* ===== SYLLABUS ===== */
        .syllabus-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.06);
            height: 100%;
        }

        .syllabus-header {
            margin-bottom: 16px;
        }

        .syllabus-badge {
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 8px;
        }

        .syllabus-header h5 {
            font-weight: 700;
            margin-bottom: 2px;
        }

        .syllabus-topics {
            list-style: none;
            padding: 0;
            margin: 0 0 16px;
        }

        .syllabus-topics li {
            font-size: 13px;
            color: var(--text-muted);
            padding: 3px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .syllabus-topics li::before {
            content: '• ';
            color: var(--teal);
        }

        .syllabus-outcome {
            background: var(--cream);
            border-radius: 8px;
            padding: 10px;
            font-size: 12px;
            color: var(--text-dark);
        }

        /* ===== NIKAH AWARENESS ===== */
        .nikah-awareness-section {
            background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);
            position: relative;
            overflow: hidden;
        }

        .nikah-awareness-section::before {
            content: '❖';
            position: absolute;
            right: -2rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20rem;
            opacity: 0.03;
            color: #fff;
        }

        .nikah-stats-row {
            display: flex;
            gap: 24px;
            margin-top: 24px;
            flex-wrap: wrap;
        }

        .nikah-stat {
            text-align: center;
        }

        .nikah-stat-n {
            display: block;
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--gold);
        }

        .nikah-stat span:last-child {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
        }

        .nikah-process-steps {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .nikah-process-step {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .process-num {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--gold);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }

        .nikah-process-step strong {
            color: #fff;
            display: block;
        }

        /* ===== ACTIVITIES ===== */
        .activity-card {
            background: var(--cream);
            border-radius: 12px;
            padding: 28px;
            height: 100%;
            text-align: center;
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .activity-card:hover {
            border-color: var(--teal);
            background: #fff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        /* ===== TESTIMONIALS ===== */
        .testi-quote {
            font-size: 15px;
            color: var(--text-dark);
            font-style: italic;
            line-height: 1.7;
        }

        .testi-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
        }

        .text-gold {
            color: var(--gold) !important;
        }

        /* ===== FINAL CTA ===== */
        .final-cta-section {
            background: linear-gradient(135deg, #b8962e 0%, #0d6b6b 100%);
            padding: 80px 0;
        }

        .final-cta-icon {
            font-size: 3rem;
            margin-bottom: 16px;
        }

        .final-cta-title {
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            font-weight: 800;
            color: #fff;
            margin-bottom: 12px;
        }

        .final-cta-sub {
            color: rgba(255, 255, 255, 0.8);
            max-width: 500px;
            margin: 0 auto;
            font-size: 1.05rem;
        }

        /* ===== VISION CARDS ===== */
        .vision-card {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
            background: var(--cream);
            border-radius: 10px;
        }

        /* ===== MOBILE ===== */
        @media (max-width: 767px) {
            .hero-section {
                min-height: 70vh;
            }

            .hero-bg-img {
                min-height: 70vh;
            }

            .stats-ticker .stat-number {
                font-size: 1.8rem;
            }

            .how-arrow {
                display: none !important;
            }

            .nikah-stats-row {
                gap: 12px;
            }

            .nikah-stat-n {
                font-size: 1.3rem;
            }

            .final-cta-section {
                padding: 50px 0;
            }
        }
    </style>

    {{-- ===== SCRIPTS ===== --}}
    <script>
        // Animated counter
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number[data-target]');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                if (target === 0) {
                    counter.textContent = '0';
                    return;
                }
                const duration = 1500;
                const step = target / (duration / 16);
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

        // Trigger when stats section enters viewport
        const statsSection = document.querySelector('.stats-ticker');
        if (statsSection) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.3
            });
            observer.observe(statsSection);
        }
    </script>

</x-guest-layout>