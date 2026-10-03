{{-- Installable-app prompt — iOS only now. This used to also prompt Android
     visitors to install the site as a PWA (via Chrome's beforeinstallprompt
     event), but now that the real Sallaamti app is published on Play Store,
     that competed with and diluted the native-app download path
     (components.mobile-app-promo) — an Android visitor should be pushed
     toward the real app, not a home-screen shortcut to the website. We still
     listen for beforeinstallprompt purely to call preventDefault() on it, so
     Chrome's own automatic mini-infobar doesn't pop up either.
     iOS Safari never fires that event at all — there's no App Store app yet
     to point iPhone visitors at instead, so PWA install stays the best
     option there, with static "how to" instructions since iOS has no
     programmatic install API. Dismissal is remembered so this doesn't nag on
     every visit. --}}
<div
    x-data="{
        show: false,
        init() {
            if (localStorage.getItem('pwaInstallDismissed') === '1') return;
            if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) return;

            window.addEventListener('beforeinstallprompt', (e) => e.preventDefault());

            const isIOS = /iphone|ipad|ipod/i.test(window.navigator.userAgent) && !window.MSStream;
            this.show = isIOS;
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
            <p class="text-xs text-gray-500 mt-0.5">{{ __('db.Tap the Share button, then "Add to Home Screen".') }}</p>
            <div class="mt-2 flex gap-2">
                <button @click="dismiss()" class="text-xs font-medium text-gray-400 px-3 py-1.5 rounded-lg hover:bg-gray-50">
                    {{ __('db.Not now') }}
                </button>
            </div>
        </div>
        <button @click="dismiss()" class="text-gray-300 hover:text-gray-500 flex-shrink-0" aria-label="{{ __('db.Dismiss') }}">✕</button>
    </div>
</div>
