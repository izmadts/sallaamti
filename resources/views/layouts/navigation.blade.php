<nav x-data="{ open: false }" class="bg-yellow-500 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Desktop Nav Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex items-center">

                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:text-gray-700 focus:outline-none transition">
                        🏠 Dashboard
                    </x-nav-link>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-2 py-2 border border-transparent text-sm font-medium rounded-md text-white hover:text-gray-700 focus:outline-none transition">
                                📖 Quran
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('courses.index')" :active="request()->routeIs('courses.*') || request()->routeIs('lessons.*') || request()->routeIs('quiz.*') || request()->routeIs('lesson.quiz.*')">📖 Self Quran Courses</x-dropdown-link>
                            <x-dropdown-link :href="route('quran-live.my-class')">📚 My Quran Class</x-dropdown-link>
                            <x-dropdown-link :href="route('quran-live.my-progress')"> 📊 My Progress</x-dropdown-link>
                            <x-dropdown-link :href="route('quran-live.index')">🎥 Live Classes</x-dropdown-link>
                            <x-dropdown-link :href="route('certificate.index')">🎓 My Certificates</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger" :active="request()->routeIs('nikah.*')">
                            <button class="inline-flex items-center px-2 py-2 border border-transparent text-sm font-medium rounded-md text-white hover:text-gray-700 focus:outline-none transition">
                                💍 Nikah
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="auth()->user()->nikahProfile ? route('nikah.show') : route('nikah.create')">📖 My Profile</x-dropdown-link>
                            <x-dropdown-link :href="route('nikah.browse')">📚 Browse Profiles</x-dropdown-link>
                            <x-dropdown-link :href="route('nikah.interests')"> 📊 Interest </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <x-nav-link :href="route('volunteer.create')" :active="request()->routeIs('volunteer.*')" class="text-white">
                        🤝 Volunteer
                    </x-nav-link>

                    <x-nav-link :href="route('donate.create')" :active="request()->routeIs('donate.*') || request()->routeIs('donations.*')" class="text-white">
                        💝 Donate
                    </x-nav-link>

                    @if (auth()->user()->certificates()->count() > 0)
                    <x-nav-link :href="route('certificate.index')" :active="request()->routeIs('certificate.*')" class="text-white">
                        🎓 Certificates
                    </x-nav-link>
                    @endif

                    @role('admin')
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')" class="text-white">
                        ⚙️ Admin
                    </x-nav-link>
                    @endrole

                    @role('teacher')
                    <x-nav-link :href="route('teacher.courses.index')" :active="request()->routeIs('teacher.*')" class="text-white">
                        👩‍🏫 Teach
                    </x-nav-link>
                    <x-nav-link :href="route('teacher.groups.index')" :active="request()->routeIs('teacher.*')" class="text-white">
                        👩‍🏫 My Classes
                    </x-nav-link>
                    @endrole

                </div>
            </div>

            <!-- Right Side: Bell + Avatar + Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">

                <!-- Notification Bell -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2 text-white hover:text-gray-700">
                        🔔
                        @if (Auth::user()->unreadNotifications->count() > 0)
                        <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
                            {{ Auth::user()->unreadNotifications->count() }}
                        </span>
                        @endif
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-100">
                        <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                            <span class="text-xs font-semibold text-white">Notifications</span>
                            @if (Auth::user()->unreadNotifications->count() > 0)
                            <form method="POST" action="{{ route('notifications.markAllRead') }}">
                                @csrf
                                <button class="text-xs text-gray-400 hover:text-gray-600">Mark all read</button>
                            </form>
                            @endif
                        </div>
                        @forelse (Auth::user()->notifications->take(5) as $notification)
                        <a href="{{ $notification->data['url'] ?? '#' }}"
                            class="block px-4 py-3 text-sm hover:bg-gray-50 border-b border-gray-50 last:border-0
                                {{ $notification->read_at ? 'text-white' : 'text-gray-800 font-medium bg-blue-50' }}">
                            {{ $notification->data['message'] ?? 'Notification' }}
                            <span class="block text-xs text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</span>
                        </a>
                        @empty
                        <p class="px-4 py-3 text-sm text-white">No notifications yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Avatar + User Dropdown -->
                <img src="{{ Auth::user()->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover border border-gray-200">

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-2 py-2 border border-transparent text-sm font-medium rounded-md text-white hover:text-gray-700 focus:outline-none transition">
                            {{ Auth::user()->name }}
                            <svg class="ms-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">👤 My Profile</x-dropdown-link>
                        <x-dropdown-link :href="route('courses.my-learning')">📚 My Learning</x-dropdown-link>
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

            <!-- Mobile Hamburger -->
            <div class="-me-2 flex items-center sm:hidden gap-2">
                <!-- Mobile Bell -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2 text-white">
                        🔔
                        @if (Auth::user()->unreadNotifications->count() > 0)
                        <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">
                            {{ Auth::user()->unreadNotifications->count() }}
                        </span>
                        @endif
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak
                        class="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-lg py-1 z-50">
                        @forelse (Auth::user()->notifications->take(5) as $notification)
                        <a href="{{ $notification->data['url'] ?? '#' }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $notification->read_at ? '' : 'font-semibold bg-blue-50' }}">
                            {{ $notification->data['message'] ?? 'Notification' }}
                        </a>
                        @empty
                        <p class="px-4 py-2 text-sm text-white">No notifications yet.</p>
                        @endforelse
                    </div>
                </div>

                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">🏠 Dashboard</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*')">📖 Quran Courses</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('quran-live.index')" :active="request()->routeIs('quran-live.*')">🎥 Live Classes</x-responsive-nav-link>
            <x-responsive-nav-link :href="auth()->user()->nikahProfile ? route('nikah.show') : route('nikah.create')" :active="request()->routeIs('nikah.*')">💍 Nikah</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('volunteer.create')">🤝 Volunteer</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('donate.create')">💝 Donate</x-responsive-nav-link>
            @role('admin')
            <x-responsive-nav-link :href="route('admin.dashboard')">⚙️ Admin Dashboard</x-responsive-nav-link>
            @endrole
            @role('teacher')
            <x-responsive-nav-link :href="route('teacher.courses.index')">👩‍🏫 Teacher Panel</x-responsive-nav-link>
            @endrole
        </div>

        <!-- Mobile User Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4 mb-3">
                <div class="flex items-center gap-3">
                    <img src="{{ Auth::user()->avatarUrl() }}" class="w-10 h-10 rounded-full object-cover">
                    <div>
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-white">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">👤 My Profile</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('courses.my-learning')">📚 My Learning</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('nikah.interests')">💌 My Interests</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('certificate.index')">🎓 My Certificates</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('donate.my')">💝 My Donations</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        🚪 Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>