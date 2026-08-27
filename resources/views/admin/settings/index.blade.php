<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600">Dashboard</a>
            <span class="text-gray-300">›</span>
            <span class="text-gray-700 font-semibold">Settings</span>
        </div>
    </x-slot>

    <div class="w-full space-y-6">

        @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
            <p class="font-medium text-sm mb-1">Please fix the following before saving:</p>
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- General --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4 border-l-4" style="border-left-color: #0D6B6B">
                <h3 class="font-semibold pb-2 border-b flex items-center gap-2" style="color: #0D6B6B">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-base" style="background: #0D6B6B22">🌐</span> General
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Site Name" />
                        <x-text-input name="site_name" class="w-full mt-1" :value="($settings['site_name'] ?? '') ?: 'Sallaamti'" required />
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
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4 border-l-4" style="border-left-color: #B8962E">
                <h3 class="font-semibold pb-2 border-b flex items-center gap-2" style="color: #92730f">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-base" style="background: #B8962E22">📖</span> About / Homepage
                </h3>
                <p class="text-xs text-gray-400">These appear in the "About" section on the homepage.</p>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <x-input-label value="About Heading" />
                        <x-text-input name="about_heading" class="w-full mt-1" :value="$settings['about_heading'] ?? ''" />
                    </div>
                    <div>
                        <x-input-label value="About Text" />
                        <input id="trix-about_text" type="hidden" name="about_text" value="{{ $settings['about_text'] ?? '' }}">
                        <trix-editor input="trix-about_text"></trix-editor>
                    </div>
                    <div>
                        <x-input-label value="Vision Text" />
                        <input id="trix-vision_text" type="hidden" name="vision_text" value="{{ $settings['vision_text'] ?? '' }}">
                        <trix-editor input="trix-vision_text"></trix-editor>
                    </div>
                    <div>
                        <x-input-label value="Mission Text" />
                        <input id="trix-mission_text" type="hidden" name="mission_text" value="{{ $settings['mission_text'] ?? '' }}">
                        <trix-editor input="trix-mission_text"></trix-editor>
                    </div>
                    <div>
                        <x-input-label value="Donate Goal Text" />
                        <x-text-input name="donate_goal_text" class="w-full mt-1" :value="$settings['donate_goal_text'] ?? ''" placeholder="PKR 50K" />
                    </div>
                </div>
            </div>

            {{-- Payment --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4 border-l-4" style="border-left-color: #16a34a">
                <h3 class="font-semibold pb-2 border-b flex items-center gap-2" style="color: #15803d">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-base" style="background: #16a34a22">💳</span> Payment Details
                </h3>
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
                        <x-text-input name="nikah_verification_fee" type="number" class="w-full mt-1" :value="($settings['nikah_verification_fee'] ?? '') ?: '500'" />
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <input type="checkbox" name="nikah_payment_required" id="nikah_payment_required" value="1"
                        {{ ($settings['nikah_payment_required'] ?? '1') === '1' ? 'checked' : '' }}
                        class="rounded border-gray-300 text-teal-600">
                    <label for="nikah_payment_required" class="text-sm text-gray-700">
                        <span class="font-medium">Require the verification fee</span>
                        — uncheck to make Nikah profile verification free for everyone (skips the payment step for every new and existing pending profile), regardless of the discount rule below.
                    </label>
                </div>

                <div class="mt-2 rounded-lg p-4" style="background: #16a34a0d; border: 1px solid #16a34a33">
                    <p class="font-medium text-sm mb-1" style="color: #15803d">🏷️ Marital Status Discount</p>
                    <p class="text-xs text-gray-500 mb-2">Give a reduced (or fully waived) verification fee to applicants with a chosen marital status — e.g. free for widows and divorcees. Set the percentage to 100 for a complete waiver, or lower for a partial discount.</p>
                    @php $discountStatuses = explode(',', $settings['nikah_discount_marital_statuses'] ?? ''); @endphp
                    <div class="flex flex-wrap gap-3 mb-3">
                        @foreach (['never_married' => 'Never Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed', 'separated' => 'Separated', 'married' => 'Married'] as $val => $label)
                        <label class="flex items-center gap-1.5 text-sm text-gray-700">
                            <input type="checkbox" name="nikah_discount_marital_statuses[]" value="{{ $val }}" {{ in_array($val, $discountStatuses) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600">
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                    <div class="max-w-xs">
                        <x-input-label value="Discount Percentage (100 = fully free)" />
                        <x-text-input name="nikah_discount_percent" type="number" min="0" max="100" class="w-full mt-1" :value="$settings['nikah_discount_percent'] ?? '0'" />
                    </div>
                </div>
            </div>

            {{-- Social --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4 border-l-4" style="border-left-color: #1D5FB8">
                <h3 class="font-semibold pb-2 border-b flex items-center gap-2" style="color: #1D5FB8">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-base" style="background: #1D5FB822">📱</span> Social Media
                </h3>
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
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border-l-4" style="border-left-color: #6D4AAE">
                <h3 class="font-semibold mb-5 pb-2 border-b border-gray-100 flex items-center gap-2" style="color: #6D4AAE">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-base" style="background: #6D4AAE22">🔍</span> SEO Settings
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
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4 border-l-4" style="border-left-color: #D2691E">
                <h3 class="font-semibold pb-2 border-b flex items-center gap-2" style="color: #b8591a">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-base" style="background: #D2691E22">🔌</span> Integrations
                </h3>
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
            {{-- Social Login (Google / Facebook / TikTok) --}}
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-6 border-l-4" style="border-left-color: #B8455A">
                <div>
                    <h3 class="font-semibold pb-2 border-b flex items-center gap-2" style="color: #B8455A">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center text-base" style="background: #B8455A22">🔐</span> Social Login (Google / Facebook / TikTok)
                    </h3>
                    <p class="text-xs text-gray-400 mt-2">
                        Lets visitors register/sign in with one tap instead of filling a form. Each provider needs a Client ID and Client Secret from its own developer console, and that console needs to know the exact "Redirect URI" below — copy it in exactly, trailing slashes and all.
                    </p>
                    <p class="text-xs text-gray-400 mt-2">
                        Both Google and Facebook also require a live <strong>Privacy Policy</strong> and <strong>Terms of Service</strong> URL during app verification — Sallaamti's are already public and linked in the site footer:
                        <br>{{ route('privacy-policy') }}
                        <br>{{ route('terms-of-service') }}
                    </p>
                </div>

                {{-- Google --}}
                <div class="border border-gray-100 rounded-lg p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <i class="fab fa-google text-red-500"></i> Google
                        </h4>
                        <label class="flex items-center gap-2 text-xs text-gray-600">
                            <input type="checkbox" name="google_login_enabled" value="1"
                                {{ ($settings['google_login_enabled'] ?? '1') === '1' ? 'checked' : '' }}
                                class="rounded border-gray-300 text-teal-600">
                            Enabled
                        </label>
                    </div>

                    <ol class="text-xs text-gray-500 list-decimal ms-4 space-y-0.5">
                        <li>Go to <span class="font-mono">console.cloud.google.com/apis/credentials</span> and create an "OAuth client ID" (type: Web application).</li>
                        <li>Under "Authorized redirect URIs", paste the URL below exactly.</li>
                        <li>Copy the generated Client ID and Client Secret into the two fields below.</li>
                    </ol>

                    <div>
                        <x-input-label value="Authorized redirect URI" />
                        <input type="text" readonly value="{{ route('social.callback', 'google') }}"
                            onclick="this.select()"
                            class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 font-mono text-gray-600">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Client ID" />
                            <x-text-input name="google_client_id" class="w-full mt-1" :value="$settings['google_client_id'] ?? ''" placeholder="xxxxxxxx.apps.googleusercontent.com" />
                        </div>
                        <div>
                            <x-input-label value="Client Secret" />
                            <x-text-input name="google_client_secret" class="w-full mt-1" :value="$settings['google_client_secret'] ?? ''" placeholder="GOCSPX-..." />
                        </div>
                    </div>
                </div>

                {{-- Facebook --}}
                <div class="border border-gray-100 rounded-lg p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <i class="fab fa-facebook text-blue-600"></i> Facebook
                        </h4>
                        <label class="flex items-center gap-2 text-xs text-gray-600">
                            <input type="checkbox" name="facebook_login_enabled" value="1"
                                {{ ($settings['facebook_login_enabled'] ?? '1') === '1' ? 'checked' : '' }}
                                class="rounded border-gray-300 text-teal-600">
                            Enabled
                        </label>
                    </div>

                    <ol class="text-xs text-gray-500 list-decimal ms-4 space-y-0.5">
                        <li>Go to <span class="font-mono">developers.facebook.com/apps</span>, create an app, and add the "Facebook Login" product.</li>
                        <li>Under Facebook Login → Settings, add the URL below to "Valid OAuth Redirect URIs".</li>
                        <li>Copy the App ID and App Secret (Settings → Basic) into the two fields below.</li>
                    </ol>

                    <div>
                        <x-input-label value="Valid OAuth redirect URI" />
                        <input type="text" readonly value="{{ route('social.callback', 'facebook') }}"
                            onclick="this.select()"
                            class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 font-mono text-gray-600">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="App ID" />
                            <x-text-input name="facebook_client_id" class="w-full mt-1" :value="$settings['facebook_client_id'] ?? ''" placeholder="1234567890123456" />
                        </div>
                        <div>
                            <x-input-label value="App Secret" />
                            <x-text-input name="facebook_client_secret" class="w-full mt-1" :value="$settings['facebook_client_secret'] ?? ''" />
                        </div>
                    </div>
                </div>

                {{-- TikTok — Login Kit, a SEPARATE TikTok Developer app from
                     the one configured under Admin > Integrations for
                     auto-posting (that one needs the Content Posting API
                     product; this one needs Login Kit). Same split as
                     Facebook above. --}}
                <div class="border border-gray-100 rounded-lg p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <i class="fab fa-tiktok text-gray-800"></i> TikTok
                        </h4>
                        <label class="flex items-center gap-2 text-xs text-gray-600">
                            <input type="checkbox" name="tiktok_login_enabled" value="1"
                                {{ ($settings['tiktok_login_enabled'] ?? '1') === '1' ? 'checked' : '' }}
                                class="rounded border-gray-300 text-teal-600">
                            Enabled
                        </label>
                    </div>

                    <ol class="text-xs text-gray-500 list-decimal ms-4 space-y-0.5">
                        <li>Go to <span class="font-mono">developers.tiktok.com/apps</span> and create an app with the "Login Kit" product — a <strong>different</strong> app from the one used for auto-posting under Admin → Integrations, even if both exist in the same developer account.</li>
                        <li>Under Login Kit → Settings, add the redirect URI below.</li>
                        <li>Copy the Client Key and Client Secret into the two fields below.</li>
                    </ol>

                    <div>
                        <x-input-label value="Redirect URI" />
                        <input type="text" readonly value="{{ route('social.callback', 'tiktok') }}"
                            onclick="this.select()"
                            class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 font-mono text-gray-600">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Client Key" />
                            <x-text-input name="tiktok_login_client_id" class="w-full mt-1" :value="$settings['tiktok_login_client_id'] ?? ''" />
                        </div>
                        <div>
                            <x-input-label value="Client Secret" />
                            <x-text-input name="tiktok_login_client_secret" class="w-full mt-1" :value="$settings['tiktok_login_client_secret'] ?? ''" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <x-primary-button>Save Settings</x-primary-button>
            </div>
        </form>

        {{-- Demo Nikah Profiles — cold-start social proof, kept outside the settings form since it's its own action --}}
        <div class="bg-white rounded-lg shadow-sm p-6 space-y-4 mt-6 border-l-4" style="border-left-color: #2E8B8B">
            <h3 class="font-semibold pb-2 border-b flex items-center gap-2" style="color: #2E8B8B">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center text-base" style="background: #2E8B8B22">💍</span> Demo Nikah Profiles
            </h3>
            <p class="text-xs text-gray-400">
                Placeholder verified profiles so the homepage "verified profiles" counter isn't stuck at 0 while the platform is new.
                They're clearly tagged internally (<code>is_demo</code>) and never mixed up with real members. Remove them the moment you have enough real verified profiles.
            </p>
            <p class="text-sm text-gray-600">Currently active: <span class="font-semibold">{{ $demoNikahProfileCount }}</span> demo profile(s).</p>
            <div class="flex flex-wrap gap-3 items-end">
                <form method="POST" action="{{ route('admin.settings.demo-nikah.generate') }}" class="flex items-end gap-2">
                    @csrf
                    <div>
                        <x-input-label value="How many to generate" />
                        <input type="number" name="demo_count" value="10" min="1" max="50" class="border-gray-300 rounded-md text-sm w-24 mt-1">
                    </div>
                    <button class="bg-teal-600 text-white text-sm px-4 py-2 rounded hover:bg-teal-700">Generate</button>
                </form>
                <form method="POST" action="{{ route('admin.settings.demo-nikah.remove') }}"
                    onsubmit="return confirm('Remove all {{ $demoNikahProfileCount }} demo Nikah profiles? This cannot be undone.')">
                    @csrf
                    <button class="bg-red-600 text-white text-sm px-4 py-2 rounded hover:bg-red-700" {{ $demoNikahProfileCount === 0 ? 'disabled' : '' }}>
                        Remove All Demo Profiles
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>