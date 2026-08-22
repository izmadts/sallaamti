<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.leads.index') }}" class="text-gray-400 hover:text-gray-600">Leads</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Add Lead</span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('admin.leads.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="name" value="Name" />
                        <x-text-input id="name" name="name" type="text" class="w-full mt-1" :value="old('name')" required autofocus />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="phone" value="Phone / WhatsApp" />
                            <x-text-input id="phone" name="phone" type="text" class="w-full mt-1" :value="old('phone')" />
                        </div>
                        <div>
                            <x-input-label for="email" value="Email" />
                            <x-text-input id="email" name="email" type="email" class="w-full mt-1" :value="old('email')" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
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
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="source" value="Source" />
                            <select id="source" name="source" required class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                @foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'whatsapp' => 'WhatsApp', 'website' => 'Website', 'phone' => 'Phone', 'referral' => 'Referral', 'manual' => 'Manual', 'other' => 'Other'] as $value => $label)
                                <option value="{{ $value }}" {{ old('source', 'manual') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="assigned_to" value="Assign To" />
                            <select id="assigned_to" name="assigned_to" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                <option value="{{ auth()->id() }}">Myself ({{ auth()->user()->name }})</option>
                                @foreach ($matchmakers as $mm)
                                @if ($mm->id !== auth()->id())
                                <option value="{{ $mm->id }}">{{ $mm->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <x-input-label for="next_follow_up_at" value="Next Follow-up (optional)" />
                        <x-text-input id="next_follow_up_at" name="next_follow_up_at" type="date" class="w-full mt-1" :value="old('next_follow_up_at')" />
                    </div>
                    <div>
                        <x-input-label for="notes" value="Notes" />
                        <textarea id="notes" name="notes" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('admin.leads.index') }}" class="text-gray-600 border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50">Cancel</a>
                        <x-primary-button>Add Lead</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-admin-layout>
