<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Dashboard</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="{{ route('admin.nikah.verifications') }}" class="bg-white rounded-lg shadow-sm p-5 hover:shadow-md transition">
                    <p class="text-3xl font-bold text-pink-600">{{ $stats['pending_nikah_verification'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">Pending Nikah Verifications</p>
                </a>
                <a href="{{ route('admin.nikah.payments') }}" class="bg-white rounded-lg shadow-sm p-5 hover:shadow-md transition">
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_nikah_payments'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">Pending Nikah Payments</p>
                </a>
                <a href="{{ route('admin.courses.index') }}" class="bg-white rounded-lg shadow-sm p-5 hover:shadow-md transition">
                    <p class="text-3xl font-bold text-green-600">{{ $stats['total_courses'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">Quran Courses</p>
                </a>
                <a href="{{ route('admin.quran-live-courses.index') }}" class="bg-white rounded-lg shadow-sm p-5 hover:shadow-md transition">
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['total_live_courses'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">Live Quran Courses</p>
                </a>
                <a href="{{ route('admin.donations.index') }}" class="bg-white rounded-lg shadow-sm p-5 hover:shadow-md transition">
                    <p class="text-3xl font-bold text-green-800">{{ $stats['total_donations'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">Donations</p>
                </a>
                <a href="{{ route('admin.volunteers.index') }}" class="bg-white rounded-lg shadow-sm p-5 hover:shadow-md transition">
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['pending_volunteers'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">Pending Volunteer Applications</p>
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Quick Links</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                    <a href="{{ route('admin.nikah.verifications') }}" class="text-pink-600 hover:underline">Nikah Verifications</a>
                    <a href="{{ route('admin.nikah.payments') }}" class="text-pink-600 hover:underline">Nikah Payments</a>
                    <a href="{{ route('admin.courses.index') }}" class="text-green-600 hover:underline">Manage Courses</a>
                    <a href="{{ route('admin.quran-live-courses.index') }}" class="text-purple-600 hover:underline">Manage Live Courses</a>
                    <a href="{{ route('admin.donations.index') }}" class="text-green-800 hover:underline">Check Donations</a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>