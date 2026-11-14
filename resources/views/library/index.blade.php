{{-- Sallaamti Library (owner, 13 Nov 2026): free Urdu booklets + Sallaamti documents. Mobile first. --}}
<x-guest-layout :title="'کتب خانہ — مفت کتابچے | Sallaamti Library'" :description="'سلامتی کے مفت اردو کتابچے: والدین، بیٹی، بیٹا، بیوہ و طلاق یافتہ بہنوں کے لیے نکاح کی رہنمائی، منہجِ سلامتی اور منشور۔ ڈاؤن لوڈ کریں اور شیئر کریں۔'">

    @push('schema')
    {{-- Nastaliq-style Urdu titles on the Library pages — see library/show.blade.php's own comment on this same push. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;700&display=swap" rel="stylesheet">
    @endpush

    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 240px; background: linear-gradient(135deg, #022c22 0%, #065f46 100%);">
        <div class="max-w-7xl mx-auto px-4 py-14 relative z-10 text-center w-full" dir="rtl">
            <span class="section-eyebrow" style="color:#e9c46a">Sallaamti Library</span>
            <h1 class="text-3xl md:text-5xl font-extrabold text-white mt-2 mb-3" style="line-height:1.6; font-family:'Noto Nastaliq Urdu', serif">کتب خانہ</h1>
            <p class="text-white/80 text-lg max-w-2xl mx-auto" style="line-height:1.9">مفت کتابچے: پڑھیں، ڈاؤن لوڈ کریں اور اپنے خاندان سے شیئر کریں</p>
        </div>
    </section>

    <section class="py-10 bg-white" dir="rtl">
        <div class="max-w-7xl mx-auto px-4">
            @if ($groups->isEmpty())
                <p class="text-center text-gray-500 py-10">جلد آ رہا ہے، ان شاء اللہ۔</p>
            @endif

            {{-- Quick jump to sections (scrolls sideways on phones) --}}
            @if ($groups->count() > 1)
            <div class="flex gap-2 mb-8" style="overflow-x:auto; -webkit-overflow-scrolling:touch; padding-bottom:4px">
                @foreach ($categories as $key => $cat)
                    @if ($groups->has($key))
                    <a href="#{{ $key }}" class="text-sm font-semibold px-4 py-2 rounded-full" style="white-space:nowrap; background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0">{{ $cat['icon'] }} {{ $cat['ur'] }}</a>
                    @endif
                @endforeach
            </div>
            @endif

            @foreach ($categories as $key => $cat)
                @if ($groups->has($key))
                <div id="{{ $key }}" class="mb-12" style="scroll-margin-top: 90px">
                    <h2 class="text-2xl font-bold mb-5" style="color:#065f46; line-height:1.7">{{ $cat['icon'] }} {{ $cat['ur'] }} <span class="text-sm font-normal text-gray-400" dir="ltr">· {{ $cat['en'] }}</span></h2>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($groups[$key] as $item)
                            @include('library._card', ['item' => $item])
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach

            <div class="rounded-2xl p-6 text-center" style="background:#fdf8ec; border:1px solid #f1e3b8; line-height:2">
                <p class="font-semibold" style="color:#065f46">یہ تمام کتابچے مفت ہیں۔ انہیں اپنے خاندان اور دوستوں کو ضرور بھیجیں۔</p>
                <p class="text-sm text-gray-500">قرآن کا ترجمہ: بیان القرآن (ڈاکٹر اسرار احمدؒ) · سوال ہو تو واٹس ایپ کریں: <a href="https://wa.me/923391114962" dir="ltr" style="color:#065f46">0339 1114962</a></p>
            </div>
        </div>
    </section>
</x-guest-layout>
