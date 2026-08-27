{{-- resources/views/about.blade.php --}}
<x-guest-layout :title="__('db.About Us')" :description="__('db.Learn about Sallaamti\'s mission to spread Quranic education, free Digital Skills training, halal matrimonial matching, and community support for the Muslim Ummah.')">
    @section('title', __('db.About Sallaamti — Our Mission, Vision & Team'))
    @section('description', __('db.Learn about Sallaamti — an Islamic education platform dedicated to spreading Quranic knowledge, free Digital Skills training, supporting families and building a global Muslim community.'))
    @section('keywords', __('db.learn quran online pakistan, online quran classes, quran teacher online, islamic matrimonial pakistan, nikah platform, free digital skills courses'))
    @section('canonical', url('/about'))

    {{-- ============================================================ --}}
    {{-- PAGE HERO --}}
    {{-- ============================================================ --}}
    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 320px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="absolute inset-0 opacity-5 text-white flex items-center justify-center" style="font-size: 20rem; pointer-events: none;">❖</div>
        <div class="max-w-7xl mx-auto px-4 py-20 relative z-10 text-center w-full">
            <span class="section-eyebrow">{{ __('db.Who We Are') }}</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-2 mb-4">{{ __('db.About Sallaamti') }}</h1>
            <p class="text-white/70 text-lg max-w-2xl mx-auto">
                {{ __('db.A grassroots Islamic platform dedicated to knowledge, compassion, community and the revival of Quranic values in everyday Muslim life.') }}
            </p>
            <nav class="flex justify-center gap-2 mt-6 text-sm text-white/50">
                <a href="{{ url('/') }}" class="hover:text-white">{{ __('db.Home') }}</a>
                <span>/</span>
                <span class="text-white">{{ __('db.About') }}</span>
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
                    <img src="{{ asset('img/about-1.jpg') }}" width="600" height="400" loading="lazy" class="w-full h-full object-cover rounded-2xl wow zoomIn" data-wow-delay="0.1s" alt="{{ __('db.Sallaamti community') }}">
                    <div class="flex flex-col gap-4">
                        <img src="{{ asset('img/about-2.jpg') }}" loading="lazy" class="w-full rounded-2xl wow zoomIn" data-wow-delay="0.2s" alt="">
                        <img src="{{ asset('img/about-3.jpg') }}" loading="lazy" class="w-full rounded-2xl wow zoomIn" data-wow-delay="0.3s" alt="">
                    </div>
                </div>

                {{-- Content --}}
                <div class="wow fadeIn" data-wow-delay="0.4s">
                    <span class="section-eyebrow">{{ __('db.About Sallaamti (سلامتی)') }}</span>
                    <h2 class="section-title mb-6">{{ __('db.Allah Helps Those Who Help Themselves') }}</h2>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        {{ __('db.Sallaamti is dedicated to spreading the teachings of the Quran and Hadith, enlightening individuals of all ages with the wisdom and guidance found within Islamic scripture. Our mission is to foster harmony and understanding among humanity, promoting peace — Sallaamati — for all.') }}
                    </p>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        {{ __('db.Through educational programs, workshops, and live online classes, we equip people with the spiritual insights and moral principles essential for personal growth and societal well-being. Additionally, our community initiatives empower the less fortunate and support institutions like Nikah that strengthen the Muslim family unit.') }}
                    </p>

                    {{-- Vision + Mission --}}
                    <div class="grid sm:grid-cols-2 gap-4 mb-8">
                        <div class="vision-card">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: var(--teal-light)">
                                <i class="fa fa-eye" style="color: var(--teal)"></i>
                            </div>
                            <div>
                                <h6 class="font-bold text-gray-800 mb-1">{{ __('db.Our Vision') }}</h6>
                                <p class="text-sm text-gray-500 mb-0">{{ __('db.A world where every Muslim lives by the Quran and Sunnah — with dignity, knowledge and compassion.') }}</p>
                            </div>
                        </div>
                        <div class="vision-card">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: #fdf6e3">
                                <i class="fa fa-flag" style="color: var(--gold)"></i>
                            </div>
                            <div>
                                <h6 class="font-bold text-gray-800 mb-1">{{ __('db.Our Mission') }}</h6>
                                <p class="text-sm text-gray-500 mb-0">{{ __('db.Empowering Muslim individuals and families through Quranic education, trusted matrimonial services, and skills training.') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Donation highlight --}}
                    <div class="rounded-xl p-4 flex items-center gap-4 mb-8" style="background: var(--cream); border: 1px solid #e5e5e5">
                        <img src="{{ asset('img/about-child.jpg') }}" loading="lazy" class="w-16 h-16 rounded-full object-cover flex-shrink-0" alt="">
                        <div class="flex-1 text-sm text-gray-600">
                            {{ __('db.To continue our vital work educating youth and supporting families, we need your generous support. Your donation creates lasting change.') }}
                        </div>
                        <div class="text-center flex-shrink-0">
                            <div class="text-2xl font-extrabold" style="color: var(--teal)">{{ setting('donate_goal_text', 'PKR 50K') }}</div>
                            <div class="text-xs text-gray-500 font-semibold">{{ __('db.Raised') }}</div>
                        </div>
                    </div>

                    {{-- Checklist --}}
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        @foreach ([__('db.Charity & Donation'), __('db.Parent Education'), __('db.Hadith & Sunnah'), __('db.Empowering the Deserving')] as $item)
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
                            {{ __('db.Every Muslim Needs to Realise the Importance of Quranic Education') }}
                        </h2>
                        <p class="text-white/70 mt-2">اقرأ، افهم، وطبّق — {{ __('db.Read, Understand & Implement Quran in Your Life') }}</p>
                    </div>
                    <div class="lg:col-span-3 text-center lg:text-right">
                        <a href="{{ route('courses.index') }}" class="btn-base btn-gold inline-block px-8 py-3 font-semibold">
                            {{ __('db.Start Learning →') }}
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
                <span class="section-eyebrow">{{ __('db.Complete Curriculum') }}</span>
                <h2 class="section-title">{{ __('db.A Structured Path to Quranic Knowledge') }}</h2>
                <p class="section-subtitle">{{ __('db.Our 4-level syllabus takes you from basics to deep understanding') }}</p>
            </div>
            <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-6">
                @foreach ([
                [__('db.Level 1'), __('db.Nazrah Quran'), __('db.6–12 months'), '#0d6b6b', [__('db.Arabic Alphabet (Makharij)'), __('db.Zabar, Zair, Pesh'), __('db.Basic Tajweed Rules'), __('db.Fluency & Accuracy')], __('db.Student reads Quran fluently with correct pronunciation.')],
                [__('db.Level 2'), __('db.Tajweed'), __('db.6–8 months'), '#b8962e', [__('db.Makharij Detail'), __('db.Sifaat (Letter Characteristics)'), __('db.Noon Sakinah & Tanween'), __('db.Qalqalah, Ghunna, Idgham')], __('db.Student recites Quran beautifully with proper Tajweed.')],
                [__('db.Level 3'), __('db.Translation'), __('db.6–8 months'), '#1a5276', [__('db.100 Common Quran Words'), __('db.Word by Word Translation'), __('db.Short Surah Translation'), __('db.Easy Tafseer')], __('db.Student understands Quran with translation & simple Tafseer.')],
                [__('db.Level 4'), __('db.Arabic Grammar'), __('db.4–6 months'), '#922b21', [__('db.Arabic Alphabets Revision'), __('db.Nouns, Verbs, Letters'), __('db.Masculine, Feminine, Plural'), __('db.Sentence Structure')], __('db.Student connects with Quranic Arabic language.')]
                ] as $level)
                <div class="bg-white rounded-2xl shadow-sm p-6" style="border-top: 4px solid {{ $level[3] }}">
                    <span class="inline-block text-xs font-bold text-white px-2.5 py-1 rounded-full mb-3" style="background: {{ $level[3] }}">{{ $level[0] }}</span>
                    <h5 class="font-bold text-gray-800 text-lg">{{ $level[1] }}</h5>
                    <p class="text-xs text-gray-400 mb-4">{{ __('db.Duration: :duration', ['duration' => $level[2]]) }}</p>
                    <ul class="space-y-1.5 mb-4">
                        @foreach ($level[4] as $topic)
                        <li class="text-sm text-gray-600 flex items-start gap-2">
                            <span class="text-xs mt-1" style="color: {{ $level[3] }}">●</span> {{ $topic }}
                        </li>
                        @endforeach
                    </ul>
                    <div class="text-xs text-gray-500 pt-3 border-t"><strong class="text-gray-700">{{ __('db.Outcome:') }}</strong> {{ $level[5] }}</div>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-8 flex gap-3 justify-center flex-wrap">
                <a href="{{ route('courses.index') }}" class="btn-teal btn-base px-8">{{ __('db.View All Courses') }}</a>
                <a href="{{ route('register') }}" class="btn-gold btn-base px-8">{{ __('db.Register & Start') }}</a>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- OUR VALUES --}}
    {{-- ============================================================ --}}
    <section class="py-20 bg-cream">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="section-eyebrow">{{ __('db.What Drives Us') }}</span>
                <h2 class="section-title">{{ __('db.Our Core Values') }}</h2>
                <p class="section-subtitle">{{ __('db.Every decision at Sallaamti is guided by these principles drawn from the Quran and Sunnah.') }}</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ([
                ['📖', __('db.Knowledge (Ilm)'), __('db.Spreading authentic Quranic and Islamic knowledge to every Muslim, young and old, near and far.'), 'var(--teal)'],
                ['🤝', __('db.Brotherhood (Ukhuwwah)'), __('db.Building a community where every Muslim cares for their brother and sister in faith.'), 'var(--gold)'],
                ['💚', __('db.Compassion (Rahmah)'), __('db.Serving the needy, the orphan, the widow and the less fortunate with genuine care.'), '#16a34a'],
                ['🛡️', __('db.Trust (Amanah)'), __('db.Maintaining complete transparency and trust in all our programs, finances and relationships.'), '#1a5276'],
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
                <span class="section-eyebrow">{{ __('db.What We Offer') }}</span>
                <h2 class="section-title">{{ __('db.Our Programs & Services') }}</h2>
            </div>

            <div class="flex flex-wrap justify-center gap-3 mb-12">
                @foreach (['quran' => '📖 '.__('db.Quran Courses'), 'live' => '🎥 '.__('db.Live Classes'), 'nikah' => '💍 '.__('db.Nikah'), 'skills' => '💻 '.__('db.Skills')] as $key => $label)
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
                        <span class="section-eyebrow">{{ __('db.Self-Paced Learning') }}</span>
                        <h3 class="text-2xl font-bold mb-4">{{ __('db.Learn Quran at Your Own Pace') }}</h3>
                        <p class="text-gray-500 mb-6">{{ __('db.Our structured courses take you from basic Arabic alphabet all the way to deep Tafseer. Video lessons, quizzes, and a certificate upon completion.') }}</p>
                        <div class="space-y-3 my-6">
                            @foreach ([[__('db.Level 1'), __('db.Nazrah Quran'), __('db.6-12 months'), '📖'], [__('db.Level 2'), __('db.Tajweed'), __('db.6-8 months'), '🎵'], [__('db.Level 3'), __('db.Translation'), __('db.6-8 months'), '📝'], [__('db.Level 4'), __('db.Arabic Grammar'), __('db.4-6 months'), '🔤']] as $step)
                            <div class="flex items-center gap-3 bg-cream rounded-xl p-3">
                                <span class="text-2xl">{{ $step[3] }}</span>
                                <div>
                                    <strong class="text-gray-800 text-sm">{{ $step[0] }}: {{ $step[1] }}</strong>
                                    <p class="text-xs text-gray-400">{{ __('db.Duration: :duration', ['duration' => $step[2]]) }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="flex gap-3 flex-wrap">
                            <a href="{{ route('courses.index') }}" class="btn-teal btn-base px-6">{{ __('db.Browse All Courses') }}</a>
                            @guest
                            <a href="{{ route('register') }}" class="btn-gold btn-base px-6">{{ __('db.Join Free') }}</a>
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
                            <p class="text-sm">{{ __('db.Courses launching soon') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Live Tab --}}
            <div x-show="tab === 'live'" x-transition x-cloak>
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <span class="section-eyebrow">{{ __('db.Online Live Sessions') }}</span>
                        <h3 class="text-2xl font-bold mb-4">{{ __('db.Live Quran Classes with Expert Teachers') }}</h3>
                        <p class="text-gray-500 mb-6">{{ __('db.One-to-one and group live classes via Zoom. Fixed schedule, monthly subscription, personal attention for every student.') }}</p>
                        <div class="space-y-2 my-6">
                            @foreach ([__('db.5 classes per week, 30–45 mins each'), __('db.One-to-one OR group class options'), __('db.Male or Female teachers available'), __('db.Weekly tests + Monthly progress reports'), __('db.PKR 3,000–5,000/month | $25–$50 international')] as $f)
                            <div class="flex items-start gap-2 text-sm text-gray-600">
                                <span>✅</span> {{ $f }}
                            </div>
                            @endforeach
                        </div>
                        <a href="{{ route('quran-live.index') }}" class="btn-teal btn-base px-6">{{ __('db.View Live Courses') }}</a>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg border overflow-hidden">
                        <div class="flex items-center gap-1.5 px-4 py-3 border-b bg-gray-50">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-green-400"></span>
                            <span class="text-xs text-gray-500 ms-2">{{ __('db.Sallaamti Live Class — Tajweed Level 2') }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 p-5">
                            <div class="flex flex-col items-center gap-1"><span class="text-3xl">👩‍🏫</span><span class="text-xs text-gray-500">{{ __('db.Teacher') }}</span></div>
                            <div class="flex flex-col items-center gap-1"><span class="text-3xl">🧑‍🎓</span><span class="text-xs text-gray-500">{{ __('db.Student 1') }}</span></div>
                            <div class="flex flex-col items-center gap-1"><span class="text-3xl">👦</span><span class="text-xs text-gray-500">{{ __('db.Student 2') }}</span></div>
                            <div class="flex flex-col items-center gap-1"><span class="text-3xl">👧</span><span class="text-xs text-gray-500">{{ __('db.Student 3') }}</span></div>
                        </div>
                        <div class="flex items-center justify-between px-4 py-3 border-t bg-gray-50">
                            <span class="text-xs font-bold text-red-500">🔴 {{ __('db.LIVE') }}</span>
                            <span class="text-gray-400 text-xs">{{ __('db.Sallaamti.com · Secure Session') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Nikah Tab --}}
            <div x-show="tab === 'nikah'" x-transition x-cloak>
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <span class="section-eyebrow">{{ __('db.Islamic Matrimonial') }}</span>
                        <h3 class="text-2xl font-bold mb-4">{{ __('db.Find a Halal Match — Properly') }}</h3>
                        <p class="text-gray-500 mb-6">{{ __('db.No casual swiping. Every profile is CNIC-verified. Guardian-mediated contact only. Your photo is private until both sides accept.') }}</p>
                        <div class="space-y-3 my-6">
                            @foreach ([['🔒', __('db.CNIC Verified'), __('db.Every profile reviewed by our team')], ['👨‍👩‍👧', __('db.Guardian Mediated'), __('db.Contact only through guardian channels')], ['📸', __('db.Private Photos'), __('db.Photos visible only after mutual acceptance')], ['💳', __('db.Serious Members Only'), __('db.Small verification fee filters out non-serious')], ['🌍', __('db.Global Platform'), __('db.Muslims from Pakistan and worldwide')]] as $item)
                            <div class="flex items-start gap-3">
                                <span class="text-xl">{{ $item[0] }}</span>
                                <div><strong class="text-sm text-gray-800">{{ $item[1] }}</strong><p class="text-xs text-gray-400">{{ $item[2] }}</p></div>
                            </div>
                            @endforeach
                        </div>
                        <a href="{{ route('register') }}" class="btn-teal btn-base px-6">{{ __('db.Create Your Profile') }}</a>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg border overflow-hidden">
                        <div class="px-4 py-3 border-b bg-cream">
                            <span class="text-xs font-bold" style="color: var(--teal)">✅ {{ __('db.Verified Profile') }}</span>
                        </div>
                        <div class="flex items-center gap-4 p-5">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center text-3xl flex-shrink-0" style="background: var(--teal-light)">👤</div>
                            <div>
                                <h5 class="font-bold text-gray-800">{{ __('db.28 yrs · Karachi') }}</h5>
                                <p class="text-xs text-gray-500">{{ __('db.Education: Masters · Profession: Engineer') }}</p>
                                <p class="text-xs text-gray-500">{{ __('db.Sect: Sunni · Family: Nuclear') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3 border-t">
                            <div class="flex items-center gap-2 flex-1">
                                <div class="flex-1 bg-gray-200 rounded-full h-1.5"><div class="h-1.5 rounded-full" style="width:78%; background: var(--gold)"></div></div>
                                <span class="text-xs text-gray-500 flex-shrink-0">{{ __('db.78% Match') }}</span>
                            </div>
                            <span class="text-xs text-gray-400 ms-3">🔒 {{ __('db.Private') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Skills Tab --}}
            <div x-show="tab === 'skills'" x-transition x-cloak>
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <span class="section-eyebrow">{{ __('db.Presented by IZMA Digital Technology & Security') }}</span>
                        <h3 class="text-2xl font-bold mb-4">{{ __('db.Skills Training for Dignified Livelihood') }}</h3>
                        <p class="text-gray-500 mb-6">{{ __('db.Empowering every Muslim with practical digital skills that lead to halal income and independence.') }}</p>
                        <div class="flex flex-wrap gap-2 my-6">
                            @foreach ([
                                ['💻 '.__('db.Computer Skills'), 'Computer Skills'],
                                ['🎨 '.__('db.Graphic Design'), 'Graphic Design'],
                                ['🌐 '.__('db.Web Development'), 'Web Development'],
                                ['📱 '.__('db.Mobile Apps'), 'Mobile Apps'],
                                ['📈 '.__('db.Digital Marketing'), 'Digital Marketing'],
                            ] as [$label, $category])
                            <a href="{{ route('skills.index', ['category' => $category]) }}" class="text-xs font-medium px-3 py-1.5 rounded-full bg-cream text-gray-600 hover:bg-teal-50 hover:text-teal-700 transition">{{ $label }}</a>
                            @endforeach
                        </div>
                        <a href="{{ route('volunteer.create') }}" class="btn-teal btn-base px-6">{{ __('db.Join as Instructor Volunteer') }}</a>
                    </div>
                    <div class="text-center bg-cream rounded-2xl p-8">
                        <div class="text-5xl mb-3">💻</div>
                        <h4 class="font-bold text-gray-800 text-lg">{{ __('db.Free & Self-Paced') }}</h4>
                        <p class="text-gray-500 text-sm mb-4">{{ __('db.Learn at your own pace and earn a certificate — presented by IZMA Digital Technology & Security') }}</p>
                        <a href="{{ route('skills.index') }}" class="btn-gold btn-base px-6">{{ __('db.Explore Courses') }}</a>
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
                <span class="section-eyebrow">{{ __('db.Activities') }}</span>
                <h2 class="section-title">{{ __('db.What We Do at Sallaamti') }}</h2>
                <p class="section-subtitle">{{ __('db.From Quranic education to matrimonial services, family support to community giving — everything in one trusted platform.') }}</p>
            </div>
            <div class="grid lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ([
                [
                'icon' => 'fa-book-open',
                'color' => 'text-teal',
                'emoji' => '📖',
                'title' => __('db.Quran Learning'),
                'desc' => __('db.Structured self-paced Quran courses — Nazrah, Tajweed, Translation and Arabic Grammar — with quizzes and certificates.'),
                'url' => route('courses.index'),
                'btn' => __('db.Explore Courses'),
                'badge' => __('db.Live'),
                ],
                [
                'icon' => 'fa-laptop',
                'color' => 'text-gold',
                'emoji' => '🎥',
                'title' => __('db.Live Quran Classes'),
                'desc' => __('db.One-to-one and group live online classes with qualified teachers. Flexible timings for Pakistan and international students.'),
                'url' => route('quran-live.index'),
                'btn' => __('db.View Live Classes'),
                'badge' => __('db.Live'),
                ],
                [
                'icon' => 'fa-laptop-code',
                'color' => 'text-teal',
                'emoji' => '💻',
                'title' => __('db.Digital Skills'),
                'desc' => __('db.Free, self-paced skills training — Web Development, Graphic Design, Digital Marketing and more — presented by IZMA Digital Technology & Security.'),
                'url' => route('skills.index'),
                'btn' => __('db.Explore Skills'),
                'badge' => __('db.Live'),
                ],
                [
                'icon' => 'fa-heart',
                'color' => 'text-red-500',
                'emoji' => '💍',
                'title' => __('db.Sallaamti Nikah'),
                'desc' => __('db.A verified, CNIC-checked Islamic matrimonial platform. Guardian-mediated, photo-private and built on Islamic values.'),
                'url' => route('nikah.create'),
                'btn' => __('db.Create Profile'),
                'badge' => __('db.Live'),
                ],
                [
                'icon' => 'fa-hands-helping',
                'color' => 'text-purple-500',
                'emoji' => '💑',
                'title' => __('db.Family Support'),
                'desc' => __('db.Book a confidential session and our qualified counselors will guide you on marital, parenting, financial or spiritual matters.'),
                'url' => auth()->check() ? route('counseling.book.start') : route('register'),
                'btn' => auth()->check() ? __('db.Get Support') : __('db.Join & Get Support'),
                'badge' => __('db.Live'),
                ],
                [
                'icon' => 'fa-hand-holding-heart',
                'color' => 'text-gold',
                'emoji' => '💝',
                'title' => __('db.Donate & Support'),
                'desc' => __('db.Fund Quran education for orphans, support needy families and keep Sallaamti free for students worldwide.'),
                'url' => route('donate.create'),
                'btn' => __('db.Donate Now'),
                'badge' => __('db.Live'),
                ],
                [
                'icon' => 'fa-users',
                'color' => 'text-teal',
                'emoji' => '🤝',
                'title' => __('db.Volunteer with Us'),
                'desc' => __('db.Join as a Quran teacher, counselor, developer or outreach volunteer. Earn Sadaqah Jariyah while serving the Ummah.'),
                'url' => route('volunteer.create'),
                'btn' => __('db.Apply as Volunteer'),
                'badge' => __('db.Live'),
                ],
                [
                'icon' => 'fa-hand-holding-heart',
                'color' => 'text-teal',
                'emoji' => '🤲',
                'title' => __('db.Sallaamti Wall'),
                'desc' => __('db.Share dua requests, celebrate community stories, and post your own reflections — with admin-reviewed content so the feed stays genuine.'),
                'url' => route('wall.index'),
                'btn' => __('db.Visit the Wall'),
                'badge' => __('db.Live'),
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
                <span class="section-eyebrow text-gold">{{ __('db.Our Impact') }}</span>
                <h2 class="text-3xl font-extrabold text-white">{{ __('db.Growing Together, Alhamdulillah') }}</h2>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
                @foreach ([
                [\App\Models\User::count(), __('db.Registered Members'), '👥'],
                [\App\Models\Enrollment::count(), __('db.Course Enrollments'), '📖'],
                [\App\Models\Certificate::count(), __('db.Certificates Issued'), '🎓'],
                [\App\Models\NikahProfile::where('verification_status','verified')->count(), __('db.Verified Nikah Profiles'), '💍'],
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
                <span class="section-eyebrow">{{ __('db.Our Team') }}</span>
                <h2 class="section-title">{{ __('db.Meet the People Behind Sallaamti') }}</h2>
                <p class="section-subtitle">{{ __('db.Dedicated volunteers and professionals committed to serving the Ummah.') }}</p>
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
                    <span class="section-eyebrow text-gold">{{ __('db.Islamic Matrimonial') }}</span>
                    <h2 class="section-title text-white">{{ __('db.Marriage Is Half of Your Deen') }}</h2>
                    <p class="text-white/60 mb-4 italic">{{ __('db.The Prophet ﷺ said: "Whoever Allah blesses with a righteous spouse, He has helped him with half of his religion." — Ibn Hibban') }}</p>
                    <p class="text-white/60 mb-6">{{ __('db.Sallaamti\'s Nikah platform is built on Islamic values: verified profiles, guardian-mediated communication, complete privacy protection.') }}</p>
                    @guest
                    <a href="{{ route('register') }}" class="btn-gold btn-base px-8 py-4 inline-block font-semibold">{{ __('db.Create Your Profile →') }}</a>
                    @else
                    <a href="{{ route('nikah.show') }}" class="btn-gold btn-base px-8 py-4 inline-block font-semibold">{{ __('db.Go to Nikah Profile →') }}</a>
                    @endguest
                </div>
                <div class="space-y-3">
                    @foreach ([
                    ['1', __('db.Register & Create Profile'), __('db.Fill your profile with personal, educational and family details')],
                    ['2', __('db.Pay Verification Fee'), __('db.A small fee confirms you are serious and funds the platform')],
                    ['3', __('db.CNIC Verification'), __('db.Our team verifies your identity for a trusted community')],
                    ['4', __('db.Browse & Express Interest'), __('db.Find compatible matches and send an interest request')],
                    ['5', __('db.Guardian Contact'), __('db.After mutual acceptance, guardian contact details are shared')],
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
                    <span class="section-eyebrow">{{ __('db.Why Choose Us') }}</span>
                    <h2 class="section-title mb-6">{{ __('db.What Makes Sallaamti Different') }}</h2>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        {{ __('db.Unlike generic online platforms, every feature of Sallaamti is built specifically for the Muslim community — with Islamic values, privacy, and trust at the core.') }}
                    </p>
                    <div class="flex flex-col gap-4">
                        @foreach ([
                        ['✅', __('db.Completely Islamic'), __('db.Every module — from education to matrimonial — is built on Quranic principles and verified by Islamic scholars.')],
                        ['🔒', __('db.Privacy First'), __('db.No public photos on Nikah until mutual acceptance. CNIC verified members only. Your data is protected.')],
                        ['🌍', __('db.Truly Global'), __('db.Students from Pakistan, Middle East, UK, USA and beyond — all in one trusted community.')],
                        ['📜', __('db.Certified Learning'), __('db.Structured curriculum with weekly quizzes, monthly tests, and internationally recognized certificates.')],
                        ['🤲', __('db.Community Driven'), __('db.Volunteer teachers, counselors and supporters who genuinely care — not just a business transaction.')],
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
                <span class="section-eyebrow" style="color: rgba(255,255,255,0.6)">{{ __('db.Community Stories') }}</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-white mt-2">{{ __('db.What Our Community Says') }}</h2>
                <p class="text-white/50 text-sm mt-3">{{ __('db.Real experiences from students, parents and families around the world.') }}</p>
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

                                <div class="prose prose-invert prose-lg max-w-none text-white/90 leading-relaxed italic font-light mb-8">
                                    "{!! $t->content !!}"
                                </div>

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
                {{ __('db.Some names and details changed to protect privacy.') }}
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
            <h2 class="final-cta-title">{{ __('db.Be Part of the Sallaamti Family') }}</h2>
            <p class="final-cta-sub">{{ __('db.Join thousands of Muslims already learning, growing and connecting. Free to join — and your first step could change your life.') }}</p>
            <div class="flex gap-4 justify-center flex-wrap mt-8">
                @guest
                <a href="{{ route('register') }}" class="btn-base btn-gold text-lg px-10 py-4 font-semibold">
                    {{ __('db.Register Free Now') }} <i class="fa fa-arrow-right ml-2"></i>
                </a>
                <a href="{{ url('/contact') }}" class="btn-base btn-outline-light text-lg px-10 py-4">
                    {{ __('db.Contact Us') }}
                </a>
                @else
                <a href="{{ route('dashboard') }}" class="btn-base btn-gold text-lg px-10 py-4 font-semibold">
                    {{ __('db.Go to My Dashboard') }} <i class="fa fa-arrow-right ml-2"></i>
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