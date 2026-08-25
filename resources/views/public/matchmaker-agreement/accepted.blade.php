<x-guest-layout title="Agreement Accepted — Sallaamti" description="Your Nikah Counselor Agreement has been accepted.">

    <div class="py-16 bg-cream">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm p-8 text-center">
                <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center text-3xl mb-4 bg-green-50">✅</div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Agreement Accepted</h3>
                <p class="text-sm text-gray-500 mb-2" dir="rtl">معاہدہ قبول کر لیا گیا</p>
                <p class="text-sm text-gray-500 mb-1">Thank you, {{ $application->full_name }}. Your acceptance has been recorded and shared with the Sallaamti team.</p>
                <p class="text-sm text-gray-500 mb-2" dir="rtl">شکریہ، {{ $application->full_name }}۔ آپ کی قبولیت محفوظ کر لی گئی ہے اور سلامتی ٹیم کو بھیج دی گئی ہے۔</p>
                <p class="text-xs text-gray-400">Accepted {{ $application->agreement_accepted_at?->format('d M Y, h:i A') }}</p>
                <p class="text-sm text-gray-500 mt-4">You'll hear from your Sallaamti contact about the next step in your onboarding.</p>
                <p class="text-sm text-gray-500 mt-1" dir="rtl">اگلے مرحلے کے بارے میں آپ کو سلامتی کی طرف سے جلد رابطہ کیا جائے گا۔</p>
            </div>
        </div>
    </div>
</x-guest-layout>
