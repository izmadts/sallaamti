{{-- Pending interests received by this profile, with accept/decline the
     matchmaker can use on the client's behalf. Included only when the
     caller has already confirmed $interests is non-empty — same "no outer
     card wrapper" convention as _payment-form.blade.php, host page supplies
     the wrapper. Expects $interests. --}}
<h3 class="font-semibold text-gray-700 mb-1">💌 {{ __('db.Interest Received') }}</h3>
<p class="text-xs text-gray-500 mb-3">{{ __("db.Someone has expressed interest in this profile. Respond on the client's behalf if they've asked you to — or let them respond themselves from their own account.") }}</p>

<div class="space-y-2">
    @foreach ($interests as $interest)
    <div class="flex flex-wrap items-center justify-between gap-2 bg-gray-50 rounded-lg px-3 py-2">
        <div class="text-sm">
            <span class="font-medium text-gray-800">{{ $interest->sender->user?->name ?? __('db.A candidate') }}</span>
            <span class="text-gray-400"> · {{ __('db.:age yrs, :city', ['age' => $interest->sender->age, 'city' => $interest->sender->city]) }}</span>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('matchmaker.nikah.interests.accept', $interest) }}" onsubmit="return confirm({{ Js::from(__("db.Accept this interest on the client's behalf? Contact details will be shared with both sides immediately.")) }})">
                @csrf
                <button class="text-xs font-semibold px-3 py-1.5 rounded-lg text-white hover:opacity-90 bg-green-600">{{ __('db.Accept') }}</button>
            </form>
            <form method="POST" action="{{ route('matchmaker.nikah.interests.decline', $interest) }}" onsubmit="return confirm({{ Js::from(__("db.Decline this interest on the client's behalf?")) }})">
                @csrf
                <button class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100">{{ __('db.Decline') }}</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
