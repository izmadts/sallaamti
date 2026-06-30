<x-guest-layout>
    <div class="card" style="width: 30rem; margin: auto; margin-top: 200px; padding: 20px;">
        
            <h2 class="font-semibold mb-2">Donate to Sallaamti</h2>
            <p class="mb-6">Your contribution supports Quran education, family support, and community welfare programs.</p>

            <form method="POST" action="{{ route('donate.store') }}" enctype="multipart/form-data" >
                @csrf
                <div><label class="mb-1 mt-2">Your Name</label>
                    <input name="donor_name" class="form-control mb-3" required />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="mb-1 mt-2">Email (optional)</label>
                        <input name="email" type="email" class="form-control mb-3" />
                    </div>
                    <div><label class="mb-1 mt-2">Phone (optional)</label>
                        <input name="phone" class="form-control mb-3" />
                    </div>
                </div>
                <div><label class="mb-1 mt-2">Donation Amount (Rs.)</label>
                    <input name="amount" type="number" step="0.01" class="form-control mb-3" required />
                </div>
                <div>
                    <label class="mb-1 mt-2">Purpose</label>
                    <select name="purpose" class="form-select mb-3">
                        <option value="General">General / Wherever needed most</option>
                        <option value="Quran Education">Quran Education</option>
                        <option value="Orphan & Needy Support">Orphan & Needy Support</option>
                        <option value="Nikah Fund">Nikah / Marriage Support</option>
                        <option value="Mosque & Facilities">Mosque & Facilities</option>
                    </select>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded p-4 text-sm">
                    <img src="{{ asset('images/jazzcash.png') }}" alt="Icon Description" class="img-fluid" style="max-height: 50px;">
                    <p><strong>JazzCash</strong> 03006163221</p>
                    <p><strong>Account Title:</strong> Mubashar Irshad</p>
                    <hr class="mb-4 mt-4 border-e-400">
                    <img src="{{ asset('images/meezan.png') }}" alt="Icon Description" class="img-fluid" style="max-height: 50px;">
                    <p><strong>Meezan Bank</strong> 0503009052782</p>
                    <p><strong>Account Title:</strong> Sallaamti</p>
                </div>

                <div>
                    <label class="mb-1 mt-2">Payment Method</label>
                    <select name="payment_method" class="form-select mb-3" required>
                        <option value="jazzcash">JazzCash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>
                <div><label class="mb-1 mt-2"> Transaction Reference</label><input name="payment_reference" class="form-control mb-3" required /></div>
                <div><label class="mb-1 mt-2"> Payment Screenshot</label><input type="file" name="payment_screenshot" accept="image/*" class="form-control mb-3" required></div>

                <button type="submit" class="btn btn-primary btn-block btn-lg mt-2">Submit Donation</button>
            </form>
    </div>
</x-guest-layout>