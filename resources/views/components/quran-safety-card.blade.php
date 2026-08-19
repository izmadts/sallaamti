{{--
    A short trust/safety card for Quran Live Classes — most students here are
    children, so this is shown wherever a parent is deciding whether to hand
    over a child's details and trust a teacher.
--}}
<div class="rounded-2xl border-2 p-5 space-y-3" style="border-color: var(--teal); background: linear-gradient(150deg, var(--teal-light) 0%, #fdfaf3 100%)">
    <div class="flex items-center gap-2">
        <span class="text-2xl">🛡️</span>
        <h3 class="font-bold text-gray-800">{{ __('db.Your Child\'s Safety') }}</h3>
    </div>
    <div class="space-y-2">
        <div class="flex items-start gap-2.5 text-sm text-gray-700">
            <span class="mt-0.5">✅</span>
            <span>{{ __('db.Every teacher is reviewed and approved by our admin team before they can be assigned a class.') }}</span>
        </div>
        <div class="flex items-start gap-2.5 text-sm text-gray-700">
            <span class="mt-0.5">💬</span>
            <span>{{ __('db.Message your teacher directly through Sallaamti — you never need to share personal contact details.') }}</span>
        </div>
        <div class="flex items-start gap-2.5 text-sm text-gray-700">
            <span class="mt-0.5">🚩</span>
            <span>{{ __('db.Concerned about something? Contact us any time and we\'ll look into it.') }} <a href="/contact" class="underline font-medium">{{ __('db.Report a concern') }}</a></span>
        </div>
    </div>
</div>
