<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600">Dashboard</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Settings</span>
        </div>
    </x-slot>

    <div class="max-w-3xl space-y-6">

        @if (session('status'))
        <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">✅ {{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf

            {{-- Add this section to settings form --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-gray-700 border-b pb-2">🕌 About Section</h3>
                <div>
                    <x-input-label value="About Text" />
                    <textarea name="about_text" rows="4" class="border-gray-300 rounded-md w-full mt-1">{{ $settings['about_text'] ?? '' }}</textarea>
                </div>
                <div>
                    <x-input-label value="Our Vision" />
                    <textarea name="vision_text" rows="3" class="border-gray-300 rounded-md w-full mt-1">{{ $settings['vision_text'] ?? '' }}</textarea>
                </div>
                <div>
                    <x-input-label value="Our Mission" />
                    <textarea name="mission_text" rows="3" class="border-gray-300 rounded-md w-full mt-1">{{ $settings['mission_text'] ?? '' }}</textarea>
                </div>
                <div>
                    <x-input-label value="Donation Goal Text (e.g. $10,000)" />
                    <x-text-input name="donate_goal_text" class="w-full mt-1" :value="$settings['donate_goal_text'] ?? ''" />
                </div>
            </div>
            {{-- General --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-gray-700 border-b pb-2">🌐 General</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Site Name" />
                        <x-text-input name="site_name" class="w-full mt-1" :value="$settings['site_name'] ?? ''" required />
                    </div>
                    <div>
                        <x-input-label value="Tagline" />
                        <x-text-input name="site_tagline" class="w-full mt-1" :value="$settings['site_tagline'] ?? ''" />
                    </div>
                    <div>
                        <x-input-label value="Contact Email" />
                        <x-text-input name="site_email" type="email" class="w-full mt-1" :value="$settings['site_email'] ?? ''" />
                    </div>
                    <div>
                        <x-input-label value="Contact Phone" />
                        <x-text-input name="site_phone" class="w-full mt-1" :value="$settings['site_phone'] ?? ''" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label value="Address" />
                        <x-text-input name="site_address" class="w-full mt-1" :value="$settings['site_address'] ?? ''" />
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <input type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1"
                        {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}
                        class="rounded border-gray-300 text-red-600">
                    <label for="maintenance_mode" class="text-sm text-gray-700">
                        <span class="font-medium text-red-600">Maintenance Mode</span>
                        — guests see a "coming soon" page instead of the site
                    </label>
                </div>
            </div>

            {{-- Payment --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-gray-700 border-b pb-2">💳 Payment Details</h3>
                <p class="text-xs text-gray-400">These appear on all payment submission forms across the site.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="JazzCash Number" />
                        <x-text-input name="jazzcash_number" class="w-full mt-1" :value="$settings['jazzcash_number'] ?? ''" placeholder="03XX-XXXXXXX" />
                    </div>
                    <div>
                        <x-input-label value="EasyPaisa Number" />
                        <x-text-input name="easypaisa_number" class="w-full mt-1" :value="$settings['easypaisa_number'] ?? ''" placeholder="03XX-XXXXXXX" />
                    </div>
                    <div>
                        <x-input-label value="Bank Account Title" />
                        <x-text-input name="bank_account_title" class="w-full mt-1" :value="$settings['bank_account_title'] ?? ''" />
                    </div>
                    <div>
                        <x-input-label value="Bank Account Number" />
                        <x-text-input name="bank_account_number" class="w-full mt-1" :value="$settings['bank_account_number'] ?? ''" />
                    </div>
                    <div>
                        <x-input-label value="Bank Name" />
                        <x-text-input name="bank_name" class="w-full mt-1" :value="$settings['bank_name'] ?? ''" />
                    </div>
                    <div>
                        <x-input-label value="Nikah Verification Fee (Rs.)" />
                        <x-text-input name="nikah_verification_fee" type="number" class="w-full mt-1" :value="$settings['nikah_verification_fee'] ?? '500'" />
                    </div>
                </div>
            </div>

            {{-- Social --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-gray-700 border-b pb-2">📱 Social Media</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Facebook URL" />
                        <x-text-input name="social_facebook" class="w-full mt-1" :value="$settings['social_facebook'] ?? ''" placeholder="https://facebook.com/..." />
                    </div>
                    <div>
                        <x-input-label value="YouTube URL" />
                        <x-text-input name="social_youtube" class="w-full mt-1" :value="$settings['social_youtube'] ?? ''" placeholder="https://youtube.com/..." />
                    </div>
                    <div>
                        <x-input-label value="WhatsApp Number" />
                        <x-text-input name="social_whatsapp" class="w-full mt-1" :value="$settings['social_whatsapp'] ?? ''" placeholder="+92 3XX XXXXXXX" />
                    </div>
                    <div>
                        <x-input-label value="Instagram URL" />
                        <x-text-input name="social_instagram" class="w-full mt-1" :value="$settings['social_instagram'] ?? ''" placeholder="https://instagram.com/..." />
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <x-primary-button>Save Settings</x-primary-button>
            </div>
        </form>
    </div>
</x-admin-layout>