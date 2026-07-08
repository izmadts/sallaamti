<nav x-data="{ open: false }" class="bg-teal-700 dark:bg-gray-800 border-b border-teal-800">
    @auth
    {{-- ===== AUTHENTICATED NAV ===== --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- Left: Logo + Nav Links --}}
            <div class="flex items-center">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-white" />
                    </a>
                </div>

                {{-- Desktop Nav --}}
                <div class="hidden space-x-1 sm:ml-8 sm:flex items-center">

                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                        class="text-white hover:bg-teal-600 px-3 py-2 rounded-md text-sm">
                        🏠 Dashboard
                    </x-nav-link>

                    {{-- Quran Dropdown --}}
                    <x-dropdown align="left" width="56">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-white hover:bg-teal-600 focus:outline-none transition
                                    {{ request()->routeIs('courses.*') || request()->routeIs('lessons.*') || request()->routeIs('quran-live.*') || request()->routeIs('quiz.*') || request()->routeIs('certificate.*') ? 'bg-teal-800' : '' }}">
                                📖 Quran
                                <svg class="ms-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="px-3 py-1 text-xs text-gray-400 font-semibold uppercase tracking-wider">Self-Paced</div>
                            <x-dropdown-link :href="route('courses.index')">📖 Browse Courses</x-dropdown-link>
                            <x-dropdown-link :href="route('courses.my-learning')">📚 My Learning</x-dropdown-link>
                            <x-dropdown-link :href="route('certificate.index')">🎓 My Certificates</x-dropdown-link>
                            <div class="border-t border-gray-100 my-1"></div>
                            <div class="px-3 py-1 text-xs text-gray-400 font-semibold uppercase tracking-wider">Live Classes</div>
                            <x-dropdown-link :href="route('quran-live.index')">🎥 Browse Live Courses</x-dropdown-link>
                            <x-dropdown-link :href="route('quran-live.my-class')">📡 My Class & Link</x-dropdown-link>
                            <x-dropdown-link :href="route('quran-live.my-progress')">📊 My Progress</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    {{-- Nikah Dropdown --}}
                    <x-dropdown align="left" width="52">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-white hover:bg-teal-600 focus:outline-none transition
                                    {{ request()->routeIs('nikah.*') ? 'bg-teal-800' : '' }}">
                                💍 Nikah
                                @if (Auth::user()?->nikahProfile?->verification_status === 'pending')
                                <span class="ml-1 w-2 h-2 rounded-full bg-yellow-400 inline-block"></span>
                                @elseif (Auth::user()?->nikahProfile?->verification_status === 'verified')
                                <span class="ml-1 w-2 h-2 rounded-full bg-green-400 inline-block"></span>
                                @endif
                                <svg class="ms-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="Auth::user()?->nikahProfile ? route('nikah.show') : route('nikah.create')">
                                👤 My Profile
                                @if (Auth::user()?->nikahProfile?->verification_status === 'pending')
                                <span class="ml-1 text-xs text-yellow-600">(Pending)</span>
                                @elseif (Auth::user()?->nikahProfile?->verification_status === 'verified')
                                <span class="ml-1 text-xs text-green-600">✓</span>
                                @endif
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('nikah.browse')">🔍 Browse Matches</x-dropdown-link>
                            <x-dropdown-link :href="route('nikah.interests')">💌 My Interests</x-dropdown-link>
                            <x-dropdown-link :href="route('nikah.saved')">★ Saved Profiles</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <x-nav-link :href="route('volunteer.create')" :active="request()->routeIs('volunteer.*')"
                        class="text-white hover:bg-teal-600 px-3 py-2 rounded-md text-sm">
                        🤝 Volunteer
                    </x-nav-link>

                    <x-nav-link :href="route('donate.create')" :active="request()->routeIs('donate.*') || request()->routeIs('donations.*')"
                        class="text-white hover:bg-teal-600 px-3 py-2 rounded-md text-sm">
                        💝 Donate
                    </x-nav-link>

                    @role('admin')
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')"
                        class="text-white hover:bg-teal-600 px-3 py-2 rounded-md text-sm">
                        ⚙️ Admin
                    </x-nav-link>
                    @endrole

                    @role('teacher')
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium text-white hover:bg-teal-600 focus:outline-none transition
                                        {{ request()->routeIs('teacher.*') ? 'bg-teal-800' : '' }}">
                                👩‍🏫 Teach
                                <svg class="ms-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('teacher.courses.index')">📚 My Courses</x-dropdown-link>
                            <x-dropdown-link :href="route('teacher.groups.index')">👥 My Class Groups</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                    @endrole

                    @hasanyrole(['manager', 'blogger'])
                    <x-nav-link :href="route('admin.blog-posts.index')" :active="request()->routeIs('admin.blog-posts*')"
                        class="text-white hover:bg-teal-600 px-3 py-2 rounded-md text-sm">
                        📝 Blog Posts
                    </x-nav-link>
                    @endhasanyrole

                </div>
            </div>

            {{-- Right: Bell + Avatar --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">

                {{-- Notification Bell --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2 text-white hover:bg-teal-600 rounded-full">
                        🔔
                        @if ((Auth::user()?->unreadNotifications?->count() ?? 0) > 0)
                        <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
                            {{ Auth::user()->unreadNotifications->count() }}
                        </span>
                        @endif
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-100">
                        <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                            <span class="text-xs font-semibold text-gray-700">Notifications</span>
                            @if ((Auth::user()?->unreadNotifications?->count() ?? 0) > 0)
                            <form method="POST" action="{{ route('notifications.markAllRead') }}">
                                @csrf
                                <button class="text-xs text-gray-400 hover:text-gray-600">Mark all read</button>
                            </form>
                            @endif
                        </div>
                        @forelse (Auth::user()?->notifications?->take(5) ?? [] as $notification)
                        <a href="{{ $notification->data['url'] ?? '#' }}"
                            class="block px-4 py-3 text-sm hover:bg-gray-50 border-b border-gray-50 last:border-0
                                    {{ $notification->read_at ? 'text-gray-500' : 'text-gray-800 font-medium bg-blue-50' }}">
                            {{ $notification->data['message'] ?? 'Notification' }}
                            <span class="block text-xs text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</span>
                        </a>
                        @empty
                        <p class="px-4 py-3 text-sm text-gray-500">No notifications yet.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Avatar --}}
                <img src="{{ Auth::user()?->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover border-2 border-white">

                {{-- User Dropdown --}}
                <x-dropdown align="right" width="52">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-2 py-2 text-sm font-medium rounded-md text-white hover:bg-teal-600 focus:outline-none transition">
                            {{ Str::limit(Auth::user()?->name ?? '', 15) }}
                            <svg class="ms-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-3 py-2 border-b border-gray-100">
                            <p class="text-xs font-semibold text-gray-800">{{ Auth::user()?->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()?->email }}</p>
                        </div>
                        <x-dropdown-link :href="route('profile.edit')">👤 My Profile</x-dropdown-link>
                        <x-dropdown-link :href="route('courses.my-learning')">📚 My Learning</x-dropdown-link>
                        <x-dropdown-link :href="route('quran-live.my-class')">📡 My Quran Class</x-dropdown-link>
                        <x-dropdown-link :href="route('nikah.interests')">💌 My Interests</x-dropdown-link>
                        <x-dropdown-link :href="route('nikah.saved')">★ Saved Profiles</x-dropdown-link>
                        <x-dropdown-link :href="route('certificate.index')">🎓 My Certificates</x-dropdown-link>
                        <x-dropdown-link :href="route('donate.my')">💝 My Donations</x-dropdown-link>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                🚪 Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Mobile Hamburger --}}
            <div class="-me-2 flex items-center sm:hidden gap-2">
                {{-- Mobile Bell --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2 text-white">
                        🔔
                        @if ((Auth::user()?->unreadNotifications?->count() ?? 0) > 0)
                        <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
                            {{ Auth::user()->unreadNotifications->count() }}
                        </span>
                        @endif
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak
                        class="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-lg py-1 z-50">
                        @forelse (Auth::user()?->notifications?->take(5) ?? [] as $notification)
                        <a href="{{ $notification->data['url'] ?? '#' }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $notification->read_at ? '' : 'font-semibold bg-blue-50' }}">
                            {{ $notification->data['message'] ?? 'Notification' }}
                        </a>
                        @empty
                        <p class="px-4 py-2 text-sm text-gray-500">No notifications yet.</p>
                        @endforelse
                    </div>
                </div>

                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-teal-600 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    {{-- Mobile Menu --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-teal-800">
        <div class="pt-2 pb-3 space-y-1 px-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white">
                🏠 Dashboard
            </x-responsive-nav-link>

            <div class="px-3 py-1 text-xs text-teal-300 font-semibold uppercase tracking-wider mt-2">Quran Learning</div>
            <x-responsive-nav-link :href="route('courses.index')" class="text-white">📖 Browse Courses</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('courses.my-learning')" class="text-white">📚 My Learning</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('quran-live.index')" class="text-white">🎥 Live Classes</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('quran-live.my-class')" class="text-white">📡 My Quran Class</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('quran-live.my-progress')" class="text-white">📊 My Progress</x-responsive-nav-link>

            <div class="px-3 py-1 text-xs text-teal-300 font-semibold uppercase tracking-wider mt-2">Nikah</div>
            <x-responsive-nav-link :href="Auth::user()?->nikahProfile ? route('nikah.show') : route('nikah.create')" class="text-white">💍 My Nikah Profile</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('nikah.browse')" class="text-white">🔍 Browse Matches</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('nikah.interests')" class="text-white">💌 My Interests</x-responsive-nav-link>

            <div class="px-3 py-1 text-xs text-teal-300 font-semibold uppercase tracking-wider mt-2">More</div>
            <x-responsive-nav-link :href="route('volunteer.create')" class="text-white">🤝 Volunteer</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('donate.create')" class="text-white">💝 Donate</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('certificate.index')" class="text-white">🎓 My Certificates</x-responsive-nav-link>

            @role('admin')
            <div class="px-3 py-1 text-xs text-teal-300 font-semibold uppercase tracking-wider mt-2">Admin</div>
            <x-responsive-nav-link :href="route('admin.dashboard')" class="text-white">⚙️ Admin Dashboard</x-responsive-nav-link>
            @endrole

            @role('teacher')
            <div class="px-3 py-1 text-xs text-teal-300 font-semibold uppercase tracking-wider mt-2">Teacher</div>
            <x-responsive-nav-link :href="route('teacher.courses.index')" class="text-white">📚 My Courses</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('teacher.groups.index')" class="text-white">👥 My Class Groups</x-responsive-nav-link>
            @endrole

            @hasanyrole(['manager', 'blogger'])
            <div class="px-3 py-1 text-xs text-teal-300 font-semibold uppercase tracking-wider mt-2">Blog</div>
            <x-responsive-nav-link :href="route('admin.blog-posts.index')" class="text-white">📝 Blog Posts</x-responsive-nav-link>
            @endhasanyrole
        </div>

        {{-- Mobile User Info --}}
        <div class="pt-4 pb-3 border-t border-teal-600">
            <div class="px-4 mb-3">
                <div class="flex items-center gap-3">
                    <img src="{{ Auth::user()?->avatarUrl() }}" class="w-10 h-10 rounded-full object-cover border-2 border-white">
                    <div>
                        <div class="font-medium text-white">{{ Auth::user()?->name }}</div>
                        <div class="text-sm text-teal-200">{{ Auth::user()?->email }}</div>
                    </div>
                </div>
            </div>
            <div class="space-y-1 px-2">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-white">👤 My Profile</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('donate.my')" class="text-white">💝 My Donations</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                        class="text-white">
                        🚪 Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>

    @else
    {{-- ===== GUEST NAV — simple, clean ===== --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            {{-- Logo --}}
            <a href="{{ url('/') }}">
                <x-application-logo class="block h-9 w-auto fill-current text-white" />
            </a>

            {{-- Desktop guest links --}}
            <div class="hidden sm:flex items-center gap-3">
                <a href="{{ url('/') }}"
                    class="text-white/80 hover:text-white text-sm font-medium transition-colors">
                    Home
                </a>
                <a href="{{ url('/about') }}"
                    class="text-white/80 hover:text-white text-sm font-medium transition-colors">
                    About
                </a>
                <a href="{{ route('courses.index') }}"
                    class="text-white/80 hover:text-white text-sm font-medium transition-colors">
                    Courses
                </a>
                <a href="{{ url('/contact') }}"
                    class="text-white/80 hover:text-white text-sm font-medium transition-colors">
                    Contact
                </a>
                <div class="w-px h-5 bg-white/20 mx-1"></div>
                <a href="{{ route('login') }}"
                    class="text-white/90 hover:text-white text-sm font-medium border border-white/30 px-4 py-1.5 rounded-lg hover:bg-white/10 transition-all">
                    Log In
                </a>
                <a href="{{ route('register') }}"
                    class="text-white text-sm font-semibold px-4 py-1.5 rounded-lg transition-all"
                    style="background: #b8962e">
                    Register Free
                </a>
            </div>

            {{-- Mobile guest buttons --}}
            <div class="flex sm:hidden items-center gap-2">
                <a href="{{ route('login') }}"
                    class="text-white text-sm font-medium border border-white/30 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all">
                    Log In
                </a>
                <a href="{{ route('register') }}"
                    class="text-white text-sm font-semibold px-3 py-1.5 rounded-lg transition-all"
                    style="background: #b8962e">
                    Register
                </a>
            </div>

        </div>
    </div>
    @endauth
</nav>