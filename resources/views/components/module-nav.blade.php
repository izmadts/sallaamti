{{--
    Shared "related pages" navigation for module hub pages (Nikah, Quran,
    Family Support, Sallaamti Wall) — so a member landing on any one page of
    a module always sees the same easy way to move to the other pages of
    that module, instead of every module page inventing its own layout.

    layout="sidebar" (default): horizontal scrollable chip row on mobile,
    vertical w-64 sidebar on desktop (lg+) — pair with a `flex flex-col
    lg:flex-row gap-6` wrapper around the nav + main content.

    layout="bar": always a horizontal chip row, no sidebar — for pages whose
    main content is already a full-width grid/feed that shouldn't be split
    into two columns.
--}}
@props(['module', 'layout' => 'sidebar', 'title' => null])

@php
$allLinks = [
    'nikah' => [
        ['route' => 'nikah.show', 'icon' => '💍', 'label' => __('db.My Profile')],
        ['route' => 'nikah.edit', 'icon' => '✏️', 'label' => __('db.Edit Profile')],
        ['route' => 'nikah.browse', 'icon' => '🔍', 'label' => __('db.Browse Matches')],
        ['route' => 'nikah.saved', 'icon' => '🔖', 'label' => __('db.Saved Profiles')],
        ['route' => 'nikah.interests', 'icon' => '💌', 'label' => __('db.Interests & Messages')],
        ['route' => 'nikah.payment', 'icon' => '💳', 'label' => __('db.Payment')],
        ['route' => 'nikah.blocked', 'icon' => '🚫', 'label' => __('db.Blocked Users')],
    ],
    'quran' => [
        ['route' => 'courses.index', 'icon' => '📖', 'label' => __('db.Self-Paced Courses')],
        ['route' => 'quran-live.index', 'icon' => '🎥', 'label' => __('db.Live Classes')],
        ['route' => 'courses.my-learning', 'icon' => '📚', 'label' => __('db.My Learning'), 'auth' => true],
        ['route' => 'quran-live.my-class', 'icon' => '🎓', 'label' => __('db.My Class'), 'auth' => true],
        ['route' => 'quran-live.my-progress', 'icon' => '📈', 'label' => __('db.My Progress'), 'auth' => true],
        ['route' => 'certificate.index', 'icon' => '🏆', 'label' => __('db.My Certificates'), 'auth' => true],
    ],
    'skills' => [
        ['route' => 'skills.index', 'icon' => '💻', 'label' => __('db.Browse Skills')],
        ['route' => 'courses.my-learning', 'icon' => '📚', 'label' => __('db.My Learning'), 'auth' => true],
        ['route' => 'certificate.index', 'icon' => '🏆', 'label' => __('db.My Certificates'), 'auth' => true],
    ],
    'counseling' => [
        ['route' => 'support.create', 'icon' => '📝', 'label' => __('db.Submit a Query')],
        ['route' => 'support.index', 'icon' => '📋', 'label' => __('db.My Queries')],
        ['route' => 'counseling.book.start', 'icon' => '📅', 'label' => __('db.Book a Counselor')],
        ['route' => 'counseling.bookings.index', 'icon' => '🗓️', 'label' => __('db.My Bookings')],
    ],
    'wall' => [
        ['route' => 'dashboard', 'icon' => '🏠', 'label' => __('db.Dashboard')],
        ['route' => 'nikah.browse', 'icon' => '💍', 'label' => __('db.Nikah Matches')],
        ['route' => 'courses.index', 'icon' => '📖', 'label' => __('db.Quran Courses')],
        ['route' => 'skills.index', 'icon' => '💻', 'label' => __('db.Digital Skills')],
        ['route' => 'support.create', 'icon' => '🤝', 'label' => __('db.Family Support')],
    ],
];

$titles = [
    'nikah' => __('db.Nikah Menu'),
    'quran' => __('db.Quran Menu'),
    'skills' => __('db.Skills Menu'),
    'counseling' => __('db.Family Support Menu'),
    'wall' => __('db.Explore More'),
];

$moduleLinks = $allLinks[$module] ?? [];
$navTitle = $title ?? ($titles[$module] ?? __('db.Quick Links'));
$isBar = $layout === 'bar';
@endphp

@if (count($moduleLinks))
<nav class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3 {{ $isBar ? 'mb-6' : 'lg:w-64 lg:flex-shrink-0 mb-6 lg:mb-0' }}">
    @unless ($isBar)
    <p class="hidden lg:block text-xs font-semibold text-gray-400 uppercase tracking-wide px-2 pt-1 pb-2">{{ $navTitle }}</p>
    @endunless
    <div class="flex {{ $isBar ? '' : 'lg:flex-col' }} gap-2 {{ $isBar ? '' : 'lg:gap-1' }} overflow-x-auto {{ $isBar ? '' : 'lg:overflow-visible' }} pb-1 {{ $isBar ? '' : 'lg:pb-0' }} -mx-1 px-1 lg:mx-0 lg:px-0">
        @foreach ($moduleLinks as $link)
            @continue(($link['auth'] ?? false) && !auth()->check())
            @continue(!\Illuminate\Support\Facades\Route::has($link['route']))
            @php $isActive = request()->routeIs($link['route']); @endphp
            <a href="{{ route($link['route']) }}"
                class="flex-shrink-0 flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-all duration-150
                {{ $isActive ? 'bg-[--teal] text-white shadow-sm' : 'text-gray-600 hover:bg-[--teal-light] hover:text-[--teal-dark]' }}">
                <span>{{ $link['icon'] }}</span> {{ $link['label'] }}
            </a>
        @endforeach
    </div>
</nav>
@endif
