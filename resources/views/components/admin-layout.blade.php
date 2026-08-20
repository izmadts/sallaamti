{{-- resources/views/components/admin-layout.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ ($isRtl ?? false) ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Trix rich text editor --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trix@2/dist/trix.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/trix@2/dist/trix.umd.min.js"></script>
    <style>
        trix-toolbar .trix-button-row {
            flex-wrap: wrap;
        }

        trix-toolbar .trix-button {
            border-color: #d1d5db;
        }

        trix-toolbar .trix-button.trix-active {
            background: #0d6b6b;
            color: #fff;
        }

        trix-editor {
            border: 1px solid #d1d5db;
            border-radius: 0 0 0.375rem 0.375rem;
            min-height: 10rem;
            padding: 0.75rem;
        }

        trix-toolbar {
            border: 1px solid #d1d5db;
            border-bottom: none;
            border-radius: 0.375rem 0.375rem 0 0;
            background: #f9fafb;
            padding: 0.375rem;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased" x-data="{ sidebarOpen: false }">

    {{-- Mobile sidebar overlay --}}
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-20 bg-black/50 lg:hidden" @click="sidebarOpen = false"></div>

    <div class="flex h-screen overflow-hidden">

        {{-- ===== SIDEBAR ===== --}}
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 bg-teal-900 text-white flex flex-col transform transition-transform duration-200 ease-in-out
                   lg:static lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            {{-- Logo --}}
            <div class="flex items-center gap-3 px-5 py-5 border-b border-teal-700">
                @if (file_exists(public_path('images/sallaamti-logo.png')))
                <img src="{{ asset('images/sallaamti-logo.png') }}" class="w-8 h-8 object-contain">
                @else
                <div class="w-8 h-8 bg-teal-600 rounded-full flex items-center justify-center text-xs font-bold">S</div>
                @endif
                <div>
                    <p class="font-bold text-white leading-none">Sallaamti</p>
                    <p class="text-teal-400 text-xs">Admin Panel</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5 text-sm">

                @role('admin')
                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.dashboard') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">🏠</span> Dashboard
                </a>

                {{-- Divider --}}
                <p class="text-teal-500 text-xs uppercase tracking-widest px-3 pt-4 pb-1">Users</p>

                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.users.index') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">👥</span> All Users
                </a>
                <a href="{{ route('admin.users.roles') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.users.roles') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">🎭</span> Roles Overview
                </a>
                <a href="{{ route('admin.bulk-messages.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.bulk-messages*') || request()->routeIs('admin.users.broadcast') || request()->routeIs('admin.subscribers.broadcast') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">📢</span> Bulk Messages
                </a>

                {{-- Nikah --}}
                <p class="text-teal-500 text-xs uppercase tracking-widest px-3 pt-4 pb-1">Nikah</p>

                <a href="{{ route('admin.nikah.profiles') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.nikah.profiles') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">🗂️</span> All Profiles
                </a>
                <a href="{{ route('admin.nikah.payments') }}"
                    class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.nikah.payments*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="flex items-center gap-3"><span class="text-base">💳</span> Payments</span>
                    @php $pendingPayments = \App\Models\NikahProfile::where('payment_status','submitted')->count(); @endphp
                    @if ($pendingPayments > 0)
                    <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $pendingPayments }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.nikah.verifications') }}"
                    class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.nikah.verifications*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="flex items-center gap-3"><span class="text-base">✅</span> Verifications</span>
                    @php $pendingVerif = \App\Models\NikahProfile::where('payment_status','confirmed')->where('verification_status','pending')->count(); @endphp
                    @if ($pendingVerif > 0)
                    <span class="bg-pink-400 text-pink-900 text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $pendingVerif }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.nikah.reports') }}"
                    class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.nikah.reports*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="flex items-center gap-3"><span class="text-base">🚩</span> Reports</span>
                    @php $pendingReports = \App\Models\NikahReport::where('status','pending')->count(); @endphp
                    @if ($pendingReports > 0)
                    <span class="bg-red-400 text-red-900 text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $pendingReports }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.nikah.contact-requests') }}"
                    class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.nikah.contact-requests*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="flex items-center gap-3"><span class="text-base">🤝</span> Matchmaker Requests</span>
                    @php $pendingContactRequests = \App\Models\NikahContactRequest::where('status','pending')->count(); @endphp
                    @if ($pendingContactRequests > 0)
                    <span class="bg-orange-400 text-orange-900 text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $pendingContactRequests }}</span>
                    @endif
                </a>

                {{-- Quran --}}
                <p class="text-teal-500 text-xs uppercase tracking-widest px-3 pt-4 pb-1">Quran Learning</p>
                <a href="{{ route('admin.quran-admissions.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition">📋 Quran Admissions</a>
                <a href="{{ route('admin.courses.index', ['track' => 'quran']) }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.courses*') && request('track') !== 'skills' ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">📖</span> Courses
                </a>
                <a href="{{ route('admin.quran-live-courses.index') }}"
                    class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.quran-live*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="flex items-center gap-3"><span class="text-base">🎥</span> Live Classes</span>
                    @php $pendingLiveSubs = \App\Models\QuranSubscription::where('payment_status','submitted')->count(); @endphp
                    @if ($pendingLiveSubs > 0)
                    <span class="bg-purple-400 text-purple-900 text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $pendingLiveSubs }}</span>
                    @endif
                </a>

                {{-- Digital Skills (presented by IZMA) --}}
                <p class="text-teal-500 text-xs uppercase tracking-widest px-3 pt-4 pb-1">Digital Skills</p>
                <a href="{{ route('admin.courses.index', ['track' => 'skills']) }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.courses*') && request('track') === 'skills' ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">💻</span> Courses
                </a>

                {{-- Community --}}
                <p class="text-teal-500 text-xs uppercase tracking-widest px-3 pt-4 pb-1">Community</p>

                <a href="{{ route('admin.wall.index') }}"
                    class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.wall*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="flex items-center gap-3"><span class="text-base">🤲</span> Sallaamti Wall</span>
                    @php $pendingDuas = \App\Models\DuaRequest::where('status','pending')->count(); @endphp
                    @if ($pendingDuas > 0)
                    <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $pendingDuas }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.volunteers.index') }}"
                    class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.volunteers*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="flex items-center gap-3"><span class="text-base">🤝</span> Volunteers</span>
                    @php $pendingVols = \App\Models\VolunteerApplication::where('status','pending')->count(); @endphp
                    @if ($pendingVols > 0)
                    <span class="bg-blue-400 text-blue-900 text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $pendingVols }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.posts.index') }}"
                    class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.posts*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="flex items-center gap-3"><span class="text-base">📝</span> Public Posts</span>
                    @php $pendingPosts = \App\Models\Post::where('status','pending')->count(); @endphp
                    @if ($pendingPosts > 0)
                    <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $pendingPosts }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.support.index') }}"
                    class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.support*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="flex items-center gap-3"><span class="text-base">🆘</span> Support Queries</span>
                    @php $pendingSupport = \App\Models\SupportQuery::where('status','new')->count(); @endphp
                    @if ($pendingSupport > 0)
                    <span class="bg-red-400 text-red-900 text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $pendingSupport }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.donations.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.donations*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">💰</span> Donations
                </a>
                <a href="{{ route('admin.subscribers.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.subscribers*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">📧</span> Newsletter
                </a>
                <a href="{{ route('admin.counseling-bookings.index') }}"
                    class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.counseling-bookings*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="flex items-center gap-3"><span class="text-base">🗓️</span> Counseling Bookings</span>
                    @php $pendingBookings = \App\Models\CounselingBooking::where('status', 'requested')->count(); @endphp
                    @if ($pendingBookings > 0)
                    <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $pendingBookings }}</span>
                    @endif
                </a>
                <p class="text-teal-500 text-xs uppercase tracking-widest px-3 pt-4 pb-1">FrontEnd</p>
                <a href="{{ route('admin.banners.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                        {{ request()->routeIs('admin.banners*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">🖼️</span> Banners
                </a>
                <a href="{{ route('admin.testimonials.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                        {{ request()->routeIs('admin.testimonials*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">💬</span> Testimonials
                </a>
                <a href="{{ route('admin.blog-posts.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                        {{ request()->routeIs('admin.blog-posts*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">📝</span> Blog Posts
                </a>
                <a href="{{ route('admin.team-members.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                        {{ request()->routeIs('admin.team-members*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">🧑‍🤝‍🧑</span> Team
                </a>
                <a href="{{ route('admin.daily-content.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                        {{ request()->routeIs('admin.daily-content*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">🕌</span> Daily Ayah / Hadith
                </a>
                <a href="{{ route('admin.community-posts.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                        {{ request()->routeIs('admin.community-posts*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">📣</span> Community Posts
                </a>
                <a href="{{ route('admin.integrations.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                        {{ request()->routeIs('admin.integrations*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">🔗</span> Integrations
                </a>
                <a href="{{ route('admin.certificates.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                        {{ request()->routeIs('admin.certificates*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">🎓</span> Certificates
                </a>
                {{-- Settings --}}
                <p class="text-teal-500 text-xs uppercase tracking-widest px-3 pt-4 pb-1">System</p>

                <a href="{{ route('admin.settings.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.settings*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">⚙️</span> Settings
                </a>
                <a href="{{ route('admin.maintenance.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.maintenance*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">🛠️</span> Maintenance
                </a>
                <a href="{{ route('admin.languages.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.languages*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">🌐</span> Languages
                </a>
                <a href="{{ route('admin.translations.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.translations*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">🈺</span> Translations
                </a>
                @else
                @hasanyrole(['manager', 'blogger'])
                <a href="{{ route('admin.blog-posts.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition
                          {{ request()->routeIs('admin.blog-posts*') ? 'bg-teal-700 text-white' : 'text-teal-100 hover:bg-teal-800' }}">
                    <span class="text-base">📝</span> Blog Posts
                </a>
                @endhasanyrole
                @endrole
            </nav>

            {{-- Bottom: Back to site --}}
            <div class="px-3 py-4 border-t border-teal-700 space-y-1">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-teal-300 hover:bg-teal-800 text-sm transition">
                    <span class="text-base">↩️</span> Back to Site
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-teal-300 hover:bg-teal-800 text-sm transition text-left">
                        <span class="text-base">🚪</span> Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Top bar --}}
            <header class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-3">
                    {{-- Mobile menu toggle --}}
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    {{-- Page title slot --}}
                    @isset($header)
                    <div>{{ $header }}</div>
                    @endisset
                </div>

                {{-- Admin info --}}
                <div class="flex items-center gap-3">
                    <x-language-switcher />
                    <span class="text-xs text-gray-400 hidden sm:block">{{ now()->format('d M Y') }}</span>
                    <img src="{{ Auth::user()->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover">
                    <span class="text-sm text-gray-700 hidden sm:block">{{ Auth::user()->name }}</span>
                </div>
            </header>

            {{-- Scrollable page content --}}
            <main class="flex-1 overflow-y-auto p-6">
                @if (isset($errors) && $errors->any())
                <div class="mb-6">
                    <x-alert type="error">
                        <strong>Please fix the following before saving:</strong>
                        <ul class="list-disc ms-5 mt-1">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                </div>
                @endif
                <x-flash-messages />
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Trix inline image upload --}}
    <script>
        document.addEventListener("trix-attachment-add", function(event) {
            const attachment = event.attachment;
            if (!attachment.file) return;

            const formData = new FormData();
            formData.append("image", attachment.file);

            fetch("{{ route('admin.editor-images.store') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData,
                })
                .then(res => res.json())
                .then(data => {
                    attachment.setAttributes({ url: data.url, href: data.url });
                })
                .catch(() => {
                    attachment.remove();
                });
        });
    </script>

</body>

</html>