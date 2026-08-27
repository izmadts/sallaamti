<x-guest-layout :title="__('db.Nikah Counselor Code of Conduct')" :description="__('db.What Sallaamti holds every certified Nikah Counselor to.')">

    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 220px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="max-w-7xl mx-auto px-4 py-16 relative z-10 text-center w-full">
            <span class="section-eyebrow" style="color: #d8c48a">{{ __('db.Trust & Transparency') }}</span>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mt-2">{{ __('db.Nikah Counselor Code of Conduct') }}</h1>
        </div>
    </section>

    <section class="py-16 bg-cream">
        <div class="max-w-3xl mx-auto px-4 space-y-6">

            <div class="bg-white rounded-2xl shadow-sm p-8">
                <p class="text-sm text-gray-600 leading-relaxed mb-6">{{ __("db.Every Sallaamti Nikah Counselor is certified only after identity verification, an interview, and formally accepting a written Agreement and confidentiality commitment. This page is what we hold them to — if a counselor you're working with does anything below, tell us.") }}</p>

                <div class="space-y-5">
                    @foreach ([
                    ['🔒', __('db.Never collects your CNIC over WhatsApp'), __("db.Identity documents go straight into Sallaamti's secure system — never through a counselor's personal phone.")],
                    ['💳', __('db.Never collects payment in cash'), __('db.All payments go through Sallaamti directly. A counselor asking you to pay them personally is against the rules.')],
                    ['🤐', __('db.Keeps your information confidential'), __("db.No personal spreadsheets, no private contact lists — your information stays inside Sallaamti's system.")],
                    ['🚫', __('db.Never guarantees a match'), __('db.A counselor can share why a profile seems compatible — they cannot promise or guarantee marriage, character, or income.')],
                    ['⚖️', __('db.Treats every client fairly'), __('db.No discrimination, no harassment, no pressure.')],
                    ['🏢', __('db.Represents Sallaamti, not themselves'), __('db.Your relationship belongs to Sallaamti as an organization — not to any one individual.')],
                    ] as $item)
                    <div class="flex items-start gap-4">
                        <span class="text-2xl flex-shrink-0">{{ $item[0] }}</span>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $item[1] }}</p>
                            <p class="text-sm text-gray-500">{{ $item[2] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-8 text-center">
                <p class="text-sm text-gray-600 mb-4">{{ __("db.Want to verify a specific counselor's certificate?") }}</p>
                <a href="{{ route('certificate.verify') }}" class="btn-base btn-teal px-6 py-3 inline-block">{{ __('db.Verify a Counselor ID') }} →</a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-8 text-center">
                <p class="text-sm text-gray-600 mb-4">{{ __('db.Something feel wrong? Tell us directly.') }}</p>
                <a href="{{ whatsapp_link() }}" target="_blank" class="btn-base px-6 py-3 inline-block border border-gray-300 text-gray-700 hover:border-[--teal]">
                    <i class="fab fa-whatsapp mr-2"></i>{{ __('db.Report a Concern') }}
                </a>
            </div>

        </div>
    </section>
</x-guest-layout>
