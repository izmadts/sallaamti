{{-- resources/views/activities.blade.php --}}
<x-guest-layout>

    {{-- ============================================================ --}}
    {{-- PAGE HERO --}}
    {{-- ============================================================ --}}
    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 320px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="absolute inset-0 opacity-5 text-white flex items-center justify-center" style="font-size: 20rem; pointer-events: none; overflow: hidden;">❖</div>
        <div class="max-w-7xl mx-auto px-4 py-20 relative z-10 text-center w-full">
            <span class="section-eyebrow">What We Do</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-2 mb-4">Our Activities</h1>
            <p class="text-white/70 text-lg max-w-2xl mx-auto">
                From Quran education to matrimonial services, skills training to family counseling — discover everything Sallaamti does to serve the Muslim Ummah.
            </p>
            <nav class="flex justify-center gap-2 mt-6 text-sm text-white/50">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a>
                <span>/</span>
                <span class="text-white">Activities</span>
            </nav>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- ACTIVITY TABS NAVIGATION --}}
    {{-- ============================================================ --}}
    <section class="bg-white sticky top-0 z-40 border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex overflow-x-auto gap-1 py-3 scrollbar-hide">
                @foreach ([
                ['quran', '📖', 'Quran Learning'],
                ['live', '🎥', 'Live Classes'],
                ['nikah', '💍', 'Nikah'],
                ['skills', '💻', 'Skills'],
                ['rozgar', '🛠️', 'Employment'],
                ['family', '👨‍👩‍👧', 'Family'],
                ['volunteer', '🤝', 'Volunteer'],
                ['donate', '💝', 'Donate'],
                ] as $tab)
                <a href="#{{ $tab[0] }}"
                    class="flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all duration-200 border-2 border-transparent hover:border-teal-600 hover:text-teal-700 text-gray-600 flex-shrink-0">
                    {{ $tab[1] }} {{ $tab[2] }}
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- 1. QURAN LEARNING --}}
    {{-- ============================================================ --}}
    <section id="quran" class="py-20 bg-cream">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12 items-center mb-16">
                <div class="wow fadeInLeft" data-wow-delay="0.1s">
                    <span class="section-eyebrow">Activity 01</span>
                    <h2 class="section-title mb-4">📖 Quran Learning (Taaleem)</h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Our flagship program — a structured, 4-level Quran education system designed by Islamic scholars for Muslims of all ages across the globe. Whether you're starting from scratch or deepening your understanding, we have a course for you.
                    </p>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Each level comes with video lessons, lesson quizzes, a final course quiz, and a <strong>Sallaamti Certificate of Completion</strong> — verified with a unique QR code.
                    </p>

                    {{-- Level Cards --}}
                    <div class="grid grid-cols-2 gap-3 mb-8">
                        @foreach ([
                        ['Level 1', 'Nazrah Quran', '6–12 months', '#0d6b6b'],
                        ['Level 2', 'Tajweed', '6–8 months', '#b8962e'],
                        ['Level 3', 'Translation', '6–8 months', '#1a5276'],
                        ['Level 4', 'Arabic Grammar', '4–6 months', '#922b21'],
                        ] as $lvl)
                        <div class="p-3 rounded-xl border-l-4 bg-white shadow-sm" style="border-color: {{ $lvl[3] }}">
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full text-white" style="background: {{ $lvl[3] }}">{{ $lvl[0] }}</span>
                            <p class="font-semibold text-gray-800 mt-2 mb-0 text-sm">{{ $lvl[1] }}</p>
                            <p class="text-xs text-gray-500 mb-0">{{ $lvl[2] }}</p>
                        </div>
                        @endforeach
                    </div>

                    <div class="flex gap-3 flex-wrap">
                        <a href="{{ route('courses.index') }}" class="btn-base btn-teal px-6 py-2">Browse Courses</a>
                        @guest
                        <a href="{{ route('register') }}" class="btn-base btn-gold px-6 py-2">Join Free</a>
                        @endguest
                    </div>
                </div>

                <div class="wow fadeInRight" data-wow-delay="0.2s">
                    <div class="grid grid-cols-2 gap-4">
                        <img src="{{ asset('img/quran-1.jpg') }}" class="w-full h-48 object-cover rounded-2xl" alt="">
                        <img src="{{ asset('img/quran-2.jpg') }}" class="w-full h-48 object-cover rounded-2xl mt-8" alt="">
                        <img src="{{ asset('img/quran-3.jpg') }}" class="w-full h-48 object-cover rounded-2xl" alt="">
                        <div class="rounded-2xl flex flex-col items-center justify-center text-center p-4 wow-card" style="background: var(--teal)">
                            <div class="text-4xl font-extrabold text-white">{{ \App\Models\Course::where('is_published',true)->count() }}+</div>
                            <div class="text-white/70 text-sm mt-1">Published Courses</div>
                            <div class="text-4xl mt-3">🎓</div>
                            <div class="text-white/70 text-sm mt-1">{{ \App\Models\Certificate::count() }}+ Certificates</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Features row --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ([
                ['🎬', 'Video Lessons', 'Pre-recorded HD lessons you can watch anytime, anywhere at your own pace.'],
                ['📝', 'Lesson Quizzes', 'After each lesson and at course end — with automatic grade tracking.'],
                ['🏆', 'Certificates', 'QR-verified completion certificates for each course level.'],
                ['📊', 'Progress Tracking', 'Your personal learning dashboard shows exactly where you are.'],
                ] as $feat)
                <div class="bg-white rounded-xl p-5 shadow-sm text-center border border-gray-50 hover:shadow-md transition-shadow">
                    <div class="text-3xl mb-3">{{ $feat[0] }}</div>
                    <h6 class="font-bold text-gray-800 mb-2">{{ $feat[1] }}</h6>
                    <p class="text-gray-500 text-sm mb-0">{{ $feat[2] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- 2. LIVE QURAN CLASSES --}}
    {{-- ============================================================ --}}
    <section id="live" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12 items-center">

                {{-- Zoom Mock --}}
                <div class="wow fadeInLeft" data-wow-delay="0.1s">
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

                    {{-- Pricing --}}
                    <div class="grid grid-cols-2 gap-4 mt-6">
                        <div class="rounded-xl p-5 text-center border-2" style="border-color: var(--teal); background: var(--teal-light)">
                            <div class="text-xs font-bold uppercase tracking-wider mb-2" style="color: var(--teal)">Pakistan</div>
                            <div class="text-2xl font-extrabold" style="color: var(--teal)">PKR 3,000</div>
                            <div class="text-xs text-gray-500 mt-1">– 5,000 / month</div>
                            <div class="text-xs text-gray-500 mt-2">Level 1–4</div>
                        </div>
                        <div class="rounded-xl p-5 text-center border-2" style="border-color: var(--gold); background: #fdf6e3">
                            <div class="text-xs font-bold uppercase tracking-wider mb-2" style="color: var(--gold)">International</div>
                            <div class="text-2xl font-extrabold" style="color: var(--gold)">$25</div>
                            <div class="text-xs text-gray-500 mt-1">– $50 / month</div>
                            <div class="text-xs text-gray-500 mt-2">Level 1–4</div>
                        </div>
                    </div>
                    <p class="text-center text-xs text-gray-400 mt-2">Special discount for siblings & group registrations</p>
                </div>

                <div class="wow fadeInRight" data-wow-delay="0.2s">
                    <span class="section-eyebrow">Activity 02</span>
                    <h2 class="section-title mb-4">🎥 Live Online Quran Classes</h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        One-to-one and small group live Quran classes with qualified, experienced teachers via Zoom. Structured monthly curriculum, flexible timings for every timezone, and personal attention for every student.
                    </p>

                    {{-- Class Structure --}}
                    <div class="rounded-xl p-5 mb-6" style="background: var(--cream)">
                        <h6 class="font-bold text-gray-800 mb-3">📋 Class Structure</h6>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ([
                            ['📅', '5 classes per week'],
                            ['⏱️', '30–45 mins per class'],
                            ['👤', 'One-to-one & group'],
                            ['📝', 'Weekly tests'],
                            ['📊', 'Monthly progress report'],
                            ['🌍', 'All timezones'],
                            ] as $cs)
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <span>{{ $cs[0] }}</span> {{ $cs[1] }}
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Assessment --}}
                    <div class="rounded-xl p-5 mb-6 border-l-4" style="background: #fff; border-color: var(--gold)">
                        <h6 class="font-bold text-gray-800 mb-3">📊 Assessment & Examinations</h6>
                        <div class="flex flex-wrap gap-2">
                            @foreach (['Weekly Quiz', 'Monthly Test', 'Quarterly Exam', 'Annual Final Exam', 'Certificate Awarded'] as $a)
                            <span class="text-xs font-semibold px-3 py-1 rounded-full" style="background: #fdf6e3; color: var(--gold)">{{ $a }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex gap-3 flex-wrap">
                        <a href="{{ route('quran-live.index') }}" class="btn-base btn-teal px-6 py-2">Browse Live Courses</a>
                        <a href="{{ route('register') }}" class="btn-base btn-gold px-6 py-2">Apply for Admission</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- 3. NIKAH --}}
    {{-- ============================================================ --}}
    <section id="nikah" class="py-20 nikah-awareness-section">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="wow fadeInLeft" data-wow-delay="0.1s">
                    <span class="section-eyebrow text-gold">Activity 03</span>
                    <h2 class="section-title text-white mb-4">💍 Sallaamti Nikah Platform</h2>
                    <p class="text-white/70 mb-6 leading-relaxed">
                        The Prophet ﷺ said: "When a man marries, he has fulfilled half of his religion." — Bayhaqi. Sallaamti's Nikah platform is built on this understanding — a serious, values-driven matrimonial service for Muslims who want to do it right.
                    </p>
                    <p class="text-white/70 mb-8 leading-relaxed">
                        Every profile is CNIC-verified. Photos are private until mutual acceptance. All contact is guardian-mediated. A small mandatory fee ensures only serious candidates join.
                    </p>

                    <div class="grid grid-cols-3 gap-4 mb-8">
                        <div class="text-center">
                            <div class="text-3xl font-extrabold" style="color: var(--gold)">{{ \App\Models\NikahProfile::where('verification_status','verified')->count() }}+</div>
                            <div class="text-white/60 text-xs mt-1">Verified Profiles</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-extrabold" style="color: var(--gold)">100%</div>
                            <div class="text-white/60 text-xs mt-1">CNIC Verified</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-extrabold" style="color: var(--gold)">🔒</div>
                            <div class="text-white/60 text-xs mt-1">Private Photos</div>
                        </div>
                    </div>

                    <a href="{{ route('register') }}" class="btn-base btn-gold px-8 py-3 font-semibold">Create Your Profile →</a>
                </div>

                <div class="wow fadeInRight" data-wow-delay="0.2s">
                    <div class="nikah-process-steps">
                        @foreach ([
                        ['1', 'Register & Create Profile', 'Fill your profile with personal, educational and family details. Guardian info is required.'],
                        ['2', 'Pay Verification Fee', 'A mandatory fee ensures every member is serious. No casual browsers.'],
                        ['3', 'Admin CNIC Verification', 'Our team verifies your CNIC and profile before it becomes visible.'],
                        ['4', 'Browse Verified Matches', 'Search by age, city, sect, education. See match percentage based on your preferences.'],
                        ['5', 'Express Interest', 'Send an interest request. Both sides must accept before contact is revealed.'],
                        ['6', 'Guardian Contact', 'After mutual acceptance, guardian contact details are shared for family communication.'],
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
    {{-- 4, 5, 6 — UPCOMING ACTIVITIES --}}
    {{-- ============================================================ --}}
    <section class="py-20 bg-cream">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="section-eyebrow">Coming Soon</span>
                <h2 class="section-title">More Activities Launching Soon</h2>
                <p class="section-subtitle">We're building these programs with the same care and Islamic values as our existing modules.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">

                {{-- Skills --}}
                <div id="skills" class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <div class="h-2" style="background: var(--gold)"></div>
                    <div class="p-6">
                        <div class="text-4xl mb-4">💻</div>
                        <div class="inline-block text-xs font-bold px-3 py-1 rounded-full mb-3" style="background: #fdf6e3; color: var(--gold)">Activity 04 · Coming Soon</div>
                        <h4 class="font-bold text-xl text-gray-800 mb-3">Sallaamti Skills Training</h4>
                        <p class="text-gray-500 text-sm mb-5 leading-relaxed">
                            Comprehensive vocational and digital skills training — computer, graphic design, web development, tailoring, electrical work and more — giving Muslims the tools for halal income.
                        </p>
                        <div class="flex flex-wrap gap-2 mb-5">
                            @foreach (['💻 Computer', '🎨 Design', '🌐 Web Dev', '✂️ Tailoring', '⚡ Electrician', '📱 Mobile'] as $s)
                            <span class="text-xs px-2 py-1 rounded-full" style="background: var(--teal-light); color: var(--teal)">{{ $s }}</span>
                            @endforeach
                        </div>
                        <a href="{{ route('volunteer.create') }}" class="btn-base btn-gold text-sm px-5 py-2 w-full text-center block">Volunteer as Instructor</a>
                    </div>
                </div>

                {{-- Employment --}}
                <div id="rozgar" class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <div class="h-2" style="background: #16a34a"></div>
                    <div class="p-6">
                        <div class="text-4xl mb-4">🛠️</div>
                        <div class="inline-block text-xs font-bold px-3 py-1 rounded-full mb-3" style="background: #f0fdf4; color: #16a34a">Activity 05 · Coming Soon</div>
                        <h4 class="font-bold text-xl text-gray-800 mb-3">Sallaamti Rozgar (Employment)</h4>
                        <p class="text-gray-500 text-sm mb-5 leading-relaxed">
                            Helping needy families, orphans and deserving Muslims start dignified small businesses and find lawful livelihoods — with guidance, mentorship and micro-financing support.
                        </p>
                        <div class="rounded-xl p-4 mb-5 text-sm" style="background: #f0fdf4">
                            <p class="text-gray-600 mb-2 font-semibold">We will help with:</p>
                            @foreach (['Business planning & mentorship', 'Micro-financing guidance', 'Skills development', 'Job placement support'] as $r)
                            <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                                <i class="fa fa-check text-green-600"></i> {{ $r }}
                            </div>
                            @endforeach
                        </div>
                        <a href="{{ route('volunteer.create') }}" class="btn-base text-sm px-5 py-2 w-full text-center block text-white font-medium" style="background: #16a34a; border-color: #16a34a; border-radius: 0.5rem;">Register Interest</a>
                    </div>
                </div>

                {{-- Family / Parenting --}}
                <div id="family" class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <div class="h-2" style="background: #7d3c98"></div>
                    <div class="p-6">
                        <div class="text-4xl mb-4">👨‍👩‍👧</div>
                        <div class="inline-block text-xs font-bold px-3 py-1 rounded-full mb-3" style="background: #f5eef8; color: #7d3c98">Activity 06 · Coming Soon</div>
                        <h4 class="font-bold text-xl text-gray-800 mb-3">Sallaamti Waldain & Family</h4>
                        <p class="text-gray-500 text-sm mb-5 leading-relaxed">
                            Professional Islamic parenting coaching and family counseling — helping parents build strong, values-driven homes and raise the next generation of practicing Muslims.
                        </p>
                        <div class="rounded-xl p-4 mb-5 text-sm" style="background: #f5eef8">
                            <p class="text-gray-600 mb-2 font-semibold">Programs include:</p>
                            @foreach (['Parenting workshops', 'Marriage counseling', 'Family conflict resolution', 'Islamic parenting guides'] as $f)
                            <div class="flex items-center gap-2 text-gray-500 text-xs mb-1">
                                <i class="fa fa-check" style="color: #7d3c98"></i> {{ $f }}
                            </div>
                            @endforeach
                        </div>
                        <a href="{{ route('volunteer.create') }}" class="btn-base text-sm px-5 py-2 w-full text-center block text-white font-medium" style="background: #7d3c98; border-color: #7d3c98; border-radius: 0.5rem;">Register Interest</a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- VOLUNTEER --}}
    {{-- ============================================================ --}}
    <section id="volunteer" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="wow fadeInLeft" data-wow-delay="0.1s">
                    <span class="section-eyebrow">Activity 07</span>
                    <h2 class="section-title mb-4">🤝 Volunteer with Sallaamti</h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        "The best of people are those who are most beneficial to people." — Prophet Muhammad ﷺ. Whether you're a teacher, developer, counselor, designer or simply passionate about serving the Ummah — your skills can create a lasting impact.
                    </p>
                    <div class="grid grid-cols-2 gap-3 mb-8">
                        @foreach ([
                        ['👩‍🏫', 'Quran Teachers', 'Teach live online Quran classes to students globally'],
                        ['💻', 'Tech Volunteers', 'Help build and improve the Sallaamti platform'],
                        ['🤲', 'Counselors', 'Provide family and marriage guidance to those in need'],
                        ['🎨', 'Creative Team', 'Design graphics, videos and content for our programs'],
                        ['📢', 'Outreach', 'Help spread the word about Sallaamti in your community'],
                        ['📦', 'Fundraisers', 'Help raise funds for our educational and charity programs'],
                        ] as $vol)
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 hover:bg-teal-50 transition-colors group">
                            <span class="text-2xl flex-shrink-0">{{ $vol[0] }}</span>
                            <div>
                                <strong class="text-sm text-gray-800 group-hover:text-teal-700 transition-colors">{{ $vol[1] }}</strong>
                                <p class="text-xs text-gray-500 mb-0 mt-0.5">{{ $vol[2] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('volunteer.create') }}" class="btn-base btn-teal px-8 py-3 font-semibold">Apply as Volunteer →</a>
                </div>

                <div class="wow fadeInRight" data-wow-delay="0.2s">
                    <img src="{{ asset('img/volunteer.jpg') }}" class="w-full rounded-2xl shadow-lg mb-6" alt="Volunteer with Sallaamti" onerror="this.style.display='none'">
                    <div class="rounded-2xl p-6" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%)">
                        <h5 class="font-bold text-white mb-4">Why Volunteer with Us?</h5>
                        <div class="flex flex-col gap-3">
                            @foreach ([
                            ['🌍', 'Global Impact', 'Your work reaches Muslims in 20+ countries'],
                            ['📜', 'Volunteer Certificate', 'Receive an official recognition certificate'],
                            ['🤝', 'Community', 'Join a team of dedicated, passionate Muslims'],
                            ['🎓', 'Grow Your Skills', 'Develop professionally while serving the Ummah'],
                            ] as $why)
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">{{ $why[0] }}</span>
                                <div>
                                    <strong class="text-white text-sm">{{ $why[1] }}</strong>
                                    <p class="text-white/60 text-xs mb-0">{{ $why[2] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- DONATE --}}
    {{-- ============================================================ --}}
    <section id="donate" class="py-20 bg-cream">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="section-eyebrow">Activity 08</span>
                <h2 class="section-title">💝 Support Sallaamti</h2>
                <p class="section-subtitle">Your sadaqah and donations power every program we run — from free Quran education for orphans to supporting needy families find a spouse.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6 mb-12">
                @foreach ([
                ['📚', 'Quran Education Fund', 'Sponsor a student\'s Quran education for 6–12 months. Cover their teacher\'s salary and learning materials.', 'PKR 3,000/month', '#0d6b6b'],
                ['👨‍👩‍👧', 'Family Support Fund', 'Help needy families with basic necessities, counseling, and access to Sallaamti\'s services for free.', 'PKR 5,000/month', '#b8962e'],
                ['🛠️', 'Skills Training Fund', 'Sponsor vocational training for a young Muslim seeking dignified, halal employment and independence.', 'PKR 10,000/course', '#16a34a'],
                ] as $fund)
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <div class="h-1.5" style="background: {{ $fund[4] }}"></div>
                    <div class="p-6 text-center">
                        <div class="text-4xl mb-3">{{ $fund[0] }}</div>
                        <h5 class="font-bold text-gray-800 mb-2">{{ $fund[1] }}</h5>
                        <p class="text-gray-500 text-sm mb-4 leading-relaxed">{{ $fund[2] }}</p>
                        <div class="text-lg font-extrabold mb-4" style="color: {{ $fund[4] }}">{{ $fund[3] }}</div>
                        <a href="{{ route('donate.create') }}" class="btn-base text-sm px-6 py-2 w-full block text-white font-semibold" style="background: {{ $fund[4] }}; border-color: {{ $fund[4] }}; border-radius: 0.5rem;">Donate Now</a>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Payment Methods --}}
            <div class="bg-white rounded-2xl p-8 text-center shadow-sm">
                <h5 class="font-bold text-gray-800 mb-2">How to Donate</h5>
                <p class="text-gray-500 text-sm mb-6">Send your donation via JazzCash, EasyPaisa or bank transfer — then submit proof on our platform and our team will confirm within 24 hours.</p>
                <div class="flex flex-wrap justify-center gap-6 mb-6">
                    @foreach ([
                    ['💚', 'JazzCash'],
                    ['🟡', 'EasyPaisa'],
                    ['🏦', 'Bank Transfer'],
                    ['🌐', 'International Wire'],
                    ] as $method)
                    <div class="flex items-center gap-2 text-gray-600 text-sm font-medium">
                        <span class="text-xl">{{ $method[0] }}</span> {{ $method[1] }}
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('donate.create') }}" class="btn-base btn-gold px-10 py-3 font-semibold">Make a Donation →</a>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- FINAL CTA --}}
    {{-- ============================================================ --}}
    <section class="py-20 final-cta-section">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="text-4xl mb-4">🕌</div>
            <h2 class="final-cta-title">Every Action Counts — Start Yours Today</h2>
            <p class="final-cta-sub">Learn Quran, find a halal match, donate, volunteer — every step you take with Sallaamti is a step toward pleasing Allah.</p>
            <div class="flex gap-4 justify-center flex-wrap mt-8">
                @guest
                <a href="{{ route('register') }}" class="btn-base btn-gold text-lg px-10 py-4 font-semibold">
                    Join Sallaamti Free <i class="fa fa-arrow-right ml-2"></i>
                </a>
                <a href="{{ url('/contact') }}" class="btn-base btn-outline-light text-lg px-10 py-4">
                    Contact Us
                </a>
                @else
                <a href="{{ route('dashboard') }}" class="btn-base btn-gold text-lg px-10 py-4 font-semibold">
                    Go to My Dashboard <i class="fa fa-arrow-right ml-2"></i>
                </a>
                @endguest
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- PAGE-SPECIFIC STYLES ONLY --}}
    {{-- ============================================================ --}}
    <style>
        /* Sticky nav active state via JS scroll */
        .activity-tab-active {
            background: var(--teal) !important;
            color: #fff !important;
            border-color: var(--teal) !important;
        }

        /* Scrollbar hide for sticky tab nav */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Hover bg teal for volunteer cards */
        .hover\:bg-teal-50:hover {
            background-color: #e8f5f5;
        }

        .group:hover .group-hover\:text-teal-700 {
            color: #0d6b6b;
        }
    </style>

    <script>
        // Highlight active tab on scroll
        const sections = document.querySelectorAll('section[id]');
        const tabs = document.querySelectorAll('a[href^="#"]');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 120;
                if (window.scrollY >= sectionTop) current = section.getAttribute('id');
            });
            tabs.forEach(tab => {
                tab.classList.remove('activity-tab-active');
                if (tab.getAttribute('href') === '#' + current) {
                    tab.classList.add('activity-tab-active');
                }
            });
        });
    </script>

</x-guest-layout>