<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Certificate / ID Verification</h2>
    </x-slot>

    <div class="py-12 text-center">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <p class="text-sm text-gray-600 mb-4">Enter the code printed on a Sallaamti certificate or volunteer ID card to verify it's genuine.</p>
                <form method="GET" action="{{ route('certificate.verify') }}" class="flex gap-2">
                    <input type="text" name="code" value="{{ $code }}" placeholder="e.g. SLM-2026-XXXXXXXX"
                        class="flex-1 border-gray-300 rounded-md text-sm" required>
                    <button type="submit" class="bg-teal-700 text-white text-sm px-5 py-2 rounded-md hover:bg-teal-800">Verify</button>
                </form>
            </div>

            @if ($code)
            <div class="bg-white rounded-lg shadow p-6 text-left">
                @if ($certificate)
                <p class="text-green-600 font-medium mb-4 text-center">✅ This {{ $certificate->type === 'volunteer_id' ? 'volunteer ID' : ($certificate->type === 'nikah_counselor_id' ? 'Nikah Counselor ID' : 'certificate') }} is valid.</p>
                <dl class="text-sm space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Name</dt>
                        <dd>{{ $certificate->user->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">{{ $certificate->type === 'course' ? 'Course' : 'Type' }}</dt>
                        <dd>
                            @if ($certificate->type === 'course')
                            {{ $certificate->course?->title }}
                            @elseif ($certificate->type === 'volunteer_id')
                            Volunteer ID Card
                            @else
                            {{ $certificate->title }}
                            @endif
                        </dd>
                    </div>
                    @if ($certificate->type === 'nikah_counselor_id')
                    @php $application = \App\Models\MatchmakerApplication::where('user_id', $certificate->user_id)->where('status', 'certified')->first(); @endphp
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Level</dt>
                        <dd>{{ \App\Models\MatchmakerApplication::LEVELS[$application?->level ?? 'nikah_counselor'] }}</dd>
                    </div>
                    @if ($application?->area)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Area</dt>
                        <dd>{{ $application->area }}</dd>
                    </div>
                    @endif
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Issued</dt>
                        <dd>{{ $certificate->issued_at->format('F j, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Code</dt>
                        <dd>{{ $certificate->certificate_number }}</dd>
                    </div>
                </dl>
                @if ($certificate->type === 'nikah_counselor_id')
                <p class="text-xs text-center mt-4"><a href="{{ route('nikah-counselor.code-of-conduct') }}" class="text-teal-700 hover:underline">What is a Nikah Counselor held to? →</a></p>
                @endif
                @else
                <p class="text-red-600 font-medium text-center">❌ No certificate or ID found with this code.</p>
                @endif
            </div>
            @endif
        </div>
    </div>
</x-guest-layout>
