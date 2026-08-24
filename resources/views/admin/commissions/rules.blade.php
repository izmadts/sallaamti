<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <span class="text-gray-700 font-semibold">Commission Rules</span>
            <span class="text-gray-300">›</span>
            <a href="{{ route('admin.commissions.ledger') }}" class="text-gray-400 hover:text-gray-600">View Ledger →</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif
            @if (session('error'))
            <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
            <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800">
                Rates are grouped by service, then by tier — a "Senior Nikah Counselor" can earn a different rate than a base "Nikah Counselor" for the exact same sale. Renewal rows only apply on a repeat package for the same client. Changes apply to commissions calculated from this point forward — already-created ledger entries keep the rate they were created with.
            </div>

            @foreach ($rules as $groupName => $groupRules)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 border-b font-semibold text-gray-700">{{ $groupName }}</div>

                <div class="grid grid-cols-6 gap-2 px-5 py-2 bg-gray-50/50 text-xs uppercase text-gray-400 border-b">
                    <div class="col-span-2">Tier</div>
                    <div>Type</div>
                    <div>Rate Type</div>
                    <div>Rate <span class="cursor-help normal-case text-gray-400" title="Percentage: enter as a whole number, e.g. 10 means 10%. Fixed: enter the flat Rs. amount, e.g. 200 means Rs. 200.">❓</span></div>
                    <div>Active</div>
                </div>

                <div class="divide-y">
                    @foreach ($groupRules as $rule)
                    <form method="POST" action="{{ route('admin.commissions.rules.update', $rule) }}" class="grid grid-cols-6 gap-2 px-5 py-2.5 items-center text-sm">
                        @csrf
                        <div class="col-span-2">{{ \App\Models\MatchmakerApplication::LEVELS[$rule->tier] ?? $rule->tier }}</div>
                        <div class="text-gray-500">{{ $rule->is_renewal ? 'Renewal' : ($rule->rule_type === 'verified_profile' ? 'Standard' : 'First Purchase') }}</div>
                        <div>
                            <select name="rate_type" class="border-gray-300 rounded text-xs w-full">
                                <option value="percentage" {{ $rule->rate_type === 'percentage' ? 'selected' : '' }}>%</option>
                                <option value="fixed" {{ $rule->rate_type === 'fixed' ? 'selected' : '' }}>Rs. fixed</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" step="0.01" min="0" name="rate_value" value="{{ $rule->rate_value }}" class="border-gray-300 rounded text-xs w-20">
                            <input type="checkbox" name="is_active" value="1" {{ $rule->is_active ? 'checked' : '' }} class="rounded" title="Active">
                        </div>
                        <div>
                            <button class="text-xs font-semibold px-3 py-1 rounded-lg text-white hover:opacity-90" style="background: #0d6b6b">Save</button>
                        </div>
                    </form>
                    @endforeach
                </div>
            </div>
            @endforeach

        </div>
    </div>
</x-admin-layout>
