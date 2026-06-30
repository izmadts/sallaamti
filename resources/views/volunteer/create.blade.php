<x-guest-layout>
    <div class="card" style="width: 20rem; margin: auto; margin-top: 200px; padding: 30px;">
        <h2 class="font-semibold mb-4">Join Sallaamti as a Volunteer</h2>

        @if (session('status'))
        <div class="p-4 bg-green-50 text-green-700 rounded mb-4">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('volunteer.store') }}" class="space-y-4 bg-white p-6 rounded-lg shadow-sm">
            @csrf
            <div><label class="mb-1 mt-2">Full Name</label><input name="name" class="form-control" required /></div>
            <div><label class="mb-1 mt-2">Email </label><input name="email" type="email" class="form-control" required /></div>
            <div><label class="mb-1 mt-2">Phone</label><input name="phone" class="form-control" required /></div>
            <div><label class="mb-1 mt-2">City </label><input name="city" class="form-control" /></div>
            <div>
                <label class="mb-1 mt-2">Area of Interest </label>
                <select name="area_of_interest" class="border-gray-300 rounded-md form-select">
                    <option value="Teaching">Teaching (Quran/Islamic Studies)</option>
                    <option value="Tech">Tech / Web Development</option>
                    <option value="Counseling">Family Counseling</option>
                    <option value="Fundraising">Fundraising</option>
                    <option value="Events">Events & Outreach</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div><label class="mb-1 mt-2">Message (optional)</label>
                <textarea name="message" rows="3" class="border-gray-300 rounded-md form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-lg mt-3">Submit Application</button>
        </form>
        <a href="{{ route('index') }}" class="inline-block mt-6 bg-light text-dark mt-3 px-5 py-2 rounded">
            <<< Back to Home</a>
    </div>
</x-guest-layout>