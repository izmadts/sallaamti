<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">My Quran Class</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
            @endif

            @if (!isset($groupStudent) || !$groupStudent)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <p class="text-gray-600 font-medium mb-2">You haven't been assigned to a class group yet.</p>
                <p class="text-sm text-gray-500 mb-4">Our admin will review your admission and assign you to a suitable group shortly. You'll receive a notification once assigned.</p>
                @if (isset($admissions) && $admissions->count() > 0)
                <h4 class="font-medium text-gray-700 mb-2">Your Applications:</h4>
                @foreach ($admissions as $admission)
                <div class="border rounded p-3 text-sm text-gray-600 mb-2">
                    <p><strong>{{ $admission->course->title }}</strong> — {{ ucfirst($admission->status) }}</p>
                    <p>Preferred: {{ $admission->preferred_time }} | Teacher: {{ ucfirst(str_replace('_', ' ', $admission->teacher_preference)) }}</p>
                </div>
                @endforeach
                @else
                <a href="{{ route('quran-live.index') }}" class="inline-block bg-pink-600 text-white text-sm px-4 py-2 rounded">Apply for a Course</a>
                @endif
            </div>
            @else
            {{-- Group Info --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">Your Class Details</h3>
                <dl class="text-sm space-y-1">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Course</dt>
                        <dd>{{ $group->course->title }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Group</dt>
                        <dd>{{ $group->group_name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Teacher</dt>
                        <dd>{{ $group->teacher?->name ?? 'TBA' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Schedule</dt>
                        <dd>{{ is_array($group->class_days) ? implode(', ', $group->class_days) : $group->class_days }} — {{ $group->class_time }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Timezone</dt>
                        <dd>{{ $group->timezone }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Monthly Payment Status --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">Monthly Fee — {{ now()->format('F Y') }}</h3>
                @if (!isset($subscription) || !$subscription)
                <p class="text-sm text-red-600 mb-3">No payment submitted for this month.</p>
                <a href="{{ route('quran-live.subscribe', $group->course) }}" class="bg-pink-600 text-white text-sm px-4 py-2 rounded">Pay This Month's Fee</a>
                @elseif ($subscription->payment_status === 'submitted')
                <p class="text-sm text-yellow-700">⏳ Payment under review.</p>
                @elseif ($subscription->payment_status === 'rejected')
                <p class="text-sm text-red-600 mb-2">❌ Rejected: {{ $subscription->payment_rejection_reason }}</p>
                <a href="{{ route('quran-live.subscribe', $group->course) }}" class="bg-pink-600 text-white text-sm px-4 py-2 rounded">Resubmit</a>
                @elseif ($hasActiveSubscription)
                <p class="text-sm text-green-600 font-medium mb-4">✅ Paid & Confirmed</p>

                {{-- Today's Class Link --}}
                @if ($todaysLink)
                <div class="bg-green-50 border border-green-200 rounded p-4">
                    <p class="text-sm font-medium text-green-800 mb-2">📡 Today's Class is Live!</p>
                    <a href="{{ $todaysLink->join_url }}" target="_blank" class="inline-block bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700">
                        🎥 Join Class Now
                    </a>
                    @if ($todaysLink->passcode)
                    <p class="text-sm text-gray-500 mt-2">Passcode: <strong>{{ $todaysLink->passcode }}</strong></p>
                    @endif
                </div>
                @else
                <div class="bg-gray-50 rounded p-4 text-sm text-gray-500">
                    Today's link hasn't been posted yet. Check back closer to class time ({{ $group->class_time }}).
                </div>
                @endif
                @endif
            </div>

            {{-- Progress link --}}
            <div class="text-center">
                <a href="{{ route('quran-live.my-progress') }}" class="text-sm text-pink-600 hover:underline">
                    📊 View My Progress & Assessments →
                </a>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>