<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Welcome back, {{ Auth::user()->name }} 👋
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Profile Completion Banner --}}
            @if ($profileCompletion < 100)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex justify-between items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-yellow-800">Complete your profile to get the most out of Sallaamti</p>
                    <div class="mt-2 w-full bg-yellow-200 rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full transition-all" style="width: {{ $profileCompletion }}%"></div>
                    </div>
                    <p class="text-xs text-yellow-700 mt-1">{{ $profileCompletion }}% complete</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="ml-4 text-sm bg-yellow-500 text-white px-3 py-1.5 rounded hover:bg-yellow-600">Complete Profile</a>
        </div>
        @endif

        {{-- Stats Row --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-5 text-center">
                <div class="text-3xl font-bold text-gray-800">{{ $enrollments->count() }}</div>
                <div class="text-sm text-gray-500 mt-1">Courses Enrolled</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-5 text-center">
                <div class="text-3xl font-bold text-gray-800">{{ $certificates->count() }}</div>
                <div class="text-sm text-gray-500 mt-1">Certificates Earned</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-5 text-center">
                <div class="text-3xl font-bold text-gray-800">{{ $liveSubscriptions->count() }}</div>
                <div class="text-sm text-gray-500 mt-1">Live Classes Active</div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-5 text-center">
                <div class="text-3xl font-bold {{ $unreadCount > 0 ? 'text-pink-600' : 'text-gray-800' }}">{{ $unreadCount }}</div>
                <div class="text-sm text-gray-500 mt-1">Unread Notifications</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left Column: Module Status Cards --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- My Modules --}}
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-semibold text-gray-700 mb-4">My Modules</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                        {{-- Quran Courses --}}
                        <a href="{{ route('courses.index') }}" class="border rounded-lg p-4 hover:shadow-md transition {{ $enrollments->count() > 0 ? 'border-green-200 bg-green-50' : 'border-gray-100' }}">
                            <div class="flex justify-between items-start">
                                <div class="text-2xl">📖</div>
                                @if ($enrollments->count() > 0)
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Active</span>
                                @else
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Not enrolled</span>
                                @endif
                            </div>
                            <h4 class="font-medium text-gray-800 mt-2">Quran Courses</h4>
                            <p class="text-xs text-gray-500">{{ $enrollments->count() }} course(s) enrolled</p>
                        </a>

                        {{-- Nikah --}}
                        <a href="{{ $nikahProfile ? route('nikah.show') : route('nikah.create') }}" class="border rounded-lg p-4 hover:shadow-md transition {{ $nikahProfile ? 'border-pink-200 bg-pink-50' : 'border-gray-100' }}">
                            <div class="flex justify-between items-start">
                                <div class="text-2xl">💍</div>
                                @if ($nikahProfile)
                                <span class="text-xs px-2 py-0.5 rounded-full
                                            {{ $nikahProfile->verification_status === 'verified' ? 'bg-green-100 text-green-700' :
                                               ($nikahProfile->verification_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ ucfirst($nikahProfile->verification_status) }}
                                </span>
                                @else
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Not created</span>
                                @endif
                            </div>
                            <h4 class="font-medium text-gray-800 mt-2">Nikah Profile</h4>
                            <p class="text-xs text-gray-500">
                                @if ($nikahProfile)
                                Payment: {{ ucfirst($nikahProfile->payment_status) }}
                                @else
                                Create your profile
                                @endif
                            </p>
                        </a>

                        {{-- Live Classes --}}
                        <a href="{{ route('quran-live.index') }}" class="border rounded-lg p-4 hover:shadow-md transition {{ $liveSubscriptions->count() > 0 ? 'border-blue-200 bg-blue-50' : 'border-gray-100' }}">
                            <div class="flex justify-between items-start">
                                <div class="text-2xl">🎥</div>
                                @if ($liveSubscriptions->count() > 0)
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Active</span>
                                @else
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Not subscribed</span>
                                @endif
                            </div>
                            <h4 class="font-medium text-gray-800 mt-2">Live Quran Classes</h4>
                            <p class="text-xs text-gray-500">{{ $liveSubscriptions->count() }} active this month</p>
                        </a>

                        {{-- Certificates --}}
                        <a href="{{ route('certificate.index') }}" class="border rounded-lg p-4 hover:shadow-md transition {{ $certificates->count() > 0 ? 'border-yellow-200 bg-yellow-50' : 'border-gray-100' }}">
                            <div class="flex justify-between items-start">
                                <div class="text-2xl">🎓</div>
                                @if ($certificates->count() > 0)
                                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Earned</span>
                                @else
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">None yet</span>
                                @endif
                            </div>
                            <h4 class="font-medium text-gray-800 mt-2">Certificates</h4>
                            <p class="text-xs text-gray-500">{{ $certificates->count() }} certificate(s) earned</p>
                        </a>

                    </div>
                </div>
            </div>

            {{-- Right Column: Notifications + Quick Links --}}
            <div class="space-y-6">
                {{-- Course Progress --}}
                @if ($enrollments->count() > 0)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-gray-700">My Learning Progress</h3>
                        <a href="{{ route('courses.my-learning') }}" class="text-sm text-pink-600 hover:underline">View all →</a>
                    </div>
                    <div class="space-y-4">
                        @foreach ($enrollments->take(3) as $item)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-700 font-medium">{{ $item['course']->title }}</span>
                                <span class="text-gray-500">{{ $item['progress'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full transition-all" style="width: {{ $item['progress'] }}%"></div>
                            </div>
                            @if ($item['progress'] === 100)
                            <p class="text-xs text-green-600 mt-0.5">✅ Complete — <a href="{{ route('courses.show', $item['course']) }}" class="underline">Get Certificate</a></p>
                            @else
                            <p class="text-xs text-gray-400 mt-0.5"><a href="{{ route('courses.show', $item['course']) }}" class="hover:underline">Continue →</a></p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Recent Notifications --}}
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-gray-700">Notifications</h3>
                        @if ($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.markAllRead') }}">
                            @csrf
                            <button class="text-xs text-gray-400 hover:text-gray-600">Mark all read</button>
                        </form>
                        @endif
                    </div>
                    <div class="space-y-2">
                        @forelse ($notifications as $notification)
                        <div class="text-sm py-2 border-b last:border-0 {{ $notification->read_at ? 'text-gray-500' : 'text-gray-800 font-medium' }}">
                            <a href="{{ $notification->data['url'] ?? '#' }}" class="block hover:text-pink-600">
                                {{ $notification->data['message'] ?? 'Notification' }}
                            </a>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @empty
                        <p class="text-sm text-gray-400">No notifications yet.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Quick Links --}}
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-semibold text-gray-700 mb-4">Quick Links</h3>
                    <div class="space-y-2">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 text-sm text-gray-600 hover:text-pink-600">
                            👤 Edit Profile
                        </a>
                        <a href="{{ route('courses.index') }}" class="flex items-center gap-2 text-sm text-gray-600 hover:text-pink-600">
                            📖 Browse Courses
                        </a>
                        <a href="{{ route('nikah.browse') }}" class="flex items-center gap-2 text-sm text-gray-600 hover:text-pink-600">
                            💍 Browse Matches
                        </a>
                        <a href="{{ route('nikah.interests') }}" class="flex items-center gap-2 text-sm text-gray-600 hover:text-pink-600">
                            💌 My Interests
                        </a>
                        <a href="{{ route('quran-live.index') }}" class="flex items-center gap-2 text-sm text-gray-600 hover:text-pink-600">
                            🎥 Live Classes
                        </a>
                        <a href="{{ route('certificate.index') }}" class="flex items-center gap-2 text-sm text-gray-600 hover:text-pink-600">
                            🎓 My Certificates
                        </a>
                        <a href="{{ route('volunteer.create') }}" class="flex items-center gap-2 text-sm text-gray-600 hover:text-pink-600">
                            🤝 Volunteer
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>
    </div>
</x-app-layout>