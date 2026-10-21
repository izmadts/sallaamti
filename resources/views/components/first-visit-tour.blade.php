{{--
    Generic "show once, remember that, let it be replayed" tour — a 👆 hand
    + emoji + short tip per step, same mechanism as the mobile app's
    FirstVisitTour widget (lib/shared/widgets/first_visit_tour.dart).
    Per-browser (localStorage), not per-account — a fresh browser/device
    sees it again, which is fine; this is a UI nudge, not account state.

    $steps: array of ['emoji' => ..., 'title' => ..., 'description' => ...]
--}}
@props(['storageKey', 'steps'])

<div x-data="{
        open: false,
        index: 0,
        steps: @js($steps),
        storageKey: @js('nikah_tour_' . $storageKey),
        init() {
            if (!localStorage.getItem(this.storageKey)) {
                this.open = true;
            }
        },
        next() {
            if (this.index < this.steps.length - 1) {
                this.index++;
            } else {
                this.close();
            }
        },
        close() {
            this.open = false;
            localStorage.setItem(this.storageKey, '1');
        },
        replay() {
            this.index = 0;
            this.open = true;
        },
    }" x-cloak>
    <div class="flex justify-end mb-2">
        <button type="button" @click="replay()" class="text-sm text-gray-500 hover:text-teal-700">❓ {{ __('db.How does this work?') }}</button>
    </div>

    <div x-show="open" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 text-center">
            <button type="button" @click="close()" class="block ml-auto text-sm text-gray-400 hover:text-gray-600 mb-1">{{ __('db.Skip') }}</button>

            <div class="text-4xl mb-1" style="animation: bounce 0.7s infinite alternate;">👆</div>

            <template x-for="(step, i) in steps" :key="i">
                <div x-show="index === i">
                    <div class="text-5xl mb-4" x-text="step.emoji"></div>
                    <h3 class="text-lg font-extrabold text-gray-800 mb-2" x-text="step.title"></h3>
                    <p class="text-sm text-gray-600 mb-5 leading-relaxed" x-text="step.description"></p>
                </div>
            </template>

            <div class="flex justify-center gap-1 mb-5">
                <template x-for="(step, i) in steps" :key="i">
                    <div class="h-1.5 rounded-full transition-all" :class="index === i ? 'w-5' : 'w-1.5 bg-gray-300'" :style="index === i ? 'background: var(--teal)' : ''"></div>
                </template>
            </div>

            <button type="button" @click="next()" class="w-full text-white font-semibold px-4 py-2.5 rounded-lg hover:opacity-90" style="background: var(--teal)">
                <span x-text="index === steps.length - 1 ? '{{ __('db.Got it!') }}' : '{{ __('db.Next') }}'"></span>
            </button>
        </div>
    </div>

    <style>
        @keyframes bounce { from { transform: translateY(0); } to { transform: translateY(-6px); } }
    </style>
</div>
