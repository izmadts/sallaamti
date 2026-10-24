{{-- Native-app download nudge for mobile browser visitors — separate from
     the PWA install banner (that installs this same web app to the home
     screen); this points Android/iPhone visitors at the real Play Store /
     App Store listing instead. Admin-controlled from Settings > General.

     A full-screen takeover, not a small centered card — maximum visibility
     for mobile browser visitors (including ones arriving from a Facebook/
     Instagram/TikTok in-app browser via an ad), by request. Two things
     keep this from tripping Google's mobile-interstitial ranking penalty
     (which targets popups that block page content the moment a search
     visitor lands): it only appears after a 2-second delay, so the page's
     real content is always visible first, and it's trivially dismissible
     (✕ or "Continue on Website" close it — true, no-escape forcing was
     deliberately ruled out: it risks that same SEO penalty, breaks inside
     some in-app browsers that restrict app-store redirects, and would
     bounce ad traffic that just wants to browse first). --}}
{{-- Skipped on rishta.next (/rishta/agla-qadam) by request — that page's
     whole job is to calmly offer "WhatsApp or get the app" after a
     Facebook/Instagram lead form; a full-screen takeover forcing the same
     choice on top of it is redundant and undercuts the page's own CTAs. --}}
@if (setting('mobile_app_promo_enabled') === '1' && !request()->routeIs('rishta.next'))
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
    class="fixed inset-0 z-[9999] flex flex-col"
    style="background: linear-gradient(160deg, var(--teal, #0d6b6b) 0%, #06302f 100%)"
>
    <button @click="dismiss()" class="absolute top-4 right-4 text-white/70 hover:text-white text-2xl leading-none w-10 h-10 flex items-center justify-center" aria-label="{{ __('db.Dismiss') }}">✕</button>

    <div class="flex-1 flex flex-col items-center justify-center text-center px-6 py-10">
        <div class="w-24 h-24 rounded-3xl bg-white shadow-xl flex items-center justify-center text-5xl mb-6">📲</div>
        <p class="text-white text-2xl font-extrabold leading-tight max-w-xs">{{ __('db.Get the Sallaamti App') }}</p>
        <p class="text-white/80 text-base mt-3 leading-snug max-w-xs">{{ __('db.A faster, easier, more private way to find your match — built for your phone.') }}</p>

        <ul class="text-left text-white/90 text-sm mt-6 space-y-2 max-w-xs">
            <li class="flex items-start gap-2"><span>✅</span> {{ __('db.Verified, serious profiles only') }}</li>
            <li class="flex items-start gap-2"><span>✅</span> {{ __('db.Guardian-mediated, Islamic process') }}</li>
            <li class="flex items-start gap-2"><span>✅</span> {{ __('db.Faster, smoother than the website') }}</li>
        </ul>
    </div>

    <div class="px-6 pb-10 pt-4 flex flex-col gap-3">
        <a :href="storeUrl()" target="_blank" rel="noopener" @click="dismiss()"
           class="text-center text-base font-bold px-4 py-4 rounded-xl shadow-lg" style="background: var(--gold); color: #1a1a2e">
            {{ __('db.Download the App') }}
        </a>
        <button @click="dismiss()" class="text-sm font-medium text-white/70 px-4 py-2 hover:text-white">
            {{ __('db.Continue on Website') }}
        </button>
    </div>
</div>
@endif
