<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600">Dashboard</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Settings</span>
        </div>
    </x-slot>

    <div class="w-full space-y-6">


        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf

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

            {{-- About / Homepage --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-gray-700 border-b pb-2">📖 About / Homepage</h3>
                <p class="text-xs text-gray-400">These appear in the "About" section on the homepage.</p>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <x-input-label value="About Heading" />
                        <x-text-input name="about_heading" class="w-full mt-1" :value="$settings['about_heading'] ?? ''" />
                    </div>
                    <div>
                        <x-input-label value="About Text" />
                        <textarea name="about_text" rows="4" class="border-gray-300 rounded-md w-full mt-1">{{ $settings['about_text'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <x-input-label value="Vision Text" />
                        <textarea name="vision_text" rows="3" class="border-gray-300 rounded-md w-full mt-1">{{ $settings['vision_text'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <x-input-label value="Mission Text" />
                        <textarea name="mission_text" rows="3" class="border-gray-300 rounded-md w-full mt-1">{{ $settings['mission_text'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <x-input-label value="Donate Goal Text" />
                        <x-text-input name="donate_goal_text" class="w-full mt-1" :value="$settings['donate_goal_text'] ?? ''" placeholder="PKR 50K" />
                    </div>
                </div>
            </div>

            {{-- Payment --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-gray-700 border-b pb-2">💳 Payment Details</h3>
                <p class="text-xs text-gray-400">These appear on all payment submission forms across the site.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="JazzCash Account Title" />
                        <x-text-input name="jazzcash_account_title" class="w-full mt-1" :value="$settings['jazzcash_account_title'] ?? ''" placeholder="Your JazzCash Account Title" />
                    </div>
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
                        <x-input-label value="Bank Account IBAN" />
                        <x-text-input name="bank_account_iban" class="w-full mt-1" :value="$settings['bank_account_iban'] ?? ''" />
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
                        <x-input-label value="WhatsApp Number" />
                        <x-text-input name="social_whatsapp" class="w-full mt-1" :value="$settings['social_whatsapp'] ?? ''" placeholder="+92 3XX XXXXXXX" />
                    </div>
                    <div>
                        <x-input-label value="Facebook URL" />
                        <x-text-input name="social_facebook" class="w-full mt-1" :value="$settings['social_facebook'] ?? ''" placeholder="https://facebook.com/..." />
                    </div>
                    <div>
                        <x-input-label value="YouTube URL" />
                        <x-text-input name="social_youtube" class="w-full mt-1" :value="$settings['social_youtube'] ?? ''" placeholder="https://youtube.com/..." />
                    </div>
                    <div>
                        <x-input-label value="Instagram URL" />
                        <x-text-input name="social_instagram" class="w-full mt-1" :value="$settings['social_instagram'] ?? ''" placeholder="https://instagram.com/..." />
                    </div>
                    <div>
                        <x-input-label value="TikTok URL" />
                        <x-text-input name="social_tiktok" class="w-full mt-1" :value="$settings['social_tiktok'] ?? ''" placeholder="https://tiktok.com/..." />
                    </div>
                </div>
            </div>


            {{-- SEO Settings --}}
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h3 class="font-semibold text-gray-700 mb-5 pb-2 border-b border-gray-100">
                    🔍 SEO Settings
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 block mb-1">
                            Homepage Meta Title
                            <span class="text-gray-400 font-normal ml-1">(max 60 chars)</span>
                        </label>
                        <input type="text" name="seo_home_title"
                            value="{{ setting('seo_home_title', 'Sallaamti — Learn Quran Online | Live Classes | Islamic Matrimonial') }}"
                            maxlength="60"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500"
                            oninput="updateCounter(this, 'title_count', 60)">
                        <div class="flex justify-between mt-1">
                            <p class="text-xs text-gray-400">Shown as the blue link in Google search results</p>
                            <span class="text-xs text-gray-400"><span id="title_count">{{ strlen(setting('seo_home_title', '')) }}</span>/60</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 block mb-1">
                            Homepage Meta Description
                            <span class="text-gray-400 font-normal ml-1">(max 160 chars)</span>
                        </label>
                        <textarea name="seo_home_description" rows="3"
                            maxlength="160"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500 resize-none"
                            oninput="updateCounter(this, 'desc_count', 160)">{{ setting('seo_home_description', 'Join Sallaamti to learn Quran online with expert teachers. Free self-paced courses, live classes, halal matrimonial and family support.') }}</textarea>
                        <div class="flex justify-between mt-1">
                            <p class="text-xs text-gray-400">Shown as the description snippet in Google</p>
                            <span class="text-xs text-gray-400"><span id="desc_count">{{ strlen(setting('seo_home_description', '')) }}</span>/160</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 block mb-1">
                            Focus Keywords
                        </label>
                        <input type="text" name="seo_keywords"
                            value="{{ setting('seo_keywords', 'learn quran online, quran classes pakistan, online quran teacher, nikah platform pakistan') }}"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500"
                            placeholder="keyword1, keyword2, keyword3">
                        <p class="text-xs text-gray-400 mt-1">Comma separated — target 5-10 keywords</p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 block mb-1">
                            Default OG Image (for social sharing previews)
                        </label>
                        @if(setting('seo_og_image'))
                        <img src="{{ Storage::url(setting('seo_og_image')) }}"
                            class="h-20 rounded-lg mb-2 object-cover">
                        @endif
                        <input type="file" name="seo_og_image" accept="image/*"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <p class="text-xs text-gray-400 mt-1">Recommended: 1200×630px — shown when sharing on Facebook, WhatsApp, Twitter</p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 block mb-1">
                            Google Search Console Verification Code
                        </label>
                        <input type="text" name="google_verification"
                            value="{{ setting('google_verification') }}"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-teal-500"
                            placeholder="google-site-verification content value">
                    </div>

                </div>
            </div>

            <script>
                function updateCounter(input, counterId, max) {
                    const count = input.value.length;
                    const el = document.getElementById(counterId);
                    el.textContent = count;
                    el.parentElement.style.color = count > max * 0.9 ? '#ef4444' : '#9ca3af';
                }
            </script>
            
            {{-- Integrations --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <h3 class="font-semibold text-gray-700 border-b pb-2">🔌 Integrations</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Google Tag Manager Container ID" />
                        <x-text-input name="gtm_id" class="w-full mt-1" :value="$settings['gtm_id'] ?? ''" placeholder="GTM-XXXXXXX" />
                        <p class="text-xs text-gray-400 mt-1">From tagmanager.google.com. Leave blank to disable GTM. Configure the Meta Pixel and any other tags inside GTM's own interface once installed.</p>
                    </div>
                    <div>
                        <x-input-label value="Meta Domain Verification Code" />
                        <x-text-input name="meta_domain_verification" class="w-full mt-1" :value="$settings['meta_domain_verification'] ?? ''" placeholder="e.g. abc123def456..." />
                        <p class="text-xs text-gray-400 mt-1">From Meta Business Manager → Brand Safety → Domain Verification. Leave blank if not needed.</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end">
                <x-primary-button>Save Settings</x-primary-button>
            </div>
        </form>
    </div>
</x-admin-layout>