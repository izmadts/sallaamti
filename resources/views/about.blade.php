{{-- resources/views/about.blade.php --}}
<x-guest-layout>

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
                    <img src="{{ asset('img/about-1.jpg') }}" class="w-full h-full object-cover rounded-2xl wow zoomIn" data-wow-delay="0.1s" alt="Sallaamti community">
                    <div class="flex flex-col gap-4">
                        <img src="{{ asset('img/about-2.jpg') }}" class="w-full rounded-2xl wow zoomIn" data-wow-delay="0.2s" alt="">
                        <img src="{{ asset('img/about-3.jpg') }}" class="w-full rounded-2xl wow zoomIn" data-wow-delay="0.3s" alt="">
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
                        <img src="{{ asset('img/about-child.jpg') }}" class="w-16 h-16 rounded-full object-cover flex-shrink-0" alt="">
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
    {{-- WHAT WE DO --}}
    {{-- ============================================================ --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-12">
                <span class="section-eyebrow">Our Programs</span>
                <h2 class="section-title">How We Serve the Ummah</h2>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ([
                ['📖', 'Sallaamti Taaleem', 'var(--teal)', 'Structured Quran learning — Naazira, Tarjuma, Arabic Grammar, Seerah and Hadith — for all ages through self-paced video courses and live classes.', route('courses.index'), 'Learn More'],
                ['🎥', 'Live Quran Classes', '#1a5276', 'Online live classes with qualified male and female teachers via Zoom. Monthly subscription, flexible timings for Pakistan and international students.', route('quran-live.index'), 'View Classes'],
                ['💍', 'Sallaamti Nikah', '#c0392b', 'A halal, verified matrimonial platform with CNIC verification, guardian-mediated communication and complete privacy. Built for serious Muslim families.', route('register'), 'Create Profile'],
                ['💻', 'Sallaamti Skills', 'var(--gold)', 'Computer skills, graphic design, web development, digital marketing, tailoring, electrician training — empowering youth with halal livelihood skills.', '#', 'Coming Soon'],
                ['🛠️', 'Sallaamti Rozgar', '#16a34a', 'Employment support for the needy, orphans and deserving families — helping them start dignified small businesses with micro-financing guidance.', '#', 'Coming Soon'],
                ['👨‍👩‍👧', 'Sallaamti Waldain', '#7d3c98', 'Parenting coaching — helping family heads build strong, values-driven homes. Because strong roots make strong families and generations.', '#', 'Coming Soon'],
                ] as $prog)
                <div class="program-block p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-300 flex flex-col">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl mb-4" style="background: {{ $prog[1] }}22">
                        {{ $prog[0] }}
                    </div>
                    <h5 class="font-bold text-gray-800 mb-2">{{ $prog[1] === '#' ? $prog[2] : $prog[2] }}</h5>
                    <p class="text-gray-500 text-sm leading-relaxed flex-1">{{ $prog[3] }}</p>
                    @if ($prog[5] === 'Coming Soon')
                    <span class="mt-4 inline-block text-xs font-semibold px-3 py-1 rounded-full" style="background: #f0f0f0; color: #888">🚀 Coming Soon</span>
                    @else
                    <a href="{{ $prog[4] }}" class="btn-base btn-teal inline-block mt-4 text-sm px-5 py-2 self-start">{{ $prog[5] }}</a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- IMPACT NUMBERS --}}
    {{-- ============================================================ --}}
    <section class="py-20" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%)">
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
                    <div class="text-3xl md:text-4xl font-extrabold mb-1" style="color: var(--gold)">{{ number_format($stat[0]) }}+</div>
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

            {{-- Featured Leader --}}
            <div class="grid lg:grid-cols-12 gap-8 mb-12 items-center">
                <div class="lg:col-span-4 xl:col-span-4 wow zoomIn" data-wow-delay="0.1s">
                    <div class="relative">
                        <img src="{{ asset('img/team-1.jpg') }}" class="w-full rounded-2xl shadow-lg" alt="Sallaamti Founder">
                        <div class="absolute -bottom-4 -right-4 w-20 h-20 rounded-2xl flex items-center justify-center text-3xl shadow-lg" style="background: var(--gold)">🕌</div>
                    </div>
                </div>
                <div class="lg:col-span-8 xl:col-span-8 wow fadeIn" data-wow-delay="0.2s">
                    <span class="section-eyebrow">Founder & Director</span>
                    <h2 class="text-3xl font-extrabold text-gray-800 mb-1">Mubashar Ahmed</h2>
                    <p class="italic mb-4" style="color: var(--teal)">Founder, Sallaamti</p>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        With a deep passion for Islamic education and community service, Mubashar founded Sallaamti with the vision of creating a platform where every Muslim — regardless of age, location or background — can access quality Quranic education, find a halal spouse, and build a life aligned with the Quran and Sunnah.
                    </p>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        "Our goal is not just to teach the Quran — it is to help Muslims live it. اقرأ، افهم، وطبّق — Read, Understand, and Implement."
                    </p>
                    <div class="flex gap-3">
                        <a href="{{ setting('social_facebook', '#') }}" class="social-btn" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="{{ setting('social_instagram', '#') }}" class="social-btn" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="{{ setting('social_tiktok', '#') }}" class="social-btn" target="_blank"><i class="fab fa-tiktok"></i></a>
                        <a href="https://wa.me/{{ setting('social_whatsapp') }}" class="social-btn" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>

            {{-- Team Grid --}}
            @if ($teamMembers->isNotEmpty())
            <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($teamMembers as $member)
                <div class="team-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 wow zoomIn" data-wow-delay="0.1s">
                    <div class="relative overflow-hidden">
                        @if ($member->photo)
                        <img src="{{ Storage::url($member->photo) }}" class="w-full h-52 object-cover" alt="{{ $member->name }}">
                        @else
                        <div class="w-full h-52 flex items-center justify-center text-4xl" style="background: var(--teal-light)">👤</div>
                        @endif
                    </div>
                    <div class="p-4 text-center">
                        <h5 class="font-bold text-gray-800 mb-0">{{ $member->name }}</h5>
                        <p class="text-sm font-semibold mb-2" style="color: var(--teal)">{{ $member->role }}</p>
                        @if ($member->bio)
                        <div class="prose prose-sm max-w-none text-gray-500">{!! $member->bio !!}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
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
                        <img src="{{ asset('img/about-4.jpg') }}" class="w-full h-56 object-cover wow zoomIn" data-wow-delay="0.1s" alt="">
                    </div>
                    <div class="rounded-2xl overflow-hidden">
                        <img src="{{ asset('img/about-5.jpg') }}" class="w-full h-40 object-cover wow zoomIn" data-wow-delay="0.2s" alt="">
                    </div>
                    <div class="rounded-2xl overflow-hidden">
                        <img src="{{ asset('img/about-6.jpg') }}" class="w-full h-40 object-cover wow zoomIn" data-wow-delay="0.3s" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>

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