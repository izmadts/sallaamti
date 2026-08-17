{{-- Facebook-timeline-style sidebar for the dashboard — desktop only
     (hidden below lg:), since the existing mobile bottom-tab-bar and
     hamburger nav remain the actual mobile navigation. Surfaces both
     member-account shortcuts and public pages that were otherwise only
     reachable via the "Website" nav dropdown. --}}
<aside class="hidden lg:block w-64 flex-shrink-0">
    <div class="sticky top-4 space-y-4">

        {{-- Profile summary --}}
        <a href="{{ route('profile.edit') }}" class="bg-white rounded-lg shadow-sm p-4 flex items-center gap-3 hover:shadow-md transition">
            <img src="{{ Auth::user()->avatarUrl() }}" loading="lazy" class="w-10 h-10 rounded-full object-cover border-2 border-teal-600 flex-shrink-0">
            <div class="min-w-0">
                <p class="font-semibold text-sm text-gray-800 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-400">{{ __('db.View your profile') }}</p>
            </div>
        </a>

        <div class="bg-white rounded-lg shadow-sm p-3">
            {{-- Your Modules — prioritizes whatever the member showed interest in
                 at registration, same conditionals the dashboard's own module
                 cards already use. --}}
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide px-2 pt-1 pb-2">{{ __('db.Your Modules') }}</p>

            @if (Auth::user()->nikah_module_enabled)
            <a href="{{ ($nikahProfile ?? null) ? route('nikah.show') : route('nikah.create') }}" class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                <span class="text-lg">💍</span> {{ __('db.Nikah') }}
            </a>
            @endif
            @if (Auth::user()->quran_module_enabled)
            <a href="{{ route('courses.index') }}" class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                <span class="text-lg">📖</span> {{ __('db.Quran Courses') }}
            </a>
            <a href="{{ route('quran-live.index') }}" class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                <span class="text-lg">🎥</span> {{ __('db.Live Classes') }}
            </a>
            @endif
            @if (Auth::user()->counseling_module_enabled)
            <a href="{{ route('support.create') }}" class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                <span class="text-lg">🤝</span> {{ __('db.Family Support') }}
            </a>
            @endif

            <a href="{{ route('wall.index') }}" class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition">
                <span class="text-lg">🤲</span> {{ __('db.Sallaamti Wall') }}
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide px-2 pt-1 pb-2">{{ __('db.Discover') }}</p>
            @foreach ([
                ['url' => url('/'), 'icon' => '🏠', 'label' => 'Home'],
                ['url' => url('/about'), 'icon' => 'ℹ️', 'label' => 'About Us'],
                ['url' => url('/activities'), 'icon' => '📅', 'label' => 'Activities'],
                ['url' => url('/events'), 'icon' => '🎉', 'label' => 'Events'],
                ['url' => url('/sermons'), 'icon' => '🎙️', 'label' => 'Sermons'],
                ['url' => route('blog.index'), 'icon' => '📰', 'label' => 'Blog'],
                ['url' => url('/contact'), 'icon' => '✉️', 'label' => 'Contact'],
            ] as $link)
            <a href="{{ $link['url'] }}" class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                <span class="text-lg">{{ $link['icon'] }}</span> {{ __("db.$link[label]") }}
            </a>
            @endforeach
        </div>

        <div class="bg-white rounded-lg shadow-sm p-3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide px-2 pt-1 pb-2">{{ __('db.Community') }}</p>
            <a href="{{ route('volunteer.create') }}" class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                <span class="text-lg">🤝</span> {{ __('db.Volunteer') }}
            </a>
            <a href="{{ route('donate.create') }}" class="flex items-center gap-3 px-2 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                <span class="text-lg">💝</span> {{ __('db.Donate') }}
            </a>
        </div>

    </div>
</aside>
