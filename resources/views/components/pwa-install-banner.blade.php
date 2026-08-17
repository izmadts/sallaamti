{{-- Installable-app prompt. Chrome/Android fires beforeinstallprompt and we
     capture it to show our own styled button (the browser's automatic mini-
     banner is inconsistent and easy to miss). iOS Safari never fires that
     event at all — there's no programmatic install API there — so it gets
     static "how to" instructions instead. Dismissal is remembered so this
     doesn't nag on every visit. --}}
<div
    x-data="{
        show: false,
        isIOS: false,
        deferredPrompt: null,
        init() {
            if (localStorage.getItem('pwaInstallDismissed') === '1') return;
            if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) return;

            this.isIOS = /iphone|ipad|ipod/i.test(window.navigator.userAgent) && !window.MSStream;

            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                this.deferredPrompt = e;
                this.show = true;
            });

            if (this.isIOS) {
                this.show = true;
            }
        },
        async install() {
            if (!this.deferredPrompt) return;
            this.deferredPrompt.prompt();
            await this.deferredPrompt.userChoice;
            this.deferredPrompt = null;
            this.show = false;
        },
        dismiss() {
            this.show = false;
            localStorage.setItem('pwaInstallDismissed', '1');
        },
    }"
    x-show="show"
    x-cloak
    x-transition
    class="fixed bottom-16 sm:bottom-4 inset-x-3 sm:inset-x-auto sm:right-4 sm:max-w-sm z-40"
>
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-4 flex items-start gap-3">
        <div class="text-3xl flex-shrink-0">📲</div>
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-gray-800 text-sm">{{ __('db.Install Sallaamti') }}</p>
            <template x-if="!isIOS">
                <p class="text-xs text-gray-500 mt-0.5">{{ __('db.Add it to your home screen — never miss a match or a class reminder.') }}</p>
            </template>
            <template x-if="isIOS">
                <p class="text-xs text-gray-500 mt-0.5">{{ __('db.Tap the Share button, then "Add to Home Screen".') }}</p>
            </template>
            <div class="mt-2 flex gap-2">
                <template x-if="!isIOS">
                    <button @click="install()" class="text-xs font-semibold text-white px-3 py-1.5 rounded-lg" style="background: var(--teal)">
                        {{ __('db.Install') }}
                    </button>
                </template>
                <button @click="dismiss()" class="text-xs font-medium text-gray-400 px-3 py-1.5 rounded-lg hover:bg-gray-50">
                    {{ __('db.Not now') }}
                </button>
            </div>
        </div>
        <button @click="dismiss()" class="text-gray-300 hover:text-gray-500 flex-shrink-0" aria-label="{{ __('db.Dismiss') }}">✕</button>
    </div>
</div>
