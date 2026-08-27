{{-- resources/views/terms-of-service.blade.php --}}
<x-guest-layout :title="__('db.Terms of Service')" :description="__('db.The terms and conditions for using Sallaamti\'s Quran courses, Digital Skills training, Nikah counseling, and family counseling services.')">
    @section('title', __('db.Terms of Service — Sallaamti'))
    @section('description', __('db.The terms and conditions for using Sallaamti.'))
    @section('canonical', url('/terms-of-service'))
    @section('robots', 'index, follow')

    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 220px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="max-w-7xl mx-auto px-4 py-14 relative z-10 text-center w-full">
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">{{ __('db.Terms of Service') }}</h1>
            <p class="text-white/60 text-sm">{{ __('db.Last updated') }}: {{ \Illuminate\Support\Carbon::parse('2026-08-20')->format('d F Y') }}</p>
            <nav class="flex justify-center gap-2 mt-4 text-sm text-white/50">
                <a href="{{ url('/') }}" class="hover:text-white">{{ __('db.Home') }}</a>
                <span>/</span>
                <span class="text-white">{{ __('db.Terms of Service') }}</span>
            </nav>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-3xl mx-auto px-4">
            <div class="prose prose-teal max-w-none prose-headings:font-bold prose-a:text-[--teal]">

                <p>
                    {{ __('db.Welcome to Sallaamti. By creating an account or using any part of sallaamti.com — including Quran courses, live classes, Digital Skills courses, the Nikah counseling platform, family counseling, the Sallaamti Wall, or volunteering and donations — you agree to these Terms of Service. Please read them carefully.') }}
                </p>

                <h2>{{ __('db.1. Eligibility') }}</h2>
                <p>
                    {{ __('db.You must be at least 18 years old to create an account, and specifically to create or browse Nikah (matrimonial) profiles. By registering, you confirm the information you provide is accurate and that you meet this age requirement.') }}
                </p>

                <h2>{{ __('db.2. Your Account') }}</h2>
                <ul>
                    <li>{{ __('db.You are responsible for keeping your login credentials confidential and for all activity under your account.') }}</li>
                    <li>{{ __('db.You may sign in using an email address, a WhatsApp number with a verification code, or a Google/Facebook account.') }}</li>
                    <li>{{ __('db.Notify us immediately if you suspect unauthorized use of your account.') }}</li>
                </ul>

                <h2>{{ __('db.3. Acceptable Use') }}</h2>
                <p>{{ __('db.You agree not to:') }}</p>
                <ul>
                    <li>{{ __('db.Provide false information, including on a Nikah profile or during identity verification.') }}</li>
                    <li>{{ __('db.Harass, threaten, or send inappropriate content to another member.') }}</li>
                    <li>{{ __('db.Use the platform for any commercial, fraudulent, or unlawful purpose.') }}</li>
                    <li>{{ __('db.Attempt to bypass verification, security, or access controls.') }}</li>
                </ul>
                <p>{{ __('db.We may suspend or terminate accounts that violate these terms, including reported Nikah profiles found to be fraudulent.') }}</p>

                <h2>{{ __('db.4. Nikah (Matrimonial) Service') }}</h2>
                <ul>
                    <li>{{ __('db.Sallaamti provides a platform to help members find a compatible spouse in accordance with Islamic principles. We do not guarantee a match, and we are not a party to any relationship or marriage that results.') }}</li>
                    <li>{{ __('db.Profiles go through a verification process, which may include a one-time verification fee and identity documents. Verification fees are non-refundable once verification review has begun.') }}</li>
                    <li>{{ __('db.You are responsible for exercising your own judgment, and involving a guardian (wali) as appropriate, before proceeding with any match.') }}</li>
                    <li>{{ __('db.You can block or report any profile; reported profiles are reviewed by our team.') }}</li>
                </ul>

                <h2>{{ __('db.5. Family Counseling Service') }}</h2>
                <ul>
                    <li>{{ __('db.Our family counseling service is intended for general guidance and support and is not a substitute for professional medical, legal, or psychiatric care. In a crisis or emergency, please contact your local emergency services.') }}</li>
                    <li>{{ __('db.Booked sessions may be rescheduled or cancelled subject to the notice period shown at the time of booking.') }}</li>
                    <li>{{ __('db.Information you share with a counselor is kept confidential and used only to provide your session, as described in our Privacy Policy.') }}</li>
                </ul>

                <h2>{{ __('db.6. Quran Courses, Digital Skills & Live Classes') }}</h2>
                <p>
                    {{ __('db.Course enrollment, live class access, and certificates — for both Quran courses and Digital Skills courses — are provided subject to your continued account standing. Certificates are issued upon meeting the course\'s stated completion requirements. Digital Skills course content is presented in partnership with IZMA Digital Technology & Security.') }}
                </p>

                <h2>{{ __('db.6a. Sallaamti Wall & Community Posts') }}</h2>
                <ul>
                    <li>{{ __('db.Dua requests and Community Posts you submit are reviewed by our team before appearing publicly, and may be edited or removed if they violate these terms.') }}</li>
                    <li>{{ __('db.Publishing a Community Post is a public action — your name, an optional public username and bio, and your post content are visible to anyone with the link, including outside Sallaamti if shared.') }}</li>
                </ul>

                <h2>{{ __('db.6b. Volunteering') }}</h2>
                <p>
                    {{ __('db.Volunteer applications are reviewed by our team; approval is at Sallaamti\'s discretion and is not guaranteed. An approved volunteer\'s ID card is issued electronically and remains valid only while your volunteer status is active.') }}
                </p>

                <h2>{{ __('db.7. Donations & Payments') }}</h2>
                <ul>
                    <li>{{ __('db.Donations are voluntary and non-refundable, and are used to support Sallaamti\'s programs.') }}</li>
                    <li>{{ __('db.Payment references you submit (JazzCash, EasyPaisa, or bank transfer) are verified manually; please allow time for confirmation.') }}</li>
                </ul>

                <h2>{{ __('db.8. Content You Submit') }}</h2>
                <p>
                    {{ __('db.You retain ownership of content you submit (such as your Nikah profile details, testimonials, dua requests, or Community Posts), and grant Sallaamti a license to display it on the platform for the purpose of providing our services. You are responsible for ensuring you have the right to share anything you submit.') }}
                </p>

                <h2>{{ __('db.9. Disclaimers') }}</h2>
                <p>
                    {{ __('db.Sallaamti is provided "as is". While we work to keep the platform accurate, secure, and available, we do not guarantee uninterrupted or error-free service, and we are not liable for the conduct of other members or the outcome of any match, counseling session, or course.') }}
                </p>

                <h2>{{ __('db.10. Termination') }}</h2>
                <p>
                    {{ __('db.You may stop using Sallaamti and request account deletion at any time. We may suspend or terminate accounts that violate these terms.') }}
                </p>

                <h2>{{ __('db.11. Governing Law') }}</h2>
                <p>
                    {{ __('db.These terms are governed by the laws of Pakistan, without regard to conflict of law principles.') }}
                </p>

                <h2>{{ __('db.12. Changes to These Terms') }}</h2>
                <p>
                    {{ __('db.We may update these Terms of Service from time to time. Continued use of Sallaamti after a change means you accept the updated terms.') }}
                </p>

                <h2>{{ __('db.13. Contact Us') }}</h2>
                <p>
                    {{ __('db.Questions about these terms? Contact us at') }}
                    <a href="mailto:{{ setting('site_email') }}">{{ setting('site_email') }}</a>.
                </p>

            </div>
        </div>
    </section>
</x-guest-layout>
