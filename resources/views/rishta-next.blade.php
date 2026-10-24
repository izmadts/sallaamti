{{-- resources/views/rishta-next.blade.php
     "Next step" page after a Facebook/Instagram lead form (thank-you button points here).
     Lets the person choose: talk on WhatsApp, or get the Sallaamti app from Google Play. Kept out of search. --}}
@php
    $waText = 'السلام علیکم! میں نے سلامتی نکاح کا فارم بھرا ہے، نکاح پروفائل بنانی ہے۔';
    $waUrl = 'https://wa.me/923391114962?text=' . rawurlencode($waText);
    $appUrl = setting('mobile_app_android_url', 'https://play.google.com/store/apps/details?id=com.sallaamti.app&pcampaignid=web_share');
@endphp
<x-guest-layout :title="__('db.Next step | Sallaamti Nikah')" :description="__('db.Thank you for filling the Sallaamti Nikah form. Talk to us on WhatsApp or get the Sallaamti app from Google Play.')">
    @section('robots', 'noindex, follow')

    <section class="page-hero relative overflow-hidden flex items-center"
        style="min-height: 80vh; background: linear-gradient(160deg, #022c22 0%, #0d6b6b 100%);">
        <div class="max-w-xl mx-auto px-5 py-16 relative z-10 text-center w-full" dir="rtl" lang="ur">
            <div class="text-5xl mb-4">🤍</div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-3" style="line-height:1.6">شکریہ! آپ کا فارم مل گیا</h1>
            <p class="text-white/80 text-lg mb-10" style="line-height:2">
                اگلا قدم آپ چنیں: ہم سے واٹس ایپ پر بات کریں، یا سلامتی ایپ لے کر خود پروفائل بنائیں۔
            </p>

            <div class="flex flex-col gap-4">
                <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                    class="block w-full rounded-2xl px-6 py-5 text-xl font-bold text-white shadow-lg transition hover:opacity-90"
                    style="background:#25D366">
                    💬 واٹس ایپ پر بات کریں
                    <span class="block text-sm font-normal text-white/90 mt-1" dir="ltr">0339 1114962</span>
                </a>
                <a href="{{ $appUrl }}" target="_blank" rel="noopener"
                    class="block w-full rounded-2xl px-6 py-5 text-xl font-bold shadow-lg transition hover:opacity-90"
                    style="background:#d4af37; color:#022c22">
                    📲 Google Play سے سلامتی ایپ لیں
                    <span class="block text-sm font-normal mt-1" style="color:#022c22cc">اینڈرائیڈ فون کے لیے</span>
                </a>
            </div>

            {{-- What Sallaamti is (owner 24 Oct: "just icons with text") --}}
            <p class="text-white/70 text-base mt-12 mb-4">سلامتی میں اور کیا ہے؟</p>
            <div class="grid grid-cols-3 gap-3">
                @foreach ([
                    ['📖', 'قرآن کورسز', '/courses'],
                    ['🎥', 'لائیو کلاسز', '/quran-live'],
                    ['💍', 'حلال رشتہ', '/nikah/packages'],
                    ['💻', 'ڈیجیٹل اسکلز', '/skills'],
                    ['🤝', 'فیملی کونسلنگ', '/support/book'],
                    ['💧', 'خدمتِ خلق', '/wall?tag=Activity'],
                ] as [$icon, $label, $path])
                    <a href="{{ url($path) }}" class="block rounded-xl px-2 py-4 text-white hover:bg-white/10 transition"
                        style="background: rgba(255,255,255,0.06); border: 1px solid rgba(212,175,55,0.35)">
                        <span class="block text-3xl mb-1">{{ $icon }}</span>
                        <span class="block text-sm" style="line-height:1.9">{{ $label }}</span>
                    </a>
                @endforeach
            </div>

            <a href="{{ url('/') }}" class="inline-block mt-10 text-white/60 text-sm hover:text-white">www.sallaamti.com</a>
        </div>
    </section>
</x-guest-layout>
