{{-- resources/views/about.blade.php --}}
<x-guest-layout title="About Us" description="Learn about Sallaamti's mission to spread Quranic education, free Digital Skills training, halal matrimonial matching, and community support for the Muslim Ummah.">
    @section('title', 'About Sallaamti — Our Mission, Vision & Team')
    @section('description', 'Learn about Sallaamti — an Islamic education platform dedicated to spreading Quranic knowledge, free Digital Skills training, supporting families and building a global Muslim community.')
    @section('keywords', 'learn quran online pakistan, online quran classes, quran teacher online, islamic matrimonial pakistan, nikah platform, free digital skills courses')
    @section('canonical', url('/about'))

    {{-- ============================================================ --}}
    {{-- PAGE HERO --}}
    {{-- ============================================================ --}}
    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 320px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="absolute inset-0 opacity-5 text-white flex items-center justify-center" style="font-size: 20rem; pointer-events: none;">❖</div>
        <div class="max-w-7xl mx-auto px-4 py-20 relative z-10 text-center w-full">
            <span class="section-eyebrow">Who We Are</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-2 mb-4">About Sallaamti</h1>
            <p class="text-white/70 text-lg max-w-2xl mx-auto">
                A grassroots Islamic platform dedicated to knowledge, compassion, community and the revival of Quranic values in everyday Muslim life.
            </p>
            <nav class="flex justify-center gap-2 mt-6 text-sm text-white/50">
                <a href="{{ url('/') }}" class="hover:text-white">Home</a>
                <span>/</span>
                <span class="text-white">About</span>
            </nav>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- MAIN ABOUT --}}
    {{-- ============================================================ --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid xl:grid-cols-2 gap-12 items-center mb-16">

                {{-- Images --}}
                <div class="grid grid-cols-2 gap-4">
                    <img src="{{ asset('img/about-1.jpg') }}" width="600" height="400" loading="lazy" class="w-full h-full object-cover rounded-2xl wow zoomIn" data-wow-delay="0.1s" alt="Sallaamti community">
                    <div class="flex flex-col gap-4">
                        <img src="{{ asset('img/about-2.jpg') }}" loading="lazy" class="w-full rounded-2xl wow zoomIn" data-wow-delay="0.2s" alt="">
                        <img src="{{ asset('img/about-3.jpg') }}" loading="lazy" class="w-full rounded-2xl wow zoomIn" data-wow-delay="0.3s" alt="">
                    </div>
                </div>

                {{-- Content --}}
                <div class="wow fadeIn" data-wow-delay="0.4s">
                    <span class="section-eyebrow">About Sallaamti (سلامتی)</span>
                    <h2 class="section-title mb-6">Allah Helps Those Who Help Themselves</h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Sallaamti is dedicated to spreading the teachings of the Quran and Hadith, enlightening individuals of all ages with the wisdom and guidance found within Islamic scripture. Our mission is to foster harmony and understanding among humanity, promoting peace — Sallaamati — for all.
                    </p>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Through educational programs, workshops, and live online classes, we equip people with the spiritual insights and moral principles essential for personal growth and societal well-being. Additionally, our community initiatives empower the less fortunate and support institutions like Nikah that strengthen the Muslim family unit.
                    </p>

                    {{-- Vision + Mission --}}
                    <div class="grid sm:grid-cols-2 gap-4 mb-8">
                        <div class="vision-card">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: var(--teal-light)">
                                <i class="fa fa-eye" style="color: var(--teal)"></i>
                            </div>
                            <div>
                                <h6 class="font-bold text-gray-800 mb-1">Our Vision</h6>
                                <p class="text-sm text-gray-500 mb-0">A world where every Muslim lives by the Quran and Sunnah — with dignity, knowledge and compassion.</p>
                            </div>
                        </div>
                        <div class="vision-card">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: #fdf6e3">
                                <i class="fa fa-flag" style="color: var(--gold)"></i>
                            </div>
                            <div>
                                <h6 class="font-bold text-gray-800 mb-1">Our Mission</h6>
                                <p class="text-sm text-gray-500 mb-0">Empowering Muslim individuals and families through Quranic education, trusted matrimonial services, and skills training.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Donation highlight --}}
                    <div class="rounded-xl p-4 flex items-center gap-4 mb-8" style="background: var(--cream); border: 1px solid #e5e5e5">
                        <img src="{{ asset('img/about-child.jpg') }}" loading="lazy" class="w-16 h-16 rounded-full object-cover flex-shrink-0" alt="">
                        <div class="flex-1 text-sm text-gray-600">
                            To continue our vital work educating youth and supporting families, we need your generous support. Your donation creates lasting change.
                        </div>
                        <div class="text-center flex-shrink-0">
                            <div class="text-2xl font-extrabold" style="color: var(--teal)">{{ setting('donate_goal_text', 'PKR 50K') }}</div>
                            <div class="text-xs text-gray-500 font-semibold">Raised</div>
                        </div>
                    </div>

                    {{-- Checklist --}}
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        @foreach (['Charity & Donation', 'Parent Education', 'Hadith & Sunnah', 'Empowering the Deserving'] as $item)
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fa fa-check-circle flex-shrink-0" style="color: var(--teal)"></i> {{ $item }}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Quran CTA Banner --}}
            <div class="rounded-2xl p-8 md:p-12 wow fadeIn" style="background: var(--teal)" data-wow-delay="0.1s">
                <div class="grid lg:grid-cols-12 gap-6 items-center">
                    <div class="lg:col-span-2 text-center">
                        <i class="fa fa-mosque text-white" style="font-size: 4rem; opacity: 0.8"></i>
                    </div>
                    <div class="lg:col-span-7 text-center lg:text-left">
                        <h2 class="text-2xl md:text-3xl font-extrabold text-white leading-tight">
                            Every Muslim Needs to Realise the Importance of Quranic Education
                        </h2>
                        <p class="text-white/70 mt-2">اقرأ، افهم، وطبّق — Read, Understand & Implement Quran in Your Life</p>
                    </div>
                    <div class="lg:col-span-3 text-center lg:text-right">
                        <a href="{{ route('courses.index') }}" class="btn-base btn-gold inline-block px-8 py-3 font-semibold">
                            Start Learning →
                        </a>
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
                <div class="bg-white rounded-2xl shadow-sm p-6" style="border-top: 4px solid {{ $level[3] }}">
                    <span class="inline-block text-xs font-bold text-white px-2.5 py-1 rounded-full mb-3" style="background: {{ $level[3] }}">{{ $level[0] }}</span>
                    <h5 class="font-bold text-gray-800 text-lg">{{ $level[1] }}</h5>
                    <p class="text-xs text-gray-400 mb-4">Duration: {{ $level[2] }}</p>
                    <ul class="space-y-1.5 mb-4">
                        @foreach ($level[4] as $topic)
                        <li class="text-sm text-gray-600 flex items-start gap-2">
                            <span class="text-xs mt-1" style="color: {{ $level[3] }}">●</span> {{ $topic }}
                        </li>
                        @endforeach
                    </ul>
                    <div class="text-xs text-gray-500 pt-3 border-t"><strong class="text-gray-700">Outcome:</strong> {{ $level[5] }}</div>
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
    {{-- OUR VALUES --}}
    {{-- ============================================================ --}}
    <section class="py-20 bg-cream">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="section-eyebrow">What Drives Us</span>
                <h2 class="section-title">Our Core Values</h2>
                <p class="section-subtitle">Every decision at Sallaamti is guided by these principles drawn from the Quran and Sunnah.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ([
                ['📖', 'Knowledge (Ilm)', 'Spreading authentic Quranic and Islamic knowledge to every Muslim, young and old, near and far.', 'var(--teal)'],
                ['🤝', 'Brotherhood (Ukhuwwah)', 'Building a community where every Muslim cares for their brother and sister in faith.', 'var(--gold)'],
                ['💚', 'Compassion (Rahmah)', 'Serving the needy, the orphan, the widow and the less fortunate with genuine care.', '#16a34a'],
                ['🛡️', 'Trust (Amanah)', 'Maintaining complete transparency and trust in all our programs, finances and relationships.', '#1a5276'],
                ] as $val)
                <div class="value-card text-center p-8 bg-white rounded-2xl shadow-sm border-t-4 hover:-translate-y-1 transition-transform duration-300" style="border-color: {{ $val[3] }}">
                    <div class="text-4xl mb-4">{{ $val[0] }}</div>
                    <h5 class="font-bold text-gray-800 mb-3">{{ $val[1] }}</h5>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $val[2] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- PROGRAMS DETAIL --}}
    {{-- ============================================================ --}}
    <section class="py-20 bg-white" x-data="{ tab: 'quran' }">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="section-eyebrow">What We Offer</span>
                <h2 class="section-title">Our Programs & Services</h2>
            </div>

            <div class="flex flex-wrap justify-center gap-3 mb-12">
                @foreach (['quran' => '📖 Quran Courses', 'live' => '🎥 Live Classes', 'nikah' => '💍 Nikah', 'skills' => '💻 Skills'] as $key => $label)
                <button @click="tab = '{{ $key }}'" class="px-5 py-2.5 rounded-full text-sm font-semibold border-2 transition"
                    :class="tab === '{{ $key }}' ? 'text-white' : 'text-gray-500 border-gray-200 hover:border-gray-300'"
                    :style="tab === '{{ $key }}' ? 'background: var(--teal); border-color: var(--teal)' : ''">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- Quran Tab --}}
            <div x-show="tab === 'quran'" x-transition>
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <span class="section-eyebrow">Self-Paced Learning</span>
                        <h3 class="text-2xl font-bold mb-4">Learn Quran at Your Own Pace</h3>
                        <p class="text-gray-500 mb-6">Our structured courses take you from basic Arabic alphabet all the way to deep Tafseer. Video lessons, quizzes, and a certificate upon completion.</p>
                        <div class="space-y-3 my-6">
                            @foreach ([['Level 1', 'Nazrah Quran', '6-12 months', '📖'], ['Level 2', 'Tajweed', '6-8 months', '🎵'], ['Level 3', 'Translation', '6-8 months', '📝'], ['Level 4', 'Arabic Grammar', '4-6 months', '🔤']] as $step)
                            <div class="flex items-center gap-3 bg-cream rounded-xl p-3">
                                <span class="text-2xl">{{ $step[3] }}</span>
                                <div>
                                    <strong class="text-gray-800 text-sm">{{ $step[0] }}: {{ $step[1] }}</strong>
                                    <p class="text-xs text-gray-400">Duration: {{ $step[2] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="flex gap-3 flex-wrap">
                            <a href="{{ route('courses.index') }}" class="btn-teal btn-base px-6">Browse All Courses</a>
                            @guest
                            <a href="{{ route('register') }}" class="btn-gold btn-base px-6">Join Free</a>
                            @endguest
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($previewCourses as $course)
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden border">
                            @if ($course->thumbnail)
                            <img src="{{ Storage::url($course->thumbnail) }}" loading="lazy" alt="{{ $course->title }}" class="w-full h-24 object-cover">
                            @else
                            <div class="w-full h-24 flex items-center justify-center text-3xl" style="background: var(--teal-light)">📖</div>
                            @endif
                            <div class="p-3">
                                <strong class="text-sm text-gray-800 block truncate">{{ $course->title }}</strong>
                                <small class="text-xs text-gray-400">{{ $course->category }}</small>
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

            {{-- Live Tab --}}
            <div x-show="tab === 'live'" x-transition x-cloak>
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <span class="section-eyebrow">Online Live Sessions</span>
                        <h3 class="text-2xl font-bold mb-4">Live Quran Classes with Expert Teachers</h3>
                        <p class="text-gray-500 mb-6">One-to-one and group live classes via Zoom. Fixed schedule, monthly subscription, personal attention for every student.</p>
                        <div class="space-y-2 my-6">
                            @foreach (['5 classes per week, 30–45 mins each', 'One-to-one OR group class options', 'Male or Female teachers available', 'Weekly tests + Monthly progress reports', 'PKR 3,000–5,000/month | $25–$50 international'] as $f)
                            <div class="flex items-start gap-2 text-sm text-gray-600">
                                <span>✅</span> {{ $f }}
                            </div>
                            @endforeach
                        </div>
                        <a href="{{ route('quran-live.index') }}" class="btn-teal btn-base px-6">View Live Courses</a>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg border overflow-hidden">
                        <div class="flex items-center gap-1.5 px-4 py-3 border-b bg-gray-50">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-green-400"></span>
                            <span class="text-xs text-gray-500 ms-2">Sallaamti Live Class — Tajweed Level 2</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 p-5">
                            <div class="flex flex-col items-center gap-1"><span class="text-3xl">👩‍🏫</span><span class="text-xs text-gray-500">Teacher</span></div>
                            <div class="flex flex-col items-center gap-1"><span class="text-3xl">🧑‍🎓</span><span class="text-xs text-gray-500">Student 1</span></div>
                            <div class="flex flex-col items-center gap-1"><span class="text-3xl">👦</span><span class="text-xs text-gray-500">Student 2</span></div>
                            <div class="flex flex-col items-center gap-1"><span class="text-3xl">👧</span><span class="text-xs text-gray-500">Student 3</span></div>
                        </div>
                        <div class="flex items-center justify-between px-4 py-3 border-t bg-gray-50">
                            <span class="text-xs font-bold text-red-500">🔴 LIVE</span>
                            <span class="text-gray-400 text-xs">Sallaamti.com · Secure Session</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Nikah Tab --}}
            <div x-show="tab === 'nikah'" x-transition x-cloak>
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <span class="section-eyebrow">Islamic Matrimonial</span>
                        <h3 class="text-2xl font-bold mb-4">Find a Halal Match — Properly</h3>
                        <p class="text-gray-500 mb-6">No casual swiping. Every profile is CNIC-verified. Guardian-mediated contact only. Your photo is private until both sides accept.</p>
                        <div class="space-y-3 my-6">
                            @foreach ([['🔒', 'CNIC Verified', 'Every profile reviewed by our team'], ['👨‍👩‍👧', 'Guardian Mediated', 'Contact only through guardian channels'], ['📸', 'Private Photos', 'Photos visible only after mutual acceptance'], ['💳', 'Serious Members Only', 'Small verification fee filters out non-serious'], ['🌍', 'Global Platform', 'Muslims from Pakistan and worldwide']] as $item)
                            <div class="flex items-start gap-3">
                                <span class="text-xl">{{ $item[0] }}</span>
                                <div><strong class="text-sm text-gray-800">{{ $item[1] }}</strong><p class="text-xs text-gray-400">{{ $item[2] }}</p></div>
                            </div>
                            @endforeach
                        </div>
                        <a href="{{ route('register') }}" class="btn-teal btn-base px-6">Create Your Profile</a>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg border overflow-hidden">
                        <div class="px-4 py-3 border-b bg-cream">
                            <span class="text-xs font-bold" style="color: var(--teal)">✅ Verified Profile</span>
                        </div>
                        <div class="flex items-center gap-4 p-5">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center text-3xl flex-shrink-0" style="background: var(--teal-light)">👤</div>
                            <div>
                                <h5 class="font-bold text-gray-800">28 yrs · Karachi</h5>
                                <p class="text-xs text-gray-500">Education: Masters · Profession: Engineer</p>
                                <p class="text-xs text-gray-500">Sect: Sunni · Family: Nuclear</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3 border-t">
                            <div class="flex items-center gap-2 flex-1">
                                <div class="flex-1 bg-gray-200 rounded-full h-1.5"><div class="h-1.5 rounded-full" style="width:78%; background: var(--gold)"></div></div>
                                <span class="text-xs text-gray-500 flex-shrink-0">78% Match</span>
                            </div>
                            <span class="text-xs text-gray-400 ms-3">🔒 Private</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Skills Tab --}}
            <div x-show="tab === 'skills'" x-transition x-cloak>
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <span class="section-eyebrow">Presented by IZMA Digital Technology & Security</span>
                        <h3 class="text-2xl font-bold mb-4">Skills Training for Dignified Livelihood</h3>
                        <p class="text-gray-500 mb-6">Empowering every Muslim with practical digital skills that lead to halal income and independence.</p>
                        <div class="flex flex-wrap gap-2 my-6">
                            @foreach ([
                                ['💻 Computer Skills', 'Computer Skills'],
                                ['🎨 Graphic Design', 'Graphic Design'],
                                ['🌐 Web Development', 'Web Development'],
                                ['📱 Mobile Apps', 'Mobile Apps'],
                                ['📈 Digital Marketing', 'Digital Marketing'],
                            ] as [$label, $category])
                            <a href="{{ route('skills.index', ['category' => $category]) }}" class="text-xs font-medium px-3 py-1.5 rounded-full bg-cream text-gray-600 hover:bg-teal-50 hover:text-teal-700 transition">{{ $label }}</a>
                            @endforeach
                        </div>
                        <a href="{{ route('volunteer.create') }}" class="btn-teal btn-base px-6">Join as Instructor Volunteer</a>
                    </div>
                    <div class="text-center bg-cream rounded-2xl p-8">
                        <div class="text-5xl mb-3">💻</div>
                        <h4 class="font-bold text-gray-800 text-lg">Free & Self-Paced</h4>
                        <p class="text-gray-500 text-sm mb-4">Learn at your own pace and earn a certificate — presented by IZMA Digital Technology & Security</p>
                        <a href="{{ route('skills.index') }}" class="btn-gold btn-base px-6">Explore Courses</a>
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
                'badge' => 'Live',
                ],
                [
                'icon' => 'fa-laptop',
                'color' => 'text-gold',
                'emoji' => '🎥',
                'title' => 'Live Quran Classes',
                'desc' => 'One-to-one and group live online classes with qualified teachers. Flexible timings for Pakistan and international students.',
                'url' => route('quran-live.index'),
                'btn' => 'View Live Classes',
                'badge' => 'Live',
                ],
                [
                'icon' => 'fa-laptop-code',
                'color' => 'text-teal',
                'emoji' => '💻',
                'title' => 'Digital Skills',
                'desc' => 'Free, self-paced skills training — Web Development, Graphic Design, Digital Marketing and more — presented by IZMA Digital Technology & Security.',
                'url' => route('skills.index'),
                'btn' => 'Explore Skills',
                'badge' => 'Live',
                ],
                [
                'icon' => 'fa-heart',
                'color' => 'text-red-500',
                'emoji' => '💍',
                'title' => 'Sallaamti Nikah',
                'desc' => 'A verified, CNIC-checked Islamic matrimonial platform. Guardian-mediated, photo-private and built on Islamic values.',
                'url' => route('nikah.create'),
                'btn' => 'Create Profile',
                'badge' => 'Live',
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
                'badge' => 'Live',
                ],
                [
                'icon' => 'fa-users',
                'color' => 'text-teal',
                'emoji' => '🤝',
                'title' => 'Volunteer with Us',
                'desc' => 'Join as a Quran teacher, counselor, developer or outreach volunteer. Earn Sadaqah Jariyah while serving the Ummah.',
                'url' => route('volunteer.create'),
                'btn' => 'Apply as Volunteer',
                'badge' => 'Live',
                ],
                [
                'icon' => 'fa-hand-holding-heart',
                'color' => 'text-teal',
                'emoji' => '🤲',
                'title' => 'Sallaamti Wall',
                'desc' => 'Share dua requests, celebrate community stories, and post your own reflections — with admin-reviewed content so the feed stays genuine.',
                'url' => route('wall.index'),
                'btn' => 'Visit the Wall',
                'badge' => 'Live',
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
    {{-- IMPACT NUMBERS --}}
    {{-- ============================================================ --}}
    <section id="impact-numbers" class="py-20" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%)">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="section-eyebrow text-gold">Our Impact</span>
                <h2 class="text-3xl font-extrabold text-white">Growing Together, Alhamdulillah</h2>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
                @foreach ([
                [\App\Models\User::count(), 'Registered Members', '👥'],
                [\App\Models\Enrollment::count(), 'Course Enrollments', '📖'],
                [\App\Models\Certificate::count(), 'Certificates Issued', '🎓'],
                [\App\Models\NikahProfile::where('verification_status','verified')->count(), 'Verified Nikah Profiles', '💍'],
                ] as $stat)
                <div>
                    <div class="text-4xl mb-2">{{ $stat[2] }}</div>
                    <div class="text-3xl md:text-4xl font-extrabold mb-1 stat-number" data-target="{{ $stat[0] }}" style="color: var(--gold)">0</div>
                    <div class="text-white/60 text-sm uppercase tracking-widest">{{ $stat[1] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- TEAM --}}
    {{-- ============================================================ --}}
    <section class="py-20 bg-cream">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12 wow fadeIn" data-wow-delay="0.1s">
                <span class="section-eyebrow">Our Team</span>
                <h2 class="section-title">Meet the People Behind Sallaamti</h2>
                <p class="section-subtitle">Dedicated volunteers and professionals committed to serving the Ummah.</p>
            </div>

            @php
            $founder = $teamMembers->firstWhere('is_founder', true);
            $otherMembers = $teamMembers->reject(fn ($m) => $m->is_founder);
            @endphp

            {{-- Featured Leader --}}
            @if ($founder)
            <div class="grid lg:grid-cols-12 gap-8 mb-12 items-center">
                <div class="lg:col-span-4 xl:col-span-4 wow zoomIn" data-wow-delay="0.1s">
                    <div class="relative">
                        <div class="w-full aspect-square rounded-2xl shadow-lg overflow-hidden bg-white flex items-center justify-center">
                            @if ($founder->photo)
                            <img src="{{ Storage::url($founder->photo) }}" loading="lazy" class="w-full h-full object-contain" alt="{{ $founder->name }}">
                            @else
                            <div class="text-6xl">👤</div>
                            @endif
                        </div>
                        <div class="absolute -bottom-4 -right-4 w-20 h-20 rounded-2xl flex items-center justify-center text-3xl shadow-lg" style="background: var(--gold)">🕌</div>
                    </div>
                </div>
                <div class="lg:col-span-8 xl:col-span-8 wow fadeIn" data-wow-delay="0.2s">
                    <span class="section-eyebrow">{{ $founder->role }}</span>
                    <h2 class="text-3xl font-extrabold text-gray-800 mb-1">{{ $founder->name }}</h2>
                    @if ($founder->bio)
                    <div class="prose prose-sm max-w-none text-gray-600 mb-6 leading-relaxed">{!! $founder->bio !!}</div>
                    @endif
                    <div class="flex gap-3">
                        @if ($founder->facebook_url)
                        <a href="{{ $founder->facebook_url }}" class="social-btn" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        @endif
                        @if ($founder->instagram_url)
                        <a href="{{ $founder->instagram_url }}" class="social-btn" target="_blank"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if ($founder->tiktok_url)
                        <a href="{{ $founder->tiktok_url }}" class="social-btn" target="_blank"><i class="fab fa-tiktok"></i></a>
                        @endif
                        @if ($founder->whatsapp_number)
                        <a href="https://wa.me/{{ $founder->whatsapp_number }}" class="social-btn" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Team Grid --}}
            @if ($otherMembers->isNotEmpty())
            <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($otherMembers as $member)
                <div class="team-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 wow zoomIn" data-wow-delay="0.1s">
                    <div class="w-full aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
                        @if ($member->photo)
                        <img src="{{ Storage::url($member->photo) }}" loading="lazy" class="w-full h-full object-contain" alt="{{ $member->name }}">
                        @else
                        <div class="text-4xl">👤</div>
                        @endif
                    </div>
                    <div class="p-4 text-center">
                        <h5 class="font-bold text-gray-800 mb-0">{{ $member->name }}</h5>
                        <p class="text-sm font-semibold mb-2" style="color: var(--teal)">{{ $member->role }}</p>
                        @if ($member->bio)
                        <div class="prose prose-sm max-w-none text-gray-500">{!! $member->bio !!}</div>
                        @endif
                        <div class="flex justify-center gap-2 mt-2">
                            @if ($member->facebook_url)
                            <a href="{{ $member->facebook_url }}" target="_blank" class="social-btn social-btn-sm"><i class="fab fa-facebook-f"></i></a>
                            @endif
                            @if ($member->instagram_url)
                            <a href="{{ $member->instagram_url }}" target="_blank" class="social-btn social-btn-sm"><i class="fab fa-instagram"></i></a>
                            @endif
                            @if ($member->tiktok_url)
                            <a href="{{ $member->tiktok_url }}" target="_blank" class="social-btn social-btn-sm"><i class="fab fa-tiktok"></i></a>
                            @endif
                            @if ($member->whatsapp_number)
                            <a href="https://wa.me/{{ $member->whatsapp_number }}" target="_blank" class="social-btn social-btn-sm"><i class="fab fa-whatsapp"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- NIKAH PROCESS --}}
    {{-- ============================================================ --}}
    <section class="py-20" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%)">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="section-eyebrow text-gold">Islamic Matrimonial</span>
                    <h2 class="section-title text-white">Marriage Is Half of Your Deen</h2>
                    <p class="text-white/60 mb-4 italic">The Prophet ﷺ said: "Whoever Allah blesses with a righteous spouse, He has helped him with half of his religion." — Ibn Hibban</p>
                    <p class="text-white/60 mb-6">Sallaamti's Nikah platform is built on Islamic values: verified profiles, guardian-mediated communication, complete privacy protection.</p>
                    @guest
                    <a href="{{ route('register') }}" class="btn-gold btn-base px-8 py-4 inline-block font-semibold">Create Your Profile →</a>
                    @else
                    <a href="{{ route('nikah.show') }}" class="btn-gold btn-base px-8 py-4 inline-block font-semibold">Go to Nikah Profile →</a>
                    @endguest
                </div>
                <div class="space-y-3">
                    @foreach ([
                    ['1', 'Register & Create Profile', 'Fill your profile with personal, educational and family details'],
                    ['2', 'Pay Verification Fee', 'A small fee confirms you are serious and funds the platform'],
                    ['3', 'CNIC Verification', 'Our team verifies your identity for a trusted community'],
                    ['4', 'Browse & Express Interest', 'Find compatible matches and send an interest request'],
                    ['5', 'Guardian Contact', 'After mutual acceptance, guardian contact details are shared'],
                    ] as $step)
                    <div class="flex items-start gap-4 rounded-xl p-4" style="background: rgba(255,255,255,0.06)">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0" style="background: var(--gold); color: #1a1a2e">{{ $step[0] }}</div>
                        <div>
                            <strong class="text-white text-sm">{{ $step[1] }}</strong>
                            <p class="text-sm text-white/50 mb-0">{{ $step[2] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- WHY SALLAAMTI --}}
    {{-- ============================================================ --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="section-eyebrow">Why Choose Us</span>
                    <h2 class="section-title mb-6">What Makes Sallaamti Different</h2>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Unlike generic online platforms, every feature of Sallaamti is built specifically for the Muslim community — with Islamic values, privacy, and trust at the core.
                    </p>
                    <div class="flex flex-col gap-4">
                        @foreach ([
                        ['✅', 'Completely Islamic', 'Every module — from education to matrimonial — is built on Quranic principles and verified by Islamic scholars.'],
                        ['🔒', 'Privacy First', 'No public photos on Nikah until mutual acceptance. CNIC verified members only. Your data is protected.'],
                        ['🌍', 'Truly Global', 'Students from Pakistan, Middle East, UK, USA and beyond — all in one trusted community.'],
                        ['📜', 'Certified Learning', 'Structured curriculum with weekly quizzes, monthly tests, and internationally recognized certificates.'],
                        ['🤲', 'Community Driven', 'Volunteer teachers, counselors and supporters who genuinely care — not just a business transaction.'],
                        ] as $why)
                        <div class="flex gap-4 items-start">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg flex-shrink-0" style="background: var(--teal-light)">{{ $why[0] }}</div>
                            <div>
                                <strong class="text-gray-800">{{ $why[1] }}</strong>
                                <p class="text-sm text-gray-500 mt-1 mb-0">{{ $why[2] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 rounded-2xl overflow-hidden">
                        <img src="{{ asset('img/about-4.jpg') }}" loading="lazy" class="w-full h-56 object-cover wow zoomIn" data-wow-delay="0.1s" alt="">
                    </div>
                    <div class="rounded-2xl overflow-hidden">
                        <img src="{{ asset('img/about-5.jpg') }}" loading="lazy" class="w-full h-40 object-cover wow zoomIn" data-wow-delay="0.2s" alt="">
                    </div>
                    <div class="rounded-2xl overflow-hidden">
                        <img src="{{ asset('img/about-6.jpg') }}" loading="lazy" class="w-full h-40 object-cover wow zoomIn" data-wow-delay="0.3s" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- TESTIMONIALS --}}
    {{-- ============================================================ --}}
    @if ($testimonials->isNotEmpty())
    <section class="py-20 overflow-hidden" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%)">
        <div class="max-w-7xl mx-auto px-4">

            <div class="text-center mb-14">
                <span class="section-eyebrow" style="color: rgba(255,255,255,0.6)">Community Stories</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-white mt-2">What Our Community Says</h2>
                <p class="text-white/50 text-sm mt-3">Real experiences from students, parents and families around the world.</p>
            </div>

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
                <div class="absolute -top-8 left-1/2 -translate-x-1/2 text-9xl font-serif select-none pointer-events-none z-0 leading-none"
                    style="color: rgba(184,150,46,0.15)">"</div>

                <div class="relative overflow-hidden">
                    <div class="flex transition-transform duration-700 ease-in-out"
                        :style="`transform: translateX(-${active * 100}%)`">

                        @foreach ($testimonials as $index => $t)
                        <div class="w-full flex-shrink-0 px-4 sm:px-16 md:px-28 lg:px-48">
                            <div class="rounded-3xl p-8 md:p-12 text-center relative z-10 border border-white/10"
                                style="background: rgba(255,255,255,0.07); backdrop-filter: blur(12px)">

                                <div class="flex justify-center gap-1.5 mb-6">
                                    @for ($i = 0; $i < 5; $i++)
                                        <svg class="w-5 h-5 transition-opacity {{ $i < $t->rating ? 'opacity-100' : 'opacity-20' }}"
                                        viewBox="0 0 20 20" fill="{{ $i < $t->rating ? '#b8962e' : '#ffffff' }}">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        @endfor
                                </div>

                                <p class="text-white/90 text-lg md:text-xl leading-relaxed italic font-light mb-8">
                                    "{{ $t->content }}"
                                </p>

                                <div class="w-12 h-px mx-auto mb-6" style="background: rgba(184,150,46,0.5)"></div>

                                <div class="flex items-center justify-center gap-4">
                                    <div class="relative flex-shrink-0">
                                        <img src="{{ $t->photo ? Storage::url($t->photo) : asset('img/testimonial-1.jpg') }}"
                                            loading="lazy" class="w-14 h-14 rounded-full object-cover border-2 border-white/20"
                                            alt="{{ $t->name }}">
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

                <button @click="prev()"
                    class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-0 sm:-translate-x-2 w-10 h-10 rounded-full flex items-center justify-center text-white transition-all duration-200 border border-white/20 hover:border-white/50 hover:bg-white/10 focus:outline-none z-20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button @click="next()"
                    class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-0 sm:translate-x-2 w-10 h-10 rounded-full flex items-center justify-center text-white transition-all duration-200 border border-white/20 hover:border-white/50 hover:bg-white/10 focus:outline-none z-20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <div class="mt-10 flex flex-col items-center gap-3">
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

                    <p class="text-white/25 text-xs">
                        <span x-text="active + 1"></span>&thinsp;/&thinsp;{{ $testimonials->count() }}
                    </p>
                </div>

                <div class="mt-4 max-w-xs mx-auto h-0.5 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.1)">
                    <div class="h-full rounded-full transition-all duration-700"
                        style="background: #b8962e"
                        :style="`width: ${((active + 1) / total) * 100}%`">
                    </div>
                </div>

            </div>

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
    {{-- JOIN CTA --}}
    {{-- ============================================================ --}}
    <section class="py-20 final-cta-section">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="text-4xl mb-4">🕌</div>
            <h2 class="final-cta-title">Be Part of the Sallaamti Family</h2>
            <p class="final-cta-sub">Join thousands of Muslims already learning, growing and connecting. Free to join — and your first step could change your life.</p>
            <div class="flex gap-4 justify-center flex-wrap mt-8">
                @guest
                <a href="{{ route('register') }}" class="btn-base btn-gold text-lg px-10 py-4 font-semibold">
                    Register Free Now <i class="fa fa-arrow-right ml-2"></i>
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

    <script>
        // Animated counter for the Impact Numbers section (moved here from
        // the old homepage, which had this same effect).
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
                    counter.textContent = Math.floor(current).toLocaleString() + '+';
                }, 16);
            });
        }
        const impactSection = document.getElementById('impact-numbers');
        if (impactSection) {
            new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                    }
                });
            }, { threshold: 0.3 }).observe(impactSection);
        }
    </script>

    {{-- ============================================================ --}}
    {{-- STYLES --}}
    {{-- ============================================================ --}}
    <style>
        :root {
            --teal: #0d6b6b;
            --teal-dark: #095555;
            --teal-light: #e8f5f5;
            --gold: #b8962e;
            --cream: #fdfaf3;
            --text-dark: #1a1a2e;
            --text-muted: #6c757d;
        }

        /* Buttons */
        .btn-base {
            display: inline-block;
            border-radius: 0.5rem;
            cursor: pointer;
            border: 2px solid transparent;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s ease;
            font-weight: 500;
            line-height: 1.5;
        }

        .btn-gold {
            background: var(--gold);
            border-color: var(--gold);
            color: #fff !important;
        }

        .btn-gold:hover {
            background: #9a7b25;
            border-color: #9a7b25;
        }

        .btn-teal {
            background: var(--teal);
            border-color: var(--teal);
            color: #fff !important;
        }

        .btn-teal:hover {
            background: var(--teal-dark);
        }

        .btn-outline-light {
            border-color: rgba(255, 255, 255, 0.7);
            color: #fff !important;
            background: transparent;
        }

        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        /* Typography */
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

        /* Colors */
        .bg-cream {
            background: var(--cream);
        }

        .text-teal {
            color: var(--teal) !important;
        }

        .text-gold {
            color: var(--gold) !important;
        }

        /* Vision Cards */
        .vision-card {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
            background: var(--cream);
            border-radius: 10px;
        }

        /* Value Cards */
        .value-card {
            border-radius: 16px;
        }

        /* Social Buttons */
        .social-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s;
            background: var(--teal);
        }

        .social-btn:hover {
            background: var(--gold);
            color: #fff;
        }

        .social-btn-sm {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 12px;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.2);
            transition: all 0.2s;
        }

        .social-btn-sm:hover {
            background: var(--gold);
        }

        /* Team Cards */
        .team-card {
            transition: all 0.3s;
        }

        .team-card:hover {
            transform: translateY(-4px);
        }

        /* Program Blocks */
        .program-block h5 {
            font-weight: 700;
            font-size: 1rem;
        }

        /* Final CTA */
        .final-cta-section {
            background: linear-gradient(135deg, #b8962e 0%, #0d6b6b 100%);
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

        /* Mobile */
        @media (max-width: 767px) {
            .page-hero {
                min-height: 250px !important;
            }

            .social-btn {
                width: 35px;
                height: 35px;
            }
        }
    </style>

</x-guest-layout>