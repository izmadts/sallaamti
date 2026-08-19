<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">My Quran Class</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif

            @if ($groupStudents->isEmpty())
            <div class="bg-white rounded-lg shadow-sm p-6">
                <p class="text-gray-600 font-medium mb-2">
                    @if ($admissions->isEmpty())
                    You haven't applied for a Quran Live Class yet.
                    @else
                    None of your applications have been assigned to a class group yet.
                    @endif
                </p>
                <p class="text-sm text-gray-500 mb-4">Our admin will review your admission and assign you to a suitable group shortly. You'll receive a notification once assigned.</p>
                @if ($admissions->isNotEmpty())
                <h4 class="font-medium text-gray-700 mb-2">Your Applications:</h4>
                @foreach ($admissions as $admission)
                <div class="border rounded p-3 text-sm text-gray-600 mb-2">
                    <p><strong>{{ $admission->student_name }}</strong> — {{ $admission->course->title }} — {{ ucfirst($admission->status) }}</p>
                    <p>Preferred: {{ $admission->preferred_time }} | Teacher: {{ ucfirst(str_replace('_', ' ', $admission->teacher_preference)) }}</p>
                </div>
                @endforeach
                @endif
                <a href="{{ route('quran-live.index') }}" class="inline-block bg-pink-600 text-white text-sm px-4 py-2 rounded mt-2">Apply for a Course</a>
            </div>
            @else

            {{-- Child switcher — only shown when a family has more than one active child --}}
            @if ($groupStudents->count() > 1)
            <div class="flex flex-wrap gap-2">
                @foreach ($groupStudents as $gs)
                <a href="{{ route('quran-live.my-class', ['child' => $gs->id]) }}"
                    class="text-sm px-4 py-2 rounded-full font-medium {{ $gs->id === $current->id ? 'text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}"
                    @if ($gs->id === $current->id) style="background: #0d6b6b" @endif>
                    {{ $gs->admission?->student_name ?? $gs->user?->name ?? 'Unknown' }}
                </a>
                @endforeach
            </div>
            @endif

            {{-- Group Info --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">{{ $current->admission?->student_name ?? 'Your' }}'s Class Details</h3>
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
                <a href="{{ route('quran-live.subscribe', [$group->course, $current->admission]) }}" class="bg-pink-600 text-white text-sm px-4 py-2 rounded">Pay This Month's Fee</a>
                @elseif ($subscription->payment_status === 'submitted')
                <p class="text-sm text-yellow-700">⏳ Payment under review.</p>
                @elseif ($subscription->payment_status === 'rejected')
                <p class="text-sm text-red-600 mb-2">❌ Rejected: {{ $subscription->payment_rejection_reason }}</p>
                <a href="{{ route('quran-live.subscribe', [$group->course, $current->admission]) }}" class="bg-pink-600 text-white text-sm px-4 py-2 rounded">Resubmit</a>
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

            {{-- Message thread with the teacher --}}
            @if ($current->admission)
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-3">💬 Messages with {{ $group->teacher?->name ?? 'your teacher' }}</h3>
                <div class="space-y-3 mb-4">
                    @forelse ($current->admission->messages as $message)
                    @php $isMe = $message->sender_id === auth()->id(); @endphp
                    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-md rounded-2xl px-4 py-2.5 text-sm {{ $isMe ? 'bg-teal-600 text-white' : 'bg-gray-50 text-gray-800' }}">
                            <p class="font-semibold text-xs mb-0.5 {{ $isMe ? 'text-teal-100' : 'text-gray-400' }}">{{ $isMe ? 'You' : ($message->sender?->name ?? 'Teacher') }}</p>
                            <p class="leading-relaxed">{{ $message->message }}</p>
                            <p class="text-xs mt-1 {{ $isMe ? 'text-teal-200' : 'text-gray-400' }}">{{ $message->created_at->format('d M, h:i A') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-2">No messages yet — say hello!</p>
                    @endforelse
                </div>
                <form method="POST" action="{{ route('quran-live.admission.reply', $current->admission) }}" class="flex gap-2">
                    @csrf
                    <input type="text" name="message" required maxlength="2000" placeholder="Message the teacher..." class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500">
                    <button class="px-4 py-2 rounded-lg text-white text-sm font-semibold" style="background: #0d6b6b">Send</button>
                </form>
            </div>
            @endif

            {{-- Progress link --}}
            <div class="text-center">
                <a href="{{ route('quran-live.my-progress', ['child' => $current->id]) }}" class="text-sm text-pink-600 hover:underline">
                    📊 View My Progress & Assessments →
                </a>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
