<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Certificate Verification</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                @if ($certificate)
                <p class="text-green-600 font-medium mb-4">✅ This certificate is valid.</p>
                <dl class="text-sm space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Name</dt>
                        <dd>{{ $certificate->user->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Course</dt>
                        <dd>{{ $certificate->course->title }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Issued</dt>
                        <dd>{{ $certificate->issued_at->format('F j, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Certificate No.</dt>
                        <dd>{{ $certificate->certificate_number }}</dd>
                    </div>
                </dl>
                @else
                <p class="text-red-600 font-medium">❌ No certificate found with this number.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>