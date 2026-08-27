{{-- resources/views/privacy-policy.blade.php --}}
<x-guest-layout :title="__('db.Privacy Policy')" :description="__('db.How Sallaamti collects, uses, and protects your personal information across our Quran learning, Digital Skills, Nikah counseling, family counseling, volunteering, and donation services.')">
    @section('title', __('db.Privacy Policy — Sallaamti'))
    @section('description', __('db.How Sallaamti collects, uses, and protects your personal information.'))
    @section('canonical', url('/privacy-policy'))
    @section('robots', 'index, follow')

    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 220px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="max-w-7xl mx-auto px-4 py-14 relative z-10 text-center w-full">
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">{{ __('db.Privacy Policy') }}</h1>
            <p class="text-white/60 text-sm">{{ __('db.Last updated') }}: {{ \Illuminate\Support\Carbon::parse('2026-08-20')->format('d F Y') }}</p>
            <nav class="flex justify-center gap-2 mt-4 text-sm text-white/50">
                <a href="{{ url('/') }}" class="hover:text-white">{{ __('db.Home') }}</a>
                <span>/</span>
                <span class="text-white">{{ __('db.Privacy Policy') }}</span>
            </nav>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-3xl mx-auto px-4">
            <div class="prose prose-teal max-w-none prose-headings:font-bold prose-a:text-[--teal]">

                <p>
                    {{ __('db.Sallaamti ("we", "us", "our") operates sallaamti.com, an Islamic education platform offering Quran courses and live classes, free Digital Skills training, a Nikah (matrimonial) matching service, family counseling support, a community Wall, and volunteering and donation programs. This Privacy Policy explains what information we collect, how we use it, and the choices you have.') }}
                </p>

                <h2>{{ __('db.1. Information We Collect') }}</h2>
                <ul>
                    <li><strong>{{ __('db.Account information') }}:</strong> {{ __('db.name, email address and/or WhatsApp number, and a securely hashed password.') }}</li>
                    <li><strong>{{ __('db.Profile information') }}:</strong> {{ __('db.gender, city, and profile photo, which you can add after registering. If you publish a Community Post, an optional public username and short bio are shown alongside it.') }}</li>
                    <li><strong>{{ __('db.Nikah profile information') }}:</strong> {{ __('db.family and personal background, religious practice, partner preferences, photos, and identity verification documents (e.g. CNIC) submitted when creating a matrimonial profile.') }}</li>
                    <li><strong>{{ __('db.Course progress') }}:</strong> {{ __('db.your enrollment, lesson progress, and quiz results for Quran and Digital Skills courses, used to issue completion certificates.') }}</li>
                    <li><strong>{{ __('db.Family counseling information') }}:</strong> {{ __('db.the category and description of your concern, preferred contact method, and any details you share with a counselor while booking or attending a session.') }}</li>
                    <li><strong>{{ __('db.Volunteer application information') }}:</strong> {{ __('db.your name, email, phone, city, and area of interest when you apply to volunteer — collected even if you apply without an account.') }}</li>
                    <li><strong>{{ __('db.Donation information') }}:</strong> {{ __('db.the amount, cause, and optional message you submit when donating — collected even if you donate without an account.') }}</li>
                    <li><strong>{{ __('db.Wall and Community Post content') }}:</strong> {{ __('db.dua requests and posts you submit to the Sallaamti Wall, and any comments or reactions you leave.') }}</li>
                    <li><strong>{{ __('db.Payment information') }}:</strong> {{ __('db.for donations, verification fees, or paid courses, we collect the payment method details you submit (e.g. JazzCash/EasyPaisa transaction reference or bank deposit slip). We do not store full card numbers.') }}</li>
                    <li><strong>{{ __('db.Sign-in via Google or Facebook') }}:</strong> {{ __('db.if you choose to sign in with Google or Facebook, we receive your name, email address, and profile photo from that provider, as permitted by your settings with them.') }}</li>
                    <li><strong>{{ __('db.Usage data') }}:</strong> {{ __('db.pages visited, device and browser type, and similar technical data collected automatically via cookies and analytics tools.') }}</li>
                </ul>

                <h2>{{ __('db.2. How We Use Your Information') }}</h2>
                <ul>
                    <li>{{ __('db.To create and manage your account, and verify your identity for the Nikah platform.') }}</li>
                    <li>{{ __('db.To operate Quran courses, live classes, Digital Skills courses, Nikah matching, family counseling bookings, and the Sallaamti Wall.') }}</li>
                    <li>{{ __('db.To send account, verification, and booking-related notifications (by email or, where you provided one, WhatsApp).') }}</li>
                    <li>{{ __('db.To process donations, volunteer applications, and payments you initiate.') }}</li>
                    <li>{{ __('db.To review member-submitted Community Posts and dua requests before they appear publicly.') }}</li>
                    <li>{{ __('db.To improve our services, prevent fraud and abuse, and comply with legal obligations.') }}</li>
                </ul>

                <h2>{{ __('db.3. Third-Party Services') }}</h2>
                <p>{{ __('db.We use a limited number of third-party services to operate Sallaamti:') }}</p>
                <ul>
                    <li><strong>{{ __('db.Google & Facebook Sign-In') }}</strong> — {{ __('db.optional one-tap login. We only receive the profile fields these providers share with your consent.') }}</li>
                    <li><strong>{{ __('db.Analytics (e.g. Google Tag Manager)') }}</strong> — {{ __('db.helps us understand aggregate site usage. This may set cookies in your browser.') }}</li>
                    <li><strong>{{ __('db.Email delivery') }}</strong> — {{ __('db.used to send verification codes, notifications, and password resets.') }}</li>
                </ul>

                <h2>{{ __('db.4. How We Share Information') }}</h2>
                <p>
                    {{ __('db.We do not sell your personal information. We share it only:') }}
                </p>
                <ul>
                    <li>{{ __('db.With service providers who help us operate the platform (e.g. hosting, email delivery), under confidentiality obligations.') }}</li>
                    <li>{{ __('db.Within the Nikah module, limited profile details are shown to other verified members according to your privacy choices — never your CNIC or full contact details without your consent.') }}</li>
                    <li>{{ __('db.With a family counselor, solely to provide the counseling session you booked.') }}</li>
                    <li>{{ __('db.When required by law, or to protect the rights, safety, and security of Sallaamti and our users.') }}</li>
                </ul>

                <h2>{{ __('db.5. Nikah Profile Data — Special Care') }}</h2>
                <p>
                    {{ __('db.Because matrimonial data is sensitive, identity verification documents (such as CNIC images) are used only to verify genuine profiles and are never displayed publicly. You control what appears on your public Nikah profile, and you may block or report any member.') }}
                </p>

                <h2>{{ __('db.6. Data Security') }}</h2>
                <p>
                    {{ __('db.We use industry-standard measures — including encrypted password storage and access controls — to protect your information. No method of transmission or storage is 100% secure, but we work to protect your data appropriately.') }}
                </p>

                <h2>{{ __('db.7. Your Rights & Choices') }}</h2>
                <ul>
                    <li>{{ __('db.You can review and update your profile information at any time from your account.') }}</li>
                    <li>{{ __('db.You can request a copy of your data, or ask us to delete your account and associated data, by contacting us below.') }}</li>
                    <li>{{ __('db.You can unsubscribe from newsletter emails at any time using the link in those emails.') }}</li>
                </ul>

                <h2>{{ __('db.8. Cookies') }}</h2>
                <p>
                    {{ __('db.We use cookies to keep you signed in, remember your language preference, and understand aggregate site usage. You can control cookies through your browser settings.') }}
                </p>

                <h2>{{ __('db.9. Children\'s Privacy') }}</h2>
                <p>
                    {{ __('db.Sallaamti, and particularly its Nikah matrimonial service, is intended for adults. We do not knowingly collect information from children.') }}
                </p>

                <h2>{{ __('db.10. Data Retention') }}</h2>
                <p>
                    {{ __('db.We retain your information for as long as your account is active or as needed to provide our services, comply with legal obligations, and resolve disputes.') }}
                </p>

                <h2>{{ __('db.11. Changes to This Policy') }}</h2>
                <p>
                    {{ __('db.We may update this Privacy Policy from time to time. Material changes will be reflected by updating the date at the top of this page.') }}
                </p>

                <h2>{{ __('db.12. Contact Us') }}</h2>
                <p>
                    {{ __('db.If you have questions about this Privacy Policy or your data, contact us at') }}
                    <a href="mailto:{{ setting('site_email') }}">{{ setting('site_email') }}</a>
                    @if (setting('social_whatsapp') || setting('site_phone'))
                    {{ __('db.or') }} <a href="{{ whatsapp_link() }}">{{ setting('social_whatsapp') ?: setting('site_phone') }}</a>.
                    @endif
                </p>

            </div>
        </div>
    </section>
</x-guest-layout>
