<x-matchmaker-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('matchmaker.clients.index') }}" class="text-gray-400 hover:text-gray-600">My Clients</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Add Client</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-1">Add a New Client</h2>
            <p class="text-sm text-gray-500 mb-6">They'll be assigned to you automatically.</p>

            @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('matchmaker.clients.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <x-input-label for="name" value="Name" />
                        <x-text-input id="name" name="name" type="text" class="w-full mt-1" :value="old('name')" required autofocus />
                    </div>
                    <div>
                        <x-input-label for="phone" value="Phone / WhatsApp" />
                        <x-text-input id="phone" name="phone" type="text" class="w-full mt-1" :value="old('phone')" />
                    </div>
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="w-full mt-1" :value="old('email')" />
                    </div>
                    <div>
                        <x-input-label for="gender" value="Gender" />
                        <select id="gender" name="gender" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                            <option value="">Unknown</option>
                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="looking_for" value="Looking For" />
                        <select id="looking_for" name="looking_for" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                            <option value="">Unknown</option>
                            <option value="self" {{ old('looking_for') === 'self' ? 'selected' : '' }}>Self</option>
                            <option value="family_member" {{ old('looking_for') === 'family_member' ? 'selected' : '' }}>Family Member</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <x-input-label for="source" value="Source" />
                        <select id="source" name="source" required class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                            @foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'whatsapp' => 'WhatsApp', 'website' => 'Website', 'phone' => 'Phone', 'referral' => 'Referral', 'manual' => 'Manual', 'other' => 'Other'] as $value => $label)
                            <option value="{{ $value }}" {{ old('source') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <x-input-label for="next_follow_up_at" value="Next Follow-up (optional)" />
                        <x-text-input id="next_follow_up_at" name="next_follow_up_at" type="date" class="w-full mt-1" :value="old('next_follow_up_at')" />
                    </div>
                    <div class="col-span-2">
                        <x-input-label for="notes" value="Notes" />
                        <textarea id="notes" name="notes" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('matchmaker.clients.index') }}" class="text-sm text-gray-500 px-4 py-2.5 hover:underline">Cancel</a>
                    <button class="text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:opacity-90 hover:-translate-y-0.5 transition shadow-sm" style="background: var(--mm-plum);">Add Client</button>
                </div>
            </form>
        </div>
    </div>
</x-matchmaker-layout>
