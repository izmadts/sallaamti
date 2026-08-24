<x-guest-layout title="Nikah Counselor Agreement — Sallaamti" description="Review and accept your Sallaamti Nikah Counselor Agreement and confidentiality agreement.">

    <div class="py-12 bg-cream">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (!$unlocked)

            <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center text-3xl mb-4" style="background: var(--cream);">🔒</div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Nikah Counselor Agreement</h3>
                <p class="text-sm text-gray-500 mb-6">Hello {{ $application->full_name }} — enter the <strong>last 7 digits</strong> of the mobile number on your application to review and accept your agreement.</p>

                @if (($error ?? null) || $errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm text-left">
                    {{ $error ?? $errors->first() }}
                </div>
                @endif

                <form method="POST" action="{{ $verifyUrl }}" class="flex flex-col items-center gap-3">
                    @csrf
                    <input type="text" name="last7" inputmode="numeric" pattern="[0-9]{7}" maxlength="7" minlength="7" required placeholder="e.g. 3001234"
                        class="border-gray-300 rounded-lg text-center text-lg tracking-widest w-48" autofocus>
                    <button class="text-white text-sm font-semibold px-6 py-3 rounded-lg hover:opacity-90 transition" style="background: #0d6b6b">Unlock Agreement</button>
                </form>
            </div>

            @else

            <div class="rounded-xl p-4 flex items-start gap-3 bg-white border" style="border-color: #0d6b6b33">
                <span class="text-xl">📜</span>
                <p class="text-sm text-gray-700">
                    Hello {{ $application->full_name }} — please read this carefully before accepting. This is a real agreement between you and Sallaamti.
                </p>
            </div>

            @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Plain-language summary --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">📋 In Plain Words</h4>
                <ul class="text-sm text-gray-600 space-y-2 list-disc list-inside">
                    <li>You represent Sallaamti — you introduce people and help them register, you don't personally decide or guarantee any match.</li>
                    <li>You're paid commission by Sallaamti according to its published rates — you never collect cash from a client yourself.</li>
                    <li>You never collect a client's CNIC or documents over WhatsApp — everything goes through Sallaamti's secure system.</li>
                    <li>Client information is confidential — no personal spreadsheets, no private database, no sharing outside Sallaamti.</li>
                    <li>Every client you bring in belongs to Sallaamti, not to you personally — if you ever leave, Sallaamti keeps the relationship.</li>
                    <li>You're never paid for recruiting other counselors — commission is only for your own verified work.</li>
                    <li>This agreement can be ended by either side, and you must stop using the Sallaamti name and return/delete any client data immediately after.</li>
                </ul>
            </div>

            {{-- Full agreement --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Nikah Counselor Agreement</h4>
                <div class="text-sm text-gray-600 space-y-4 leading-relaxed">
                    <p><strong>1. Role & Scope.</strong> You act as an independent Sallaamti Nikah Counselor, introducing prospective clients to the Sallaamti platform, assisting them with registration and verification, and facilitating the matchmaking process described in Sallaamti's Nikah Counselor Code of Conduct. You do not have authority to make decisions on Sallaamti's behalf, guarantee any outcome, or bind Sallaamti to any promise.</p>

                    <p><strong>2. Commission & Payment.</strong> You are compensated solely through commission on verified, Sallaamti-confirmed activity, at the rates published in your Sallaamti dashboard from time to time. Commission is calculated automatically, subject to a review hold period before approval, and paid to the bank/mobile-wallet account you provide. You will never collect payment directly from a client — all client payments must go through Sallaamti's own payment channels.</p>

                    <p><strong>3. Confidentiality & Data Protection.</strong> Any client information you access (names, contact details, documents, preferences, or any other personal data) is strictly confidential. You must not copy it into any personal record — no spreadsheet, notebook, phone contacts list, or third-party app — and must not disclose it to anyone outside Sallaamti except as required to perform your role.</p>

                    <p><strong>4. No Cash Collection.</strong> You must never accept cash or any form of payment directly from a client on Sallaamti's behalf.</p>

                    <p><strong>5. No Guarantees.</strong> You must never promise or guarantee a match, marriage, or any specific outcome, and must never make representations about a candidate's character, income, or family beyond what is stated in their verified Sallaamti profile.</p>

                    <p><strong>6. Client Ownership.</strong> Every client, lead, and profile you register or work with is and remains the property of Sallaamti, not your personal client. If this Agreement ends for any reason, all client relationships remain with Sallaamti and may be reassigned.</p>

                    <p><strong>7. No Recruitment Commission.</strong> You are compensated only for your own direct client-facing work. You will never receive commission, bonus, or any payment for recruiting, referring, or being connected to another counselor's activity.</p>

                    <p><strong>8. Brand Use.</strong> You may identify yourself as a "Sallaamti Nikah Counselor" using materials Sallaamti provides (your ID card, certificate, referral link/QR code). You may not create your own marketing materials using the Sallaamti name or logo without prior written approval.</p>

                    <p><strong>9. Conduct.</strong> You must not discriminate against or harass any client, submit fake or misleading profiles, or advertise your services in a way that misrepresents Sallaamti or makes claims Sallaamti has not authorized.</p>

                    <p><strong>10. Termination.</strong> Either you or Sallaamti may end this Agreement at any time, with or without cause. Upon termination, you must immediately stop representing yourself as a Sallaamti Nikah Counselor, return or permanently delete any client data or materials in your possession, and your access to Sallaamti systems will be revoked. Any commission already earned and approved before termination remains payable.</p>

                    <p><strong>11. Dispute Resolution.</strong> Both parties will first attempt to resolve any disagreement in good faith directly. Unresolved disputes will be handled under the applicable laws of Pakistan.</p>

                    <p><strong>12. Intellectual Property.</strong> The Sallaamti name, logo, certificate design, platform, and all related materials remain the property of Sallaamti at all times.</p>
                </div>
            </div>

            {{-- NDA --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h4 class="font-semibold text-gray-700 mb-3 border-b pb-2">Confidentiality / NDA</h4>
                <div class="text-sm text-gray-600 space-y-4 leading-relaxed">
                    <p><strong>1. Confidential Information.</strong> This includes any client's personal, contact, financial, or identity information; Sallaamti's internal processes, commission structure, and business data; and any other non-public information you access through your role.</p>

                    <p><strong>2. No Independent Database.</strong> You must not build, maintain, export, or copy Confidential Information into any system, device, or record outside Sallaamti's own platform.</p>

                    <p><strong>3. No Disclosure.</strong> You must not share Confidential Information with anyone who does not have a legitimate need to know it as part of Sallaamti's own operations.</p>

                    <p><strong>4. Return or Deletion.</strong> Upon request or upon termination of your Agreement, you must immediately return or permanently delete all Confidential Information in your possession, in any form.</p>

                    <p><strong>5. Survival.</strong> Your confidentiality obligations continue indefinitely after this Agreement ends.</p>
                </div>
            </div>

            <p class="text-xs text-gray-400 text-center">Sallaamti Nikah Counselor onboarding — full details available in the Nikah Counselor Code of Conduct.</p>

            {{-- Accept --}}
            <div class="rounded-xl p-6" style="background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
                <form method="POST" action="{{ $acceptUrl }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="last7" value="{{ $last7 ?? '' }}">

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="agreement_accepted" value="1" required class="mt-0.5">
                        <span class="text-white text-sm">I have read and accept the Nikah Counselor Agreement above.</span>
                    </label>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="nda_accepted" value="1" required class="mt-0.5">
                        <span class="text-white text-sm">I have read and accept the Confidentiality / NDA terms above.</span>
                    </label>

                    <button class="w-full bg-white text-gray-800 font-semibold py-3 rounded-lg hover:opacity-90 transition">I Accept — Submit</button>
                </form>
            </div>

            @endif

        </div>
    </div>
</x-guest-layout>
