<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ $course->title }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))<div class="p-4 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>@endif

            <div class="bg-white rounded-lg shadow-sm p-6">
                <p class="text-gray-600">{{ $course->description }}</p>
                <p class="text-sm text-gray-500 mt-2">Teacher: {{ $course->teacher?->name ?? 'TBA' }} | {{ $course->class_time }}</p>
                <p class="text-sm font-medium text-pink-600 mt-1">Rs. {{ number_format($course->monthly_fee) }}/month</p>
                @if ($course->min_age || $course->max_age)
                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full mt-2" style="background: var(--teal-light); color: var(--teal)">
                    🎂 Ages {{ $course->min_age ?? '0' }}{{ $course->max_age ? '–'.$course->max_age : '+' }}
                </span>
                @endif
            </div>

            <x-quran-safety-card />

            @if (!auth()->check())
            {{-- Guest --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="text-center p-4 rounded-xl border-2 border-dashed border-teal-200 bg-teal-50">
                    <p class="text-teal-800 font-medium mb-3">🔒 Register to apply for this live class</p>
                    <div class="flex gap-2 justify-center">
                        <a href="{{ route('register') }}" class="bg-teal-700 text-white text-sm px-5 py-2 rounded-lg font-medium">Register Free</a>
                        <a href="{{ route('login') }}" class="border border-teal-600 text-teal-700 text-sm px-5 py-2 rounded-lg font-medium">Log In</a>
                    </div>
                </div>
            </div>
            @else

            @forelse ($admissions as $admission)
            @php
                $subscription = $course->subscriptionFor($admission);
                $todaysLink = $subscription && $subscription->payment_status === 'confirmed' ? $course->todaysLink() : null;
            @endphp
            <div class="bg-white rounded-lg shadow-sm p-6">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">{{ $admission->student_name }}</p>
                @if (!$subscription || $subscription->payment_status === 'unpaid')
                <p class="text-sm text-gray-600 mb-3">💳 This month's fee payment required.</p>
                <a href="{{ route('quran-live.subscribe', [$course, $admission]) }}" class="inline-block bg-pink-600 text-white text-sm px-5 py-2 rounded">Pay This Month's Fee</a>
                @elseif ($subscription->payment_status === 'submitted')
                <p class="text-sm text-yellow-700">⏳ Payment under review for {{ $subscription->month }}.</p>
                @elseif ($subscription->payment_status === 'rejected')
                <p class="text-sm text-red-600 mb-3">❌ Rejected: {{ $subscription->payment_rejection_reason }}</p>
                <a href="{{ route('quran-live.subscribe', [$course, $admission]) }}" class="inline-block bg-pink-600 text-white text-sm px-5 py-2 rounded">Resubmit Payment</a>
                @elseif ($subscription->payment_status === 'confirmed')
                <p class="text-sm text-green-600 font-medium mb-3">✅ Active for {{ $subscription->month }}</p>
                @if ($todaysLink)
                <div class="bg-gray-50 border rounded p-4 text-sm">
                    <p><strong>Today's Class Link:</strong> <a href="{{ $todaysLink->join_url }}" target="_blank" class="text-blue-600 underline">Join Now</a></p>
                    @if ($todaysLink->passcode)<p><strong>Passcode:</strong> {{ $todaysLink->passcode }}</p>@endif
                </div>
                @else
                <p class="text-sm text-gray-500">Today's link hasn't been posted yet by your teacher. Check back closer to class time.</p>
                @endif
                @endif
            </div>
            @empty
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">Register for this Class</h3>
                <a href="{{ route('quran-live.admission', $course) }}" class="inline-block bg-pink-600 text-white text-sm px-5 py-2 rounded-lg hover:bg-pink-700">
                    Apply for Admission
                </a>
            </div>
            @endforelse

            @if ($admissions->isNotEmpty())
            <div class="text-center">
                <a href="{{ route('quran-live.admission', $course) }}" class="text-sm text-teal-700 hover:underline">+ Apply for another child</a>
            </div>
            @endif

            @endif


        </div>
    </div>
</x-app-layout>