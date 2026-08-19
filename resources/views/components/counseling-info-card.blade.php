{{--
    A colorful "about this module" card for the Family Counseling sidebar
    column — pairs with <x-module-nav module="counseling" /> so the sidebar
    isn't just a short list of links with empty space beneath it.
--}}
<div class="rounded-2xl border-2 p-5 space-y-4" style="border-color: var(--teal); background: linear-gradient(150deg, var(--teal-light) 0%, #fdfaf3 100%)">
    <div class="flex items-center gap-2">
        <span class="text-2xl">🤝</span>
        <h3 class="font-bold text-gray-800">{{ __('db.Family Counseling') }}</h3>
    </div>
    <p class="text-sm text-gray-600 leading-relaxed">
        {{ __('db.Confidential guidance from qualified counselors on marital, parenting, financial, legal, and spiritual matters — rooted in Islamic values.') }}
    </p>

    <div class="space-y-2.5">
        <div class="flex items-start gap-2.5 text-sm text-gray-700">
            <span class="mt-0.5">🔒</span>
            <span>{{ __("db.You can stay anonymous — your name is never shown to the counselor if you choose.") }}</span>
        </div>
        <div class="flex items-start gap-2.5 text-sm text-gray-700">
            <span class="mt-0.5">⏱️</span>
            <span>{{ __('db.Counselors typically respond within 24–48 hours, in sha Allah.') }}</span>
        </div>
        <div class="flex items-start gap-2.5 text-sm text-gray-700">
            <span class="mt-0.5">💚</span>
            <span>{{ __('db.This service is completely free of charge.') }}</span>
        </div>
        <div class="flex items-start gap-2.5 text-sm text-gray-700">
            <span class="mt-0.5">📅</span>
            <span>{{ __("db.Prefer to talk live? Book a session and pick a counselor and time that works for you.") }}</span>
        </div>
    </div>

    <div class="rounded-xl p-3 flex items-start gap-2" style="background: #fef2f2; border: 1px solid #fecaca">
        <span class="text-base">🚨</span>
        <p class="text-xs text-red-700 leading-relaxed">
            {{ __('db.This is not a crisis or emergency service. If you or someone else is in immediate danger, please contact local emergency services right away.') }}
        </p>
    </div>
</div>
