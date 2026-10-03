{{-- Native-app download nudge for mobile browser visitors — separate from
     the PWA install banner (that installs this same web app to the home
     screen); this points Android/iPhone visitors at the real Play Store /
     App Store listing instead. Admin-controlled from Settings > General.

     A centered, dimmed-backdrop popup rather than a plain top banner — by
     request, for more visibility than the original slim bar. Two things
     keep this from tripping Google's mobile-interstitial ranking penalty
     (which targets popups that block page content the moment a search
     visitor lands): it only appears after a 2-second delay, so the page's
     real content is always visible first, and it's trivially dismissible
     (✕, "Continue on Website", or tapping the backdrop all close it). --}}
@if (setting('mobile_app_promo_enabled') === '1')
<div
    x-data="{
        show: false,
        platform: null,
        {{-- Real published Play Store link as the built-in fallback, so this
             works the moment the toggle above is switched on — Settings'
             Android URL field only needs filling in if that link ever
             changes. iOS has no fallback since that app isn't published yet. --}}
        androidUrl: @js(setting('mobile_app_android_url', 'https://play.google.com/store/apps/details?id=com.sallaamti.app&pcampaignid=web_share')),
        iosUrl: @js(setting('mobile_app_ios_url', '')),
        init() {
            if (localStorage.getItem('mobileAppPromoDismissed') === '1') return;
            const ua = window.navigator.userAgent;
            if (/android/i.test(ua) && this.androidUrl) {
                this.platform = 'android';
            } else if (/iphone|ipad|ipod/i.test(ua) && !window.MSStream && this.iosUrl) {
                this.platform = 'ios';
            }
            if (this.platform === null) return;
            setTimeout(() => { this.show = true; }, 2000);
        },
        storeUrl() {
            return this.platform === 'android' ? this.androidUrl : this.iosUrl;
        },
        dismiss() {
            this.show = false;
            localStorage.setItem('mobileAppPromoDismissed', '1');
        },
    }"
    x-show="show"
    x-cloak
    x-transition
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    {{-- Backdrop — tapping it dismisses too, same as the ✕ and "Continue" button --}}
    <div class="absolute inset-0 bg-black/50" @click="dismiss()"></div>

    <div class="relative bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 text-center">
        <button @click="dismiss()" class="absolute top-3 right-3 text-gray-300 hover:text-gray-500" aria-label="{{ __('db.Dismiss') }}">✕</button>

        <div class="text-5xl mb-3">📲</div>
        <p class="text-gray-900 text-lg font-bold leading-tight">{{ __('db.Get the Sallaamti App') }}</p>
        <p class="text-gray-500 text-sm mt-2 leading-snug">{{ __('db.A faster, easier way to find your match and use Sallaamti on your phone.') }}</p>

        <div class="mt-5 flex flex-col gap-2">
            <a :href="storeUrl()" target="_blank" rel="noopener" @click="dismiss()"
               class="text-sm font-semibold px-4 py-2.5 rounded-lg" style="background: var(--gold); color: #1a1a2e">
                {{ __('db.Download App') }}
            </a>
            <button @click="dismiss()" class="text-sm font-medium text-gray-400 px-4 py-2 hover:text-gray-600">
                {{ __('db.Continue on Website') }}
            </button>
        </div>
    </div>
</div>
@endif
