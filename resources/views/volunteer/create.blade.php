<x-guest-layout>
    <div class="max-w-xl mx-auto py-12 px-4">
        <h2 class="text-2xl font-semibold mb-4">Join Sallaamti as a Volunteer</h2>

        @if (session('status'))
        <div class="p-4 bg-green-50 text-green-700 rounded mb-4">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('volunteer.store') }}" class="space-y-4 bg-white p-6 rounded-lg shadow-sm">
            @csrf
            <div><x-input-label value="Full Name" /><x-text-input name="name" class="w-full mt-1" required /></div>
            <div><x-input-label value="Email" /><x-text-input name="email" type="email" class="w-full mt-1" required /></div>
            <div><x-input-label value="Phone" /><x-text-input name="phone" class="w-full mt-1" required /></div>
            <div><x-input-label value="City" /><x-text-input name="city" class="w-full mt-1" /></div>
            <div>
                <x-input-label value="Area of Interest" />
                <select name="area_of_interest" class="border-gray-300 rounded-md w-full mt-1">
                    <option value="Teaching">Teaching (Quran/Islamic Studies)</option>
                    <option value="Tech">Tech / Web Development</option>
                    <option value="Counseling">Family Counseling</option>
                    <option value="Fundraising">Fundraising</option>
                    <option value="Events">Events & Outreach</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div><x-input-label value="Message (optional)" /><textarea name="message" rows="3" class="border-gray-300 rounded-md w-full mt-1"></textarea></div>
            <x-primary-button>Submit Application</x-primary-button>
        </form>
        <a href="{{ route('index') }}" class="inline-block mt-6 bg-gray-400 text-black text-sm px-5 py-2 rounded">Back to Home</a>
    </div>
</x-guest-layout>