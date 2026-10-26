<x-guest-layout :title="__('db.Nikah Counselor Guide — How to Earn with Sallaamti')" :description="__('db.The complete, step-by-step guide to becoming a Sallaamti Nikah Counselor — registration, finding people online, adding clients, and exactly how commission is calculated.')">

    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 260px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="max-w-7xl mx-auto px-4 py-16 relative z-10 text-center w-full">
            <span class="section-eyebrow" style="color: #d8c48a">{{ __('db.Become a Nikah Counselor') }}</span>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mt-2">{{ __('db.The Complete Nikah Counselor Guide') }}</h1>
            <p class="text-gray-300 mt-3 max-w-2xl mx-auto">{{ __('db.From registration, to finding people who need a rishta, to registering them on Sallaamti, to exactly how much commission you earn — every step, in one place.') }}</p>
            <div class="flex flex-wrap justify-center gap-3 mt-6">
                @auth
                    @if (auth()->user()->hasRole('matchmaker'))
                        <a href="{{ route('dashboard.matchmaker') }}" class="btn-base px-6 py-3" style="background: #b8962e; color:#fff">{{ __('db.Go to My Dashboard') }} →</a>
                    @else
                        <a href="{{ route('nikah-counselor.create') }}" class="btn-base px-6 py-3" style="background: #b8962e; color:#fff">{{ __('db.Apply Now') }} →</a>
                    @endif
                @else
                    <a href="{{ route('nikah-counselor.create') }}" class="btn-base px-6 py-3" style="background: #b8962e; color:#fff">{{ __('db.Apply Now') }} →</a>
                    <a href="{{ route('login') }}" class="btn-base px-6 py-3 border border-white/30 text-white hover:bg-white/10">{{ __('db.Already Applied? Log In') }}</a>
                @endauth
            </div>
        </div>
    </section>

    <div x-data="{ tab: 'overview' }" class="bg-cream">

        {{-- ===== Tab bar ===== --}}
        <div class="sticky top-[57px] z-30 bg-white border-b shadow-sm">
            <div class="max-w-6xl mx-auto px-2 overflow-x-auto no-scrollbar">
                <div class="flex gap-1 py-2 min-w-max">
                    @foreach ([
                        ['overview', '🎯', __('db.Overview')],
                        ['register', '📝', __('db.Registration')],
                        ['find', '📣', __('db.Find People Online')],
                        ['client', '👤', __('db.Register a Client')],
                        ['commission', '💰', __('db.Commission & Earnings')],
                        ['match', '🤝', __('db.Matchmaking Process')],
                        ['rules', '📜', __('db.Rules to Follow')],
                    ] as [$key, $emoji, $label])
                    <button type="button" @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}' ? 'bg-[--teal] text-white' : 'text-gray-600 hover:bg-gray-100'"
                        class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition flex items-center gap-1.5">
                        <span>{{ $emoji }}</span> {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-4 py-12">

            {{-- ============================================================ --}}
            {{-- TAB 1 — OVERVIEW --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'overview'" x-cloak>
                <h2 class="text-2xl font-bold text-[--text-dark] mb-2">{{ __('db.What does a Sallaamti Nikah Counselor actually do?') }}</h2>
                <p class="text-gray-600 mb-8 max-w-3xl">{{ __('db.A Nikah Counselor is Sallaamti\'s representative in your city or community. You help people who are genuinely looking for a halal rishta — understand their situation, guide them onto the Sallaamti platform, and support them until they\'re matched. In return, Sallaamti pays you a commission for every verified profile and every matchmaking package you bring in.') }}</p>

                <div class="grid md:grid-cols-3 gap-5 mb-10">
                    @foreach ([
                        ['🔍', __('db.Find genuine seekers'), __('db.In Facebook/Instagram groups, your own community, or people who come to you directly — real people, really looking for a rishta.')],
                        ['🧭', __('db.Guide them onto Sallaamti'), __('db.Either help them build their own profile, or register them yourself — explained fully in the "Register a Client" tab.')],
                        ['💵', __('db.Earn two ways'), __('db.A fixed amount for every verified Sallaamti profile, plus a percentage every time your client buys a matchmaking package — see "Commission & Earnings".')],
                    ] as $item)
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <span class="text-3xl">{{ $item[0] }}</span>
                        <h3 class="font-bold text-[--text-dark] mt-3">{{ $item[1] }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $item[2] }}</p>
                    </div>
                    @endforeach
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <p class="font-semibold text-[--text-dark]">{{ __('db.Mobile app is on its way') }}</p>
                        <p class="text-sm text-gray-500">{{ __('db.The Nikah Counselor mobile app isn\'t publicly live yet. Until then, share Sallaamti with prospects using the website link and your own WhatsApp number — both work fully today.') }}</p>
                    </div>
                    <a href="{{ whatsapp_link() }}" target="_blank" class="btn-base px-5 py-2.5 flex-shrink-0 border border-gray-300 text-gray-700 hover:border-[--teal]"><i class="fab fa-whatsapp mr-2 text-green-600"></i>{{ __('db.Sallaamti WhatsApp') }}</a>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- TAB 2 — REGISTRATION --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'register'" x-cloak>
                <h2 class="text-2xl font-bold text-[--text-dark] mb-2">{{ __('db.How to become a certified Nikah Counselor') }}</h2>
                <p class="text-gray-600 mb-8 max-w-3xl">{{ __('db.You must be certified before you can earn commission — this protects every client\'s privacy and keeps Sallaamti\'s rishta network trustworthy. Here is every stage your application goes through, in order.') }}</p>

                <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
                    <h3 class="font-bold text-[--text-dark] mb-3">{{ __('db.Step 1 — Apply online') }}</h3>
                    <p class="text-sm text-gray-500 mb-4">{{ __('db.The application takes about 5 minutes. You\'ll need:') }}</p>
                    <div class="grid sm:grid-cols-2 gap-x-8 gap-y-2 text-sm text-gray-700 mb-5">
                        @foreach ([
                            __('db.Full name & Father/Husband/Guardian name'),
                            __('db.Mobile and WhatsApp number'),
                            __('db.Gender, age (21–70), marital status'),
                            __('db.Qualification'),
                            __('db.Country, city & address'),
                            __('db.CNIC number + a clear selfie photo'),
                            __('db.Payout method (bank / Easypaisa / JazzCash) & account details'),
                        ] as $req)
                        <div class="flex items-start gap-2"><i class="fa fa-check text-[--teal] mt-1"></i><span>{{ $req }}</span></div>
                        @endforeach
                    </div>
                    <a href="{{ route('nikah-counselor.create') }}" class="btn-base btn-teal px-5 py-2.5 inline-block">{{ __('db.Start the Application') }} →</a>
                </div>

                <h3 class="font-bold text-[--text-dark] mb-4">{{ __('db.Step 2 — What happens after you apply') }}</h3>
                <p class="text-sm text-gray-500 mb-5">{{ __('db.Every application moves through the same checkpoints, in this order. Nothing is skipped — this is what makes a Sallaamti counselor ID trustworthy to clients.') }}</p>

                <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
                    <div class="space-y-0">
                        @foreach ([
                            [__('db.Application Received'), __('db.Your application lands with Sallaamti\'s admin team for review.')],
                            [__('db.Identity Verified'), __('db.Your CNIC and selfie are checked against your submitted details.')],
                            [__('db.References Checked'), __('db.A basic background/reference check is carried out.')],
                            [__('db.Interviewed'), __('db.A short call or meeting with the Sallaamti team.')],
                            [__('db.Nikah Counselor Agreement Signed'), __('db.A digital agreement link is sent to you — review and accept it.')],
                            [__('db.Privacy / NDA Signed'), __('db.You formally commit to keeping every client\'s information confidential.')],
                            [__('db.Training'), __('db.You\'re walked through the platform, the rules, and how to use your counselor tools.')],
                            [__('db.Assessment Passed'), __('db.A short check to confirm you understood the training.')],
                            [__('db.Probation'), __('db.A short settling-in period while you handle your first few real cases.')],
                            [__('db.Certified Nikah Counselor'), __('db.You\'re fully certified — your counselor ID, referral link and QR code go live, and every eligible commission starts counting.')],
                        ] as $i => $step)
                        <div class="flex items-start gap-4 {{ !$loop->last ? 'pb-5 mb-5 border-b border-gray-100' : '' }}">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold {{ $loop->last ? 'bg-[--teal] text-white' : 'bg-gray-100 text-gray-500' }}">{{ $i + 1 }}</span>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $step[0] }}</p>
                                <p class="text-sm text-gray-500">{{ $step[1] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                    <p class="text-sm text-gray-600 mb-1">{{ __('db.Track exactly which stage your own application is on at any time.') }}</p>
                    <a href="{{ route('login') }}" class="text-[--teal] font-semibold text-sm">{{ __('db.Log in to check your status') }} →</a>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- TAB 3 — FIND PEOPLE ONLINE --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'find'" x-cloak>
                <h2 class="text-2xl font-bold text-[--text-dark] mb-2">{{ __('db.Where to find people who genuinely need a rishta') }}</h2>
                <p class="text-gray-600 mb-8 max-w-3xl">{{ __('db.Your job isn\'t to message hundreds of strangers. It\'s to find people who are already looking, earn their trust, and guide them — properly, respectfully — onto Sallaamti. Your rule: understand first, help second, recommend Sallaamti third.') }}</p>

                <h3 class="font-bold text-[--text-dark] mb-4">{{ __('db.1. Where genuine seekers actually are') }}</h3>
                <div class="grid sm:grid-cols-2 gap-5 mb-10">
                    @foreach ([
                        ['📘', __('db.Facebook Groups'), __('db.Search your city + "Rishta", "Marriage Proposal", "Matrimonial", or "Muslim Marriage" — e.g. "Lahore Rishta", "Pakistani Matrimonial", "Overseas Pakistani Rishta", "Divorced, Widowed & Second Marriage Proposals".')],
                        ['💬', __('db.Comments on public posts'), __('db.Answer genuine questions in the comments of public posts, politely and helpfully — not by mass-inboxing everyone.')],
                        ['🎥', __('db.Instagram & TikTok'), __('db.Follow pages about marriage/rishta/Pakistani families. Reply usefully to comments, post your own helpful short videos.')],
                        ['📱', __('db.WhatsApp & your own network'), __('db.Tell friends, relatives and your local community you\'re a certified Sallaamti Nikah Counselor — trusted referrals from people who already know you convert best.')],
                    ] as $item)
                    <div class="bg-white rounded-2xl shadow-sm p-5">
                        <span class="text-2xl">{{ $item[0] }}</span>
                        <h4 class="font-semibold text-gray-800 mt-2">{{ $item[1] }}</h4>
                        <p class="text-sm text-gray-500 mt-1">{{ $item[2] }}</p>
                    </div>
                    @endforeach
                </div>

                <h3 class="font-bold text-[--text-dark] mb-4">{{ __('db.2. How to tell a genuine seeker from a random post') }}</h3>
                <div class="bg-white rounded-2xl shadow-sm overflow-x-auto mb-10">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-left">
                            <tr>
                                <th class="px-5 py-3 font-semibold">{{ __('db.What you see') }}</th>
                                <th class="px-5 py-3 font-semibold">{{ __('db.What to do') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ([
                                [__('db."Looking for a rishta, serious families only"'), __('db.Ask politely whether they\'d like help building an online profile.')],
                                [__('db."Suggest a trustworthy platform for rishta"'), __('db.Introduce Sallaamti and share the official website link.')],
                                [__('db."Settled abroad, looking for a match back home"'), __('db.Explain the available filters and real features — don\'t oversell.')],
                                [__('db."Second marriage / divorced, looking again"'), __('db.Reply with respect and privacy — never ask for sensitive details publicly.')],
                                [__('db."Looking for my son/daughter"'), __('db.Guide them on building a profile with the family\'s consent.')],
                            ] as $row)
                            <tr>
                                <td class="px-5 py-3 text-gray-700">{{ $row[0] }}</td>
                                <td class="px-5 py-3 text-gray-500">{{ $row[1] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <p class="text-xs text-gray-400 p-4 border-t">{{ __('db.Important: never save or forward someone\'s personal details, photos or phone number from a public post without their clear permission. Let people reach out to you, or ask first.') }}</p>
                </div>

                <h3 class="font-bold text-[--text-dark] mb-4">{{ __('db.3. What to say') }}</h3>
                <div class="space-y-4 mb-10">
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <p class="text-xs font-bold text-[--teal] uppercase tracking-wide mb-2">{{ __('db.Replying under a public post') }}</p>
                        <p class="text-sm text-gray-600 italic leading-relaxed">{{ __('db.Assalamu Alaikum! May Allah make this easy for you. If you\'d like a more organized way to look for a rishta, you can also check out Sallaamti Nikah — you can see the available packages and build a profile on the website. If you\'d like, I can personally guide you through it as a certified Sallaamti Nikah Counselor.') }}</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <p class="text-xs font-bold text-[--teal] uppercase tracking-wide mb-2">{{ __('db.When someone messages you directly') }}</p>
                        <p class="text-sm text-gray-600 italic leading-relaxed">{{ __('db.Assalamu Alaikum! Thank you for reaching out. I\'m a certified Sallaamti Nikah Counselor — we help people build a proper profile and guide them through the whole process, in sha Allah. Are you looking for yourself, or for someone in your family? I\'ll explain the packages and how verification works.') }}</p>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <p class="text-xs font-bold text-[--teal] uppercase tracking-wide mb-2">{{ __('db.When they ask you to help them register') }}</p>
                        <p class="text-sm text-gray-600 italic leading-relaxed">{{ __('db.Of course! There are two ways: I can send you a direct link to register yourself on Sallaamti, or — if you\'d prefer — I can help you set it up personally. Please never send me your CNIC, photos or payment in a regular chat; everything is submitted only through the official Sallaamti platform.') }}</p>
                    </div>
                </div>

                <h3 class="font-bold text-[--text-dark] mb-4">{{ __('db.4. A simple 30-day plan') }}</h3>
                <div class="grid sm:grid-cols-2 gap-5 mb-10">
                    @foreach ([
                        [__('db.Days 1–7 — Build your presence'), [__('db.Make your Facebook profile look professional, mention you\'re a Sallaamti Nikah Counselor.'), __('db.Join 10 relevant Facebook groups.'), __('db.Leave 2 genuinely helpful comments a day.')]],
                        [__('db.Days 8–14 — Earn trust'), [__('db.Post about common mistakes people make looking for a rishta.'), __('db.Share how verification and privacy work on Sallaamti.'), __('db.Keep a simple record of everyone you\'ve talked to.')]],
                        [__('db.Days 15–21 — Turn interest into registration'), [__('db.Follow up (politely, with permission) with interested people.'), __('db.Walk them through building their profile.'), __('db.Help undecided families pick the right package.')]],
                        [__('db.Days 22–30 — Review & improve'), [__('db.See which group or page brought the best results.'), __('db.Check your registration/verification numbers in My Performance.'), __('db.Reuse what worked; drop what didn\'t.')]],
                    ] as $phase)
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h4 class="font-bold text-gray-800 mb-3">{{ $phase[0] }}</h4>
                        <ul class="space-y-2 text-sm text-gray-500">
                            @foreach ($phase[1] as $line)
                            <li class="flex items-start gap-2"><span class="text-[--teal]">•</span><span>{{ $line }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                    @endforeach
                </div>

                <h3 class="font-bold text-[--text-dark] mb-4">{{ __('db.3 mistakes that will hurt you') }}</h3>
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <div class="space-y-4">
                        @foreach ([
                            [__('db.Don\'t mass-message or spam unrelated groups'), __('db.The same copy-pasted message to hundreds of people gets you blocked and reported — it also damages Sallaamti\'s name.')],
                            [__('db.Never use someone\'s post/photo/number without permission'), __('db.Even a public rishta post is personal — don\'t copy it into your own records or ads without asking first.')],
                            [__('db.Never guarantee a match or a successful nikah'), __('db.You can explain why a profile seems compatible — you can never promise marriage, character, or outcome.')],
                        ] as $m)
                        <div class="flex items-start gap-3">
                            <span class="text-xl flex-shrink-0">🚫</span>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $m[0] }}</p>
                                <p class="text-sm text-gray-500">{{ $m[1] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- TAB 4 — REGISTER A CLIENT --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'client'" x-cloak>
                <h2 class="text-2xl font-bold text-[--text-dark] mb-2">{{ __('db.How to turn someone you found into a Sallaamti client') }}</h2>
                <p class="text-gray-600 mb-8 max-w-3xl">{{ __('db.There are exactly two ways to bring someone onto Sallaamti — and in both, the commission is attributed to you automatically.') }}</p>

                <div class="grid md:grid-cols-2 gap-6 mb-10">
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-t-4 border-[--teal]">
                        <span class="text-2xl">🔗</span>
                        <h3 class="font-bold text-gray-800 mt-2 mb-1">{{ __('db.A — They register themselves, using your link') }}</h3>
                        <p class="text-sm text-gray-500 mb-4">{{ __('db.Once you\'re certified, your personal counselor ID and referral link appear under "My Performance" in your dashboard. Share that link (or its QR code) with anyone you\'ve guided towards Sallaamti.') }}</p>
                        <ol class="space-y-2 text-sm text-gray-600 list-decimal list-inside">
                            <li>{{ __('db.Share your link or QR code with them directly — not in a public post.') }}</li>
                            <li>{{ __('db.They open it, create a free account and build their own Nikah profile.') }}</li>
                            <li>{{ __('db.They pay the Sallaamti Verified profile fee through the official platform — never to you.') }}</li>
                            <li>{{ __('db.Once their payment is confirmed, the commission is attributed to you automatically.') }}</li>
                        </ol>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm p-6 border-t-4" style="border-color:#b8962e">
                        <span class="text-2xl">🧑‍💼</span>
                        <h3 class="font-bold text-gray-800 mt-2 mb-1">{{ __('db.B — You register them yourself (walk-in)') }}</h3>
                        <p class="text-sm text-gray-500 mb-4">{{ __('db.For someone who\'d rather you handle it — a family that visits you directly, for example.') }}</p>
                        <ol class="space-y-2 text-sm text-gray-600 list-decimal list-inside">
                            <li>{{ __('db.Open "My Clients" in your counselor dashboard and add them as a new client.') }}</li>
                            <li>{{ __('db.Fill in their details with their consent, exactly as they tell you.') }}</li>
                            <li>{{ __('db.Their payment is made and confirmed only through Sallaamti\'s official channels — never cash to you personally.') }}</li>
                            <li>{{ __('db.Because you created the profile, the commission is attributed to you directly.') }}</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                    <p class="font-semibold text-gray-800 mb-1">⚠️ {{ __('db.A rule that protects you too') }}</p>
                    <p class="text-sm text-gray-500">{{ __('db.Never collect CNIC images or payment yourself, and never keep a client\'s information in your own personal notes or spreadsheets — everything goes through Sallaamti\'s system directly. This is also what the Nikah Counselor Code of Conduct requires.') }}</p>
                    <a href="{{ route('nikah-counselor.code-of-conduct') }}" class="text-[--teal] text-sm font-semibold mt-2 inline-block">{{ __('db.Read the full Code of Conduct') }} →</a>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                    <p class="text-sm text-gray-600 mb-1">{{ __('db.Your referral link, QR code and client list live in your own counselor dashboard once certified.') }}</p>
                    <a href="{{ route('login') }}" class="text-[--teal] font-semibold text-sm">{{ __('db.Log in to find your link') }} →</a>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- TAB 5 — COMMISSION & EARNINGS --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'commission'" x-cloak>
                <h2 class="text-2xl font-bold text-[--text-dark] mb-2">{{ __('db.Exactly how your commission is calculated') }}</h2>
                <p class="text-gray-600 mb-2 max-w-3xl">{{ __('db.These are the live, admin-approved rates currently active on Sallaamti — not an estimate. You earn in two separate ways, and both can happen on the very same client.') }}</p>
                <p class="text-xs text-gray-400 mb-8">{{ __('db.Every commission is held for 7 days after payment is confirmed before it becomes payable — this covers refunds/cancellations — then it moves to your payout account.') }}</p>

                <h3 class="font-bold text-[--text-dark] mb-1">{{ __('db.1. Verified Profile commission — one-time, fixed amount') }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ __('db.Paid the moment a Sallaamti Verified profile fee (currently Rs :amount) is confirmed as paid — whichever tier you\'re on when it\'s confirmed.', ['amount' => number_format((float) $verificationFee)]) }}</p>
                <div class="bg-white rounded-2xl shadow-sm overflow-x-auto mb-8">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-left">
                            <tr>
                                <th class="px-5 py-3 font-semibold">{{ __('db.Your Tier') }}</th>
                                <th class="px-5 py-3 font-semibold">{{ __('db.You Earn Per Verified Profile') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($tiers as $tier)
                            <tr>
                                <td class="px-5 py-3 text-gray-700 font-medium">{{ $tier['label'] }}</td>
                                <td class="px-5 py-3 text-[--teal] font-bold">{{ $tier['verified_amount'] !== null ? 'Rs ' . number_format((float) $tier['verified_amount']) : '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <h3 class="font-bold text-[--text-dark] mb-1">{{ __('db.2. Matchmaking Package commission — a percentage') }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ __('db.Paid when a matchmaking package is purchased or renewed by a client assigned to you — "First Purchase" the first time, "Renewal" every time after that. The same rate applies to every package type.') }}</p>
                <div class="bg-white rounded-2xl shadow-sm overflow-x-auto mb-8">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-left">
                            <tr>
                                <th class="px-5 py-3 font-semibold">{{ __('db.Your Tier') }}</th>
                                <th class="px-5 py-3 font-semibold">{{ __('db.First Purchase') }}</th>
                                <th class="px-5 py-3 font-semibold">{{ __('db.Renewal') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($tiers as $tier)
                            <tr>
                                <td class="px-5 py-3 text-gray-700 font-medium">{{ $tier['label'] }}</td>
                                <td class="px-5 py-3 text-[--teal] font-bold">{{ $tier['package_first_pct'] !== null ? rtrim(rtrim(number_format((float) $tier['package_first_pct'], 2), '0'), '.') . '%' : '—' }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $tier['package_renewal_pct'] !== null ? rtrim(rtrim(number_format((float) $tier['package_renewal_pct'], 2), '0'), '.') . '%' : '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <h3 class="font-bold text-[--text-dark] mb-4">{{ __('db.Current package prices (for reference)') }}</h3>
                <div class="grid sm:grid-cols-3 gap-5 mb-10">
                    @foreach ($packages as $package)
                    <div class="bg-white rounded-2xl shadow-sm p-5 text-center">
                        <p class="font-semibold text-gray-800">{{ $package->name }}</p>
                        <p class="text-2xl font-extrabold text-[--teal] mt-1">Rs {{ number_format((float) $package->price) }}</p>
                        @if ($package->duration_days)
                        <p class="text-xs text-gray-400 mt-1">{{ __('db.Valid for :days days', ['days' => $package->duration_days]) }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>

                <h3 class="font-bold text-[--text-dark] mb-4">{{ __('db.A simple example') }}</h3>
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-10">
                    <p class="text-sm text-gray-600 leading-relaxed">{{ __('db.You\'re a Nikah Counselor. In one month, you help 10 people complete a Verified profile, and one of your clients also buys the Assisted Nikah Counseling package as their first purchase.') }}</p>
                    <ul class="mt-4 space-y-1 text-sm">
                        <li>• {{ __('db.10 Verified profiles × Rs :rate = Rs :total', ['rate' => number_format((float) ($tiers->firstWhere('key', 'nikah_counselor')['verified_amount'] ?? 200)), 'total' => number_format((float) ($tiers->firstWhere('key', 'nikah_counselor')['verified_amount'] ?? 200) * 10)]) }}</li>
                        <li>• {{ __('db.1 Assisted Nikah Counseling package (Rs 5,000) × :pct% first purchase = Rs :amount', ['pct' => rtrim(rtrim(number_format((float) ($tiers->firstWhere('key', 'nikah_counselor')['package_first_pct'] ?? 20), 2), '0'), '.'), 'amount' => number_format(5000 * (float) ($tiers->firstWhere('key', 'nikah_counselor')['package_first_pct'] ?? 20) / 100)]) }}</li>
                    </ul>
                </div>

                <h3 class="font-bold text-[--text-dark] mb-1">{{ __('db.Your rate goes up automatically as you grow') }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ __('db.You don\'t need to ask for a promotion — Sallaamti checks these three numbers automatically every day. The moment you clear all three for the next tier, your commission rate increases on every future case.') }}</p>
                <div class="bg-white rounded-2xl shadow-sm overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-left">
                            <tr>
                                <th class="px-5 py-3 font-semibold">{{ __('db.Next Tier') }}</th>
                                <th class="px-5 py-3 font-semibold">{{ __('db.Verified Profiles Needed') }}</th>
                                <th class="px-5 py-3 font-semibold">{{ __('db.Quality Score') }}</th>
                                <th class="px-5 py-3 font-semibold">{{ __('db.Minimum Days Certified') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($tiers as $tier)
                            @if ($tier['threshold'])
                            <tr>
                                <td class="px-5 py-3 text-gray-700 font-medium">{{ $tier['label'] }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $tier['threshold']['verified'] }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $tier['threshold']['quality_score'] }}+</td>
                                <td class="px-5 py-3 text-gray-600">{{ $tier['threshold']['tenure_days'] }}</td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-center mt-8">
                    @auth
                        @if (auth()->user()->hasRole('matchmaker'))
                        <a href="{{ route('matchmaker.commissions.index') }}" class="text-[--teal] font-semibold text-sm">{{ __('db.See your own commission history') }} →</a>
                        @endif
                    @else
                    <a href="{{ route('login') }}" class="text-[--teal] font-semibold text-sm">{{ __('db.Log in to see your own commission history') }} →</a>
                    @endauth
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- TAB 6 — MATCHMAKING PROCESS --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'match'" x-cloak>
                <h2 class="text-2xl font-bold text-[--text-dark] mb-2">{{ __('db.What happens after someone becomes your client') }}</h2>
                <p class="text-gray-600 mb-8 max-w-3xl">{{ __('db.Once a client is assigned to you — whichever path you used — everything from here happens inside your "My Clients" area.') }}</p>

                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <div class="space-y-0">
                        @foreach ([
                            [__('db.Understand what they\'re looking for'), __('db.Record their partner preferences and requirements against their client file.')],
                            [__('db.Search verified profiles'), __('db.Browse Sallaamti\'s verified Nikah profiles for genuinely compatible matches.')],
                            [__('db.Build a shortlist'), __('db.Put together a batch of suggested profiles for your client.')],
                            [__('db.Send the batch'), __('db.Send your shortlist to your client for their review.')],
                            [__('db.Record their consent'), __('db.Your client accepts or declines interest in each suggestion — nothing moves forward without their clear consent.')],
                            [__('db.Guarded messaging begins'), __('db.Once both sides consent, a private, moderated conversation opens between them.')],
                            [__('db.Stay in touch'), __('db.Keep notes, follow up, and support your client until — in sha Allah — they\'re matched.')],
                        ] as $i => $step)
                        <div class="flex items-start gap-4 {{ !$loop->last ? 'pb-5 mb-5 border-b border-gray-100' : '' }}">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold bg-gray-100 text-gray-500">{{ $i + 1 }}</span>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $step[0] }}</p>
                                <p class="text-sm text-gray-500">{{ $step[1] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6 text-center mt-6">
                    <p class="text-sm text-gray-600 mb-1">{{ __('db.This whole flow lives in your counselor dashboard once you\'re certified.') }}</p>
                    <a href="{{ route('login') }}" class="text-[--teal] font-semibold text-sm">{{ __('db.Log in to open My Clients') }} →</a>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- TAB 7 — RULES --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'rules'" x-cloak>
                <h2 class="text-2xl font-bold text-[--text-dark] mb-2">{{ __('db.The rules every Sallaamti Nikah Counselor follows') }}</h2>
                <p class="text-gray-600 mb-8 max-w-3xl">{{ __('db.These exist to protect your clients, and to protect you. Breaking them puts your certification at risk.') }}</p>

                <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
                    <div class="space-y-5">
                        @foreach ([
                        ['🔒', __('db.Never collect CNIC over WhatsApp'), __('db.Identity documents go straight into Sallaamti\'s secure system — never through your personal phone.')],
                        ['💳', __('db.Never collect payment in cash'), __('db.All payments go through Sallaamti directly. Asking a client to pay you personally breaks the rules.')],
                        ['🤐', __('db.Keep client information confidential'), __('db.No personal spreadsheets, no private contact lists — client information stays inside Sallaamti\'s system only.')],
                        ['🚫', __('db.Never guarantee a match'), __('db.You can explain why a profile seems compatible — you cannot promise or guarantee marriage, character, or outcome.')],
                        ['⚖️', __('db.Treat every client fairly'), __('db.No discrimination, no harassment, no pressure.')],
                        ['🏢', __('db.You represent Sallaamti, not yourself'), __('db.Every client relationship belongs to Sallaamti as an organization — not to you individually.')],
                        ] as $item)
                        <div class="flex items-start gap-4">
                            <span class="text-2xl flex-shrink-0">{{ $item[0] }}</span>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $item[1] }}</p>
                                <p class="text-sm text-gray-500">{{ $item[2] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                    <a href="{{ route('nikah-counselor.code-of-conduct') }}" class="btn-base btn-teal px-6 py-3 inline-block">{{ __('db.Read the Full Code of Conduct') }} →</a>
                </div>
            </div>

        </div>

        {{-- Bottom CTA --}}
        <div class="py-14" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
            <div class="max-w-3xl mx-auto px-4 text-center">
                <h3 class="text-2xl font-bold text-white mb-2">{{ __('db.Ready to start earning as a Nikah Counselor?') }}</h3>
                <p class="text-gray-300 mb-6">{{ __('db.Applications are reviewed by Sallaamti\'s admin team — the sooner you apply, the sooner you\'re certified.') }}</p>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="{{ route('nikah-counselor.create') }}" class="btn-base px-6 py-3" style="background: #b8962e; color:#fff">{{ __('db.Apply Now') }} →</a>
                    <a href="{{ whatsapp_link() }}" target="_blank" class="btn-base px-6 py-3 border border-white/30 text-white hover:bg-white/10"><i class="fab fa-whatsapp mr-2"></i>{{ __('db.Ask a Question First') }}</a>
                </div>
            </div>
        </div>

    </div>
</x-guest-layout>
