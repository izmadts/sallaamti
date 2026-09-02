<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('db.Verification Fee Payment') }}</h2>
    </x-slot>

    @php
        $feeAmount = $profile->payment_status === 'confirmed' ? $profile->payment_amount : $profile->applicableVerificationFee();
        $whatsappMessage = "Assalam-o-Alaikum, I'm " . auth()->user()->name . " (Sallaamti Profile #" . $profile->id . "). I've paid the Rs. " . number_format($feeAmount) . " Nikah verification fee. Sending my payment receipt here for confirmation.";
    @endphp

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
            <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (in_array($profile->payment_status, ['unpaid', 'rejected']) && !$lead)
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wide px-3 py-1 rounded-full text-white" style="background: var(--teal)">
                    🎯 {{ __('db.Final Step') }}
                </span>
                <span class="text-sm text-gray-500">{{ __('db.This is the only thing standing between you and a live, searchable profile.') }}</span>
            </div>
            @endif

            @if ($lead)
                {{-- A counselor has been hired — the page pivots entirely to
                     package selection/payment. The self-service fee track
                     stays available independently (see nikah.show), this
                     screen just isn't where it's offered anymore once
                     someone's chosen a counselor instead. --}}
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">🤝</span>
                            <div>
                                <h3 class="font-semibold text-gray-800">{{ __('db.Your Nikah Counselor') }}</h3>
                                <p class="text-sm text-gray-500">{{ $lead->assignedTo?->name }}</p>
                            </div>
                        </div>
                        @if (!in_array($lead->package_payment_status, ['submitted', 'confirmed'], true))
                        <form method="POST" action="{{ route('nikah.release-counselor') }}" onsubmit="return confirm('{{ __('db.Go back to self-service? This does not undo any payment already sent.') }}')">
                            @csrf
                            <button type="submit" class="text-xs text-gray-400 hover:text-red-600 underline whitespace-nowrap">{{ __('db.← Go back to self-service') }}</button>
                        </form>
                        @endif
                    </div>

                    @if ($lead->package_payment_status === 'submitted')
                        <div class="p-4 bg-yellow-50 text-yellow-700 rounded text-sm">
                            ⏳ {{ __('db.Your package payment proof has been submitted and is awaiting confirmation by our team.') }}
                        </div>
                    @elseif ($lead->package_payment_status === 'confirmed')
                        <div class="p-4 bg-green-50 text-green-700 rounded text-sm">
                            ✅ {{ __('db.:package package is active.', ['package' => $lead->nikahPackage?->name]) }}
                            @if ($lead->package_expires_at)
                                {{ __('db.Valid until :date.', ['date' => $lead->package_expires_at->format('d M, Y')]) }}
                            @endif
                        </div>
                    @else
                        @if ($lead->package_payment_status === 'rejected')
                        <div class="p-4 bg-red-50 text-red-700 rounded text-sm mb-4">
                            ❌ {{ __('db.Your previous package payment was rejected. Reason:') }} {{ $lead->package_payment_rejection_reason }}
                            <br>{{ __('db.Please choose a package and resubmit below.') }}
                        </div>
                        @endif

                        <p class="text-sm text-gray-500 mb-4">{{ __('db.Choose the package that fits you, send the payment, then upload your receipt below.') }}</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6" x-data="{ selected: null }">
                            @foreach ($packages as $package)
                            <label class="block border-2 rounded-xl p-4 cursor-pointer transition"
                                :class="selected === {{ $package->id }} ? 'border-teal-500 bg-teal-50' : 'border-gray-200 hover:border-gray-300'"
                                x-on:click="selected = {{ $package->id }}">
                                <input type="radio" name="nikah_package_id" value="{{ $package->id }}" form="package-payment-form" class="hidden" x-model.number="selected">
                                <p class="font-semibold text-gray-800">{{ $package->icon }} {{ $package->localizedName() }}</p>
                                @if ($package->localizedTagline())
                                <p class="text-xs text-gray-500 italic mt-0.5">{{ $package->localizedTagline() }}</p>
                                @endif
                                <p class="text-lg font-bold text-gray-800 mt-2">Rs. {{ number_format($package->price) }}
                                    <span class="text-xs font-normal text-gray-400">/ {{ __('db.:days days', ['days' => $package->duration_days]) }}</span>
                                </p>
                            </label>
                            @endforeach
                        </div>

                        <div class="bg-gray-50 border border-gray-200 rounded p-4 text-sm mb-6 space-y-4">
                            @if (setting('jazzcash_number'))
                            <div>
                                <p class="font-bold mb-1">📱 {{ __('db.JazzCash') }}</p>
                                <p class="text-gray-600 mb-0 flex items-center gap-1">
                                    {{ setting('jazzcash_number') }}
                                    <x-copy-button :value="setting('jazzcash_number')" />
                                </p>
                                <p class="font-semibold text-gray-700 mb-0">{{ setting('jazzcash_account_title') }}</p>
                            </div>
                            @endif
                            @if (setting('easypaisa_number'))
                            <div>
                                <p class="font-bold mb-1">💚 {{ __('db.EasyPaisa') }}</p>
                                <p class="text-gray-600 mb-0 flex items-center gap-1">
                                    {{ setting('easypaisa_number') }}
                                    <x-copy-button :value="setting('easypaisa_number')" />
                                </p>
                            </div>
                            @endif
                            @if (setting('bank_name'))
                            <div>
                                <p class="font-bold mb-1">🏦 {{ __('db.Bank Transfer') }}</p>
                                <p class="text-gray-600 text-sm mb-0">{{ __('db.Bank:') }} {{ setting('bank_name') }}</p>
                                <p class="text-gray-600 text-sm mb-0">{{ __('db.Account Title:') }} {{ setting('bank_account_title') }}</p>
                                @if (setting('bank_account_number'))
                                <p class="text-gray-600 text-sm mb-0 flex items-center gap-1">
                                    {{ __('db.Account No:') }} {{ setting('bank_account_number') }}
                                    <x-copy-button :value="setting('bank_account_number')" />
                                </p>
                                @endif
                                @if (setting('bank_account_iban'))
                                <p class="text-gray-600 text-sm mb-0 flex items-center gap-1">
                                    {{ __('db.IBAN:') }} {{ setting('bank_account_iban') }}
                                    <x-copy-button :value="setting('bank_account_iban')" />
                                </p>
                                @endif
                            </div>
                            @endif
                            @if (!setting('jazzcash_number') && !setting('easypaisa_number') && !setting('bank_name'))
                            <p class="text-red-600">{{ __('db.Payment details have not been configured yet. Please contact support before sending any payment.') }}</p>
                            @endif
                        </div>

                        <form id="package-payment-form" method="POST" action="{{ route('nikah.package-payment.store') }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <x-input-label :value="__('db.Payment Method')" />
                                <select name="payment_method" required class="border-gray-300 rounded-md w-full mt-1">
                                    <option value="jazzcash">{{ __('db.JazzCash') }}</option>
                                    <option value="easypaisa">{{ __('db.EasyPaisa') }}</option>
                                    <option value="bank_transfer">{{ __('db.Bank Transfer') }}</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label :value="__('db.Payment Screenshot')" />
                                <x-photo-upload-field name="payment_screenshot" :required="true" :allow-camera="false" />
                            </div>
                            <x-primary-button>✅ {{ __('db.Submit Package Payment') }}</x-primary-button>
                        </form>
                    @endif
                </div>
            @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

                {{-- Self-service track --}}
                <div class="rounded-xl border-2 p-5 space-y-4" style="border-color: var(--gold); background: linear-gradient(135deg, #fffbeb 0%, #f0fdfa 100%)">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl shrink-0">🛡️</span>
                        <div>
                            <h3 class="font-bold text-base mb-1" style="color: var(--teal)">
                                {{ __('db.Continue on your own') }}
                            </h3>
                            <p class="text-sm text-gray-700 leading-relaxed">
                                {{ __("db.This small, one-time fee is not a service charge — it's how we keep Sallaamti's Nikah section safe. This fee filters out fake accounts and confirms every profile belongs to a real, serious family — so once verified, you speak directly with the interested family, with no middleman in between.") }}
                            </p>
                            <ul class="mt-3 space-y-1 text-sm text-gray-700">
                                <li>✅ {{ __('db.Charged only once — never again') }}</li>
                                <li>✅ {{ __('db.Zero Nikah Counselor or agent commission') }}</li>
                                <li>✅ {{ __('db.Direct contact with the interested family') }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg p-4">
                        <h4 class="font-semibold text-gray-700 mb-2">{{ __('db.Fee Amount:') }} {{ __('db.Rs. :amount', ['amount' => number_format($feeAmount)]) }}</h4>

                        <div class="bg-gray-50 border border-gray-200 rounded p-4 text-sm mb-4 space-y-4">
                            @if (setting('jazzcash_number'))
                            <div>
                                <p class="font-bold mb-1" style="color: var(--gold)">📱 {{ __('db.JazzCash') }}</p>
                                <p class="text-gray-600 mb-0 flex items-center gap-1">
                                    {{ setting('jazzcash_number') }}
                                    <x-copy-button :value="setting('jazzcash_number')" />
                                </p>
                                <p class="font-semibold text-gray-700 mb-0">{{ setting('jazzcash_account_title') }}</p>
                            </div>
                            @endif
                            @if (setting('bank_name'))
                            <div>
                                <p class="font-bold mb-1" style="color: var(--gold)">🏦 {{ __('db.Bank Transfer') }}</p>
                                <p class="text-gray-600 text-sm mb-0">{{ __('db.Bank:') }} {{ setting('bank_name') }}</p>
                                <p class="text-gray-600 text-sm mb-0">{{ __('db.Account Title:') }} {{ setting('bank_account_title') }}</p>
                                <p class="text-gray-600 text-sm mb-0 flex items-center gap-1">
                                    {{ __('db.Account No:') }} {{ setting('bank_account_number') }}
                                    <x-copy-button :value="setting('bank_account_number')" />
                                </p>
                                <p class="text-gray-600 text-sm mb-0 flex items-center gap-1">
                                    {{ __('db.IBAN:') }} {{ setting('bank_account_iban') }}
                                    <x-copy-button :value="setting('bank_account_iban')" />
                                </p>
                            </div>
                            @endif
                            @if (!setting('jazzcash_number') && !setting('bank_name'))
                            <p class="text-red-600">{{ __('db.Payment details have not been configured yet. Please contact support before sending any payment.') }}</p>
                            @endif
                        </div>

                        @if (setting('social_whatsapp'))
                        <a href="{{ whatsapp_link($whatsappMessage) }}" target="_blank"
                            class="flex items-center gap-3 rounded-xl p-3 mb-4 transition hover:shadow-md" style="background: #f0fdf4; border: 1px solid #bbf7d0">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl shrink-0 bg-white">💬</div>
                            <div>
                                <p class="font-bold text-gray-800 text-xs">{{ __('db.Prefer WhatsApp? Send your receipt there instead') }}</p>
                            </div>
                        </a>
                        @endif

                        @if ($profile->payment_status === 'submitted')
                        <div class="p-4 bg-yellow-50 text-yellow-700 rounded text-sm">
                            ⏳ {{ __('db.Your payment proof has been submitted and is awaiting confirmation by our team.') }}
                        </div>
                        @elseif ($profile->payment_status === 'confirmed')
                        <div class="p-4 bg-green-50 text-green-700 rounded text-sm">
                            ✅ {{ __('db.Payment confirmed. Your profile is now awaiting admin verification.') }}
                        </div>
                        @else
                        @if ($profile->payment_status === 'rejected')
                        <div class="p-4 bg-red-50 text-red-700 rounded text-sm mb-4">
                            ❌ {{ __('db.Your previous payment proof was rejected. Reason:') }} {{ $profile->payment_rejection_reason }}
                            <br>{{ __('db.Please resubmit below.') }}
                        </div>
                        @endif

                        <form method="POST" action="{{ route('nikah.payment.store') }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <x-input-label :value="__('db.Payment Method')" />
                                <select name="payment_method" required class="border-gray-300 rounded-md w-full mt-1">
                                    <option value="jazzcash">{{ __('db.JazzCash') }}</option>
                                    <option value="bank_transfer">{{ __('db.Bank Transfer') }}</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label :value="__('db.Payment Screenshot')" />
                                <x-photo-upload-field name="payment_screenshot" :required="true" :allow-camera="false" />
                            </div>
                            <x-primary-button>✅ {{ __('db.Complete My Verification — Submit Payment') }}</x-primary-button>
                        </form>
                        @endif
                    </div>
                </div>

                {{-- Counselor-assisted track --}}
                <div class="rounded-xl border-2 border-teal-200 bg-white p-5 space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl shrink-0">🤝</span>
                        <div>
                            <h3 class="font-bold text-base mb-1" style="color: var(--teal)">{{ __('db.Get a dedicated Nikah Counselor') }}</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                {{ __('db.Prefer a consultant to manage your search for you — reviewing profiles, arranging introductions, and guiding your family through the process? Choose a counselor below.') }}
                            </p>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('nikah.payment') }}" class="flex flex-wrap gap-2">
                        <input type="text" name="city" value="{{ request('city') }}" placeholder="{{ __('db.Filter by city') }}" class="border-gray-300 rounded-md text-sm flex-1 min-w-[140px]">
                        <select name="gender" class="border-gray-300 rounded-md text-sm">
                            <option value="">{{ __('db.Any gender') }}</option>
                            <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>{{ __('db.Male') }}</option>
                            <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>{{ __('db.Female') }}</option>
                        </select>
                        <button type="submit" class="text-sm px-3 py-1.5 rounded-md border border-gray-300 text-gray-600 hover:bg-gray-50">{{ __('db.Filter') }}</button>
                    </form>

                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse ($counselors as $application)
                        <div class="flex items-center justify-between gap-3 border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold shrink-0" style="background: var(--teal)">
                                    {{ strtoupper(substr($application->user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $application->user->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $application->user->city ?? __('db.City not set') }} · {{ ucfirst($application->level) }}</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('nikah.hire-counselor') }}" onsubmit="return confirm('{{ __('db.Hire :name as your Nikah Counselor?', ['name' => $application->user->name]) }}')">
                                @csrf
                                <input type="hidden" name="counselor_id" value="{{ $application->user->id }}">
                                <button type="submit" class="text-xs font-semibold px-3 py-2 rounded-lg text-white hover:opacity-90" style="background: var(--teal)">{{ __('db.Hire') }}</button>
                            </form>
                        </div>
                        @empty
                        <p class="text-sm text-gray-400 text-center py-6">{{ __('db.No Nikah Counselors match that filter right now.') }}</p>
                        @endforelse
                    </div>

                    <a href="{{ route('nikah.packages') }}" target="_blank" class="text-xs text-teal-600 hover:underline">{{ __('db.Compare package pricing →') }}</a>
                </div>

            </div>
            @endif

        </div>
    </div>
</x-app-layout>
