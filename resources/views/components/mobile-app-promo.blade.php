{{-- Native-app download nudge for mobile browser visitors — separate from
     the PWA install banner (that installs this same web app to the home
     screen); this points Android/iPhone visitors at the real Play Store /
     App Store listing instead. Admin-controlled from Settings > General.
     A top slim dismissible banner, not a full-screen interstitial — Google
     penalizes mobile search rankings for app-install interstitials that
     cover the page content, so this deliberately never blocks the page. --}}
@if (setting('mobile_app_promo_enabled') === '1' && (setting('mobile_app_android_url') || setting('mobile_app_ios_url')))
<div
    x-data="{
        show: false,
        platform: null,
        androidUrl: @js(setting('mobile_app_android_url', '')),
        iosUrl: @js(setting('mobile_app_ios_url', '')),
        init() {
            if (localStorage.getItem('mobileAppPromoDismissed') === '1') return;
            const ua = window.navigator.userAgent;
            if (/android/i.test(ua) && this.androidUrl) {
                this.platform = 'android';
            } else if (/iphone|ipad|ipod/i.test(ua) && !window.MSStream && this.iosUrl) {
                this.platform = 'ios';
            }
            this.show = this.platform !== null;
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
    class="fixed top-0 inset-x-0 z-50"
>
    <div class="flex items-center gap-3 px-3 sm:px-4 py-2.5 shadow-md" style="background: var(--teal)">
        <span class="text-2xl flex-shrink-0">📲</span>
        <div class="flex-1 min-w-0">
            <p class="text-white text-sm font-semibold leading-tight">{{ __('db.Get the Sallaamti App') }}</p>
            <p class="text-white/80 text-xs leading-tight hidden sm:block">{{ __('db.A faster, easier way to use Sallaamti on your phone.') }}</p>
        </div>
        <button @click="dismiss()" class="text-xs font-medium text-white/80 px-2 py-1.5 hover:text-white whitespace-nowrap flex-shrink-0">
            {{ __('db.Continue on Website') }}
        </button>
        <a :href="storeUrl()" target="_blank" rel="noopener" @click="dismiss()"
           class="text-xs font-semibold px-3 py-1.5 rounded-lg whitespace-nowrap flex-shrink-0" style="background: var(--gold); color: #1a1a2e">
            {{ __('db.Download App') }}
        </a>
    </div>
</div>
@endif
