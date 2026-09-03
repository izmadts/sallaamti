{{-- resources/views/delete-account.blade.php --}}
<x-guest-layout :title="__('db.Delete Your Account')" :description="__('db.How to delete your Sallaamti account and what happens to your data.')">
    @section('title', __('db.Delete Your Account — Sallaamti'))
    @section('description', __('db.Steps to delete your Sallaamti account, and what happens to your data.'))
    @section('canonical', url('/delete-account'))
    @section('robots', 'index, follow')

    <section class="page-hero relative overflow-hidden flex items-center" style="min-height: 220px; background: linear-gradient(135deg, #0d6b6b 0%, #1a1a2e 100%);">
        <div class="max-w-7xl mx-auto px-4 py-14 relative z-10 text-center w-full">
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">{{ __('db.Delete Your Sallaamti Account') }}</h1>
            <nav class="flex justify-center gap-2 mt-4 text-sm text-white/50">
                <a href="{{ url('/') }}" class="hover:text-white">{{ __('db.Home') }}</a>
                <span>/</span>
                <span class="text-white">{{ __('db.Delete Account') }}</span>
            </nav>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-3xl mx-auto px-4">
            <div class="prose prose-teal max-w-none prose-headings:font-bold prose-a:text-[--teal]">

                <p>
                    {{ __('db.You can permanently delete your Sallaamti account and all its associated data — on the website or in either of our mobile apps (Sallaamti and Sallaamti Nikah Counselor) — by following the steps below.') }}
                </p>

                <h2>{{ __('db.How to Request Deletion') }}</h2>
                <ol>
                    <li>{{ __('db.Log in to your Sallaamti account, on the website or in the app.') }}</li>
                    <li>{{ __('db.Go to your Profile / Account Settings page.') }}</li>
                    <li>{{ __('db.Scroll to the "Delete Account" section and tap/click the "Delete Account" button.') }}</li>
                    <li>{{ __('db.Enter your password to confirm.') }}</li>
                </ol>
                <p>
                    {{ __("db.Can't log in? Email us at") }}
                    <a href="mailto:{{ setting('site_email') }}">{{ setting('site_email') }}</a>
                    {{ __('db.from the address on your account and ask us to delete it for you.') }}
                </p>

                <h2>{{ __('db.What Happens to Your Data') }}</h2>
                <ul>
                    <li>{{ __('db.Your account is deactivated immediately and you are logged out of all devices.') }}</li>
                    <li>{{ __('db.For 30 days, your data is kept safe — simply log back in during that window to reactivate your account exactly as it was.') }}</li>
                    <li>{{ __('db.If you do not log back in within 30 days, your account is permanently and irreversibly deleted, along with everything tied to it: your profile, Nikah matrimonial profile and photos, course enrollments and certificates, posts, comments, messages, and uploaded files.') }}</li>
                    <li>{{ __('db.A small number of records are kept even after deletion where required for legitimate business or legal reasons — for example, donation and payment records are retained in anonymized form, and other members\' messages to you are not deleted from their own side of a conversation.') }}</li>
                </ul>

            </div>
        </div>
    </section>
</x-guest-layout>
