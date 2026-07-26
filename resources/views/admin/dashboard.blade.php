{{-- resources/views/admin/dashboard.blade.php --}}
<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <h2 class="font-semibold text-lg text-gray-800">Dashboard</h2>
            <span class="text-gray-300">›</span>
            <span class="text-sm text-gray-500">Overview</span>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-7xl">

        {{-- ===== USERS ===== --}}
        <section>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Users</p>
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
                <x-admin-stat
                    :href="route('admin.users.index')"
                    value="{{ $stats['total_users'] }}"
                    label="Total Users"
                    color="indigo"
                    icon="👥" />
                <x-admin-stat
                    :href="route('admin.users.index')"
                    value="{{ $stats['new_users_this_month'] }}"
                    label="New This Month"
                    color="indigo"
                    icon="🆕" />
            </div>
        </section>

        {{-- ===== NIKAH ===== --}}
        <section>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Nikah Matchmaking</p>
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
                <x-admin-stat
                    :href="route('admin.nikah.payments')"
                    value="{{ $stats['pending_nikah_payments'] }}"
                    label="Pending Payments"
                    color="yellow"
                    icon="💳"
                    :badge="$stats['pending_nikah_payments'] > 0" />
                <x-admin-stat
                    :href="route('admin.nikah.verifications')"
                    value="{{ $stats['pending_nikah_verification'] }}"
                    label="Pending Verifications"
                    color="pink"
                    icon="✅"
                    :badge="$stats['pending_nikah_verification'] > 0" />
                <x-admin-stat
                    :href="route('admin.nikah.verifications')"
                    value="{{ $stats['total_nikah_profiles'] }}"
                    label="Total Profiles"
                    color="gray"
                    icon="💍" />
                <x-admin-stat
                    :href="route('admin.nikah.verifications')"
                    value="{{ $stats['verified_nikah_profiles'] }}"
                    label="Verified Profiles"
                    color="green"
                    icon="🔒" />
            </div>
            {{-- Reports alert --}}
            @if ($stats['pending_nikah_reports'] > 0)
            <div class="mt-2">
                <a href="{{ route('admin.nikah.reports') }}"
                    class="inline-flex items-center gap-2 text-sm bg-red-50 border border-red-200 text-red-700 px-3 py-1.5 rounded hover:bg-red-100">
                    ⚑ {{ $stats['pending_nikah_reports'] }} pending report(s) need review
                </a>
            </div>
            @endif
        </section>

        {{-- ===== QURAN SELF-PACED COURSES ===== --}}
        <section>
            <div class="flex justify-between items-center mb-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Quran Self-Paced Courses</p>
                <a href="{{ route('admin.courses.create') }}"
                    class="text-xs bg-gray-700 text-white px-2.5 py-1 rounded hover:bg-gray-600">
                    + Add Course
                </a>
            </div>
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
                <x-admin-stat
                    :href="route('admin.courses.index')"
                    value="{{ $stats['total_courses'] }}"
                    label="Total Courses"
                    color="green"
                    icon="📖" />
                <x-admin-stat
                    :href="route('admin.courses.index')"
                    value="{{ $stats['published_courses'] }}"
                    label="Published"
                    color="teal"
                    icon="📢" />
                <x-admin-stat
                    :href="route('admin.courses.index')"
                    value="{{ $stats['total_enrollments'] }}"
                    label="Enrollments"
                    color="teal"
                    icon="🎓" />
                <x-admin-stat
                    :href="route('admin.courses.index')"
                    value="{{ $stats['total_certificates'] }}"
                    label="Certificates Issued"
                    color="teal"
                    icon="🏅" />
            </div>
        </section>

        {{-- ===== QURAN LIVE CLASSES ===== --}}
        <section>
            <div class="flex justify-between items-center mb-2">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Quran Live Classes</p>
                <div class="flex gap-2">
                    <a href="{{ route('admin.quran-admissions.index') }}"
                        class="text-xs bg-yellow-500 text-white px-2.5 py-1 rounded hover:bg-yellow-600">
                        📥 Admissions
                        @if ($stats['pending_admissions'] > 0)
                        <span class="ml-1 bg-white text-yellow-600 text-xs font-bold px-1.5 rounded-full">{{ $stats['pending_admissions'] }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.quran-live-courses.create') }}"
                        class="text-xs bg-gray-700 text-white px-2.5 py-1 rounded hover:bg-gray-600">
                        + Add Level
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
                <x-admin-stat
                    :href="route('admin.quran-live-courses.index')"
                    value="{{ $stats['total_live_courses'] }}"
                    label="Course Levels"
                    color="purple"
                    icon="🎥" />
                <x-admin-stat
                    :href="route('admin.quran-live-courses.index')"
                    value="{{ $stats['total_class_groups'] }}"
                    label="Class Groups"
                    color="violet"
                    icon="👥" />
                <x-admin-stat
                    :href="route('admin.quran-live-courses.index')"
                    value="{{ $stats['total_live_students'] }}"
                    label="Active Students"
                    color="purple"
                    icon="🧑‍🎓" />
                <x-admin-stat
                    :href="route('admin.quran-admissions.index')"
                    value="{{ $stats['pending_admissions'] }}"
                    label="Pending Admissions"
                    color="yellow"
                    icon="📋"
                    :badge="$stats['pending_admissions'] > 0" />
            </div>
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 mt-3">
                <x-admin-stat
                    :href="route('admin.quran-live-courses.index')"
                    value="{{ $stats['total_admissions'] }}"
                    label="Total Admissions"
                    color="gray"
                    icon="📝" />
                <x-admin-stat
                    :href="route('admin.quran-live-courses.index')"
                    value="{{ $stats['pending_live_subscriptions'] }}"
                    label="Pending Payments"
                    color="red"
                    icon="💳"
                    :badge="$stats['pending_live_subscriptions'] > 0" />
                <x-admin-stat
                    :href="route('admin.quran-live-courses.index')"
                    value="{{ $stats['published_live_courses'] }}"
                    label="Published Levels"
                    color="green"
                    icon="📢" />
                <x-admin-stat
                    :href="route('admin.quran-live-courses.index')"
                    value="{{ $stats['active_class_groups'] }}"
                    label="Active Groups"
                    color="teal"
                    icon="✅" />
            </div>
        </section>
        {{-- Support Queries --}}
        <section>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Family Support</p>
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
                <x-admin-stat :href="route('admin.support.index', ['status' => 'new'])"
                    value="{{ $stats['support_new'] }}"
                    label="New Queries"
                    color="yellow" icon="🆘"
                    :badge="$stats['support_new'] > 0" />
                <x-admin-stat :href="route('admin.support.index', ['status' => 'in_progress'])"
                    value="{{ $stats['support_in_progress'] }}"
                    label="In Progress"
                    color="purple" icon="💬"
                    :badge="$stats['support_in_progress'] > 0" />
                <x-admin-stat :href="route('admin.support.index', ['status' => 'resolved'])"
                    value="{{ $stats['support_resolved'] }}"
                    label="Resolved"
                    color="green" icon="✅"
                    :badge="$stats['support_resolved'] > 0" />
                <x-admin-stat :href="route('admin.support.index', ['status' => 'closed'])"
                    value="{{ $stats['support_closed'] }}"
                    label="Closed"
                    color="gray" icon="🔒"
                    :badge="$stats['support_closed'] > 0" />
            </div>
        </section>
        {{-- ===== DONATIONS & NEWSLETTER ===== --}}
        <section>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Donations & Newsletter</p>
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
                <x-admin-stat
                    :href="route('admin.donations.index')"
                    value="{{ $stats['total_donations'] }}"
                    label="Total Donations"
                    color="emerald"
                    icon="💰" />
                <x-admin-stat
                    :href="route('admin.donations.index')"
                    value="{{ $stats['pending_donations'] }}"
                    label="Pending Donations"
                    color="yellow"
                    icon="⏳"
                    :badge="$stats['pending_donations'] > 0" />
                <x-admin-stat
                    :href="route('admin.subscribers.index')"
                    value="{{ $stats['total_subscribers'] }}"
                    label="Subscribers"
                    color="sky"
                    icon="📧" />
            </div>
        </section>

        {{-- ===== VOLUNTEERS ===== --}}
        <section>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Volunteers</p>
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
                <x-admin-stat
                    :href="route('admin.volunteers.index')"
                    value="{{ $stats['pending_volunteers'] }}"
                    label="Pending Applications"
                    color="blue"
                    icon="🤝"
                    :badge="$stats['pending_volunteers'] > 0" />
                <x-admin-stat
                    :href="route('admin.volunteers.index')"
                    value="{{ $stats['total_volunteers'] }}"
                    label="Approved Volunteers"
                    color="blue"
                    icon="⭐" />
            </div>
        </section>

    </div>
</x-admin-layout>