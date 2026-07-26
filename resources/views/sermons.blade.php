{{-- resources/views/sermons.blade.php --}}
<x-guest-layout>
    <section class="page-hero relative overflow-hidden flex items-center"
        style="min-height: 280px; background: linear-gradient(135deg, #1a1a2e 0%, #0d6b6b 100%);">
        <div class="max-w-7xl mx-auto px-4 py-16 relative z-10 text-center w-full">
            <span class="section-eyebrow" style="color: rgba(255,255,255,0.7)">Islamic Reminders</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-2 mb-3">🕌 Bayans & Sermons</h1>
            <p class="text-white/70 text-lg max-w-xl mx-auto">
                Weekly Islamic reminders, Friday khutbahs and spiritual talks to strengthen your faith.
            </p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">

            {{-- Quran Ayah --}}
            <div class="text-center mb-16 max-w-2xl mx-auto">
                <p class="text-2xl mb-3" style="font-family: 'Scheherazade New', serif; color: var(--teal)">
                    إِنَّ هَٰذَا الْقُرْآنَ يَهْدِي لِلَّتِي هِيَ أَقْوَمُ
                </p>
                <p class="text-gray-500 italic">"Indeed, this Qur'an guides to that which is most suitable." — Surah Al-Isra: 9</p>
            </div>

            <div class="text-center py-10">
                <div class="text-6xl mb-4">🎙️</div>
                <h3 class="text-2xl font-bold text-gray-700 mb-3">Sermon Archive Coming Soon</h3>
                <p class="text-gray-500 max-w-md mx-auto mb-8">
                    We're building a library of Islamic reminders and sermons. Follow us on social media for weekly reminders.
                </p>
                <div class="flex gap-3 justify-center flex-wrap">
                    <a href="{{ setting('social_youtube', '#') }}" target="_blank" class="btn-base btn-teal px-6 py-2">
                        <i class="fab fa-youtube mr-2"></i>YouTube Channel
                    </a>
                    <a href="{{ setting('social_facebook', '#') }}" target="_blank" class="btn-base btn-gold px-6 py-2">
                        <i class="fab fa-facebook mr-2"></i>Facebook Page
                    </a>
                    <a href="{{ setting('social_tiktok', '#') }}" target="_blank" class="btn-base px-6 py-2 text-white" style="background: #000">
                        <i class="fab fa-tiktok mr-2"></i>TikTok
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-guest-layout>