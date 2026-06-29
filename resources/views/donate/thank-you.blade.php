<x-guest-layout>
    <div class="max-w-xl mx-auto py-12 px-4 text-center">
        <div class="bg-white rounded-lg shadow-sm p-8">
            <div class="text-5xl mb-4">🤲</div>
            <h2 class="text-2xl font-semibold mb-2">Jazak Allah Khairah, {{ $donation->donor_name }}!</h2>
            <p class="text-gray-500 mb-4">Your donation of Rs. {{ number_format($donation->amount) }} has been received and is pending confirmation.</p>
            <p class="text-sm text-gray-400">Donation Reference: {{ $donation->donation_number }}</p>
            <a href="{{ route('index') }}" class="inline-block mt-6 bg-gray-800 text-white text-sm px-5 py-2 rounded">Back to Home</a>
        </div>
    </div>
</x-guest-layout>