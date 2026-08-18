<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\PublishCommunityPostToSocialJob;
use App\Models\SocialAccount;
use App\Models\SocialPostDispatch;
use App\Models\Setting;
use Google\Client as GoogleClient;
use Google\Service\YouTube as YouTubeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialIntegrationController extends Controller
{
    private const GRAPH_VERSION = 'v19.0';

    public function index()
    {
        $accounts = SocialAccount::where('status', 'connected')->get()->keyBy('platform');
        $settings = Setting::allKeyed();

        return view('admin.integrations.index', ['accounts' => $accounts, 'settings' => $settings]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'facebook_client_id' => ['nullable', 'string'],
            'facebook_client_secret' => ['nullable', 'string'],
            'twitter_client_id' => ['nullable', 'string'],
            'twitter_client_secret' => ['nullable', 'string'],
            'youtube_client_id' => ['nullable', 'string'],
            'youtube_client_secret' => ['nullable', 'string'],
            'tiktok_client_id' => ['nullable', 'string'],
            'tiktok_client_secret' => ['nullable', 'string'],
            'tiktok_audited' => ['nullable', 'boolean'],
            'scheduled_batch_size' => ['nullable', 'integer', 'min:0', 'max:50'],
            'scheduled_batch_time' => ['nullable', 'date_format:H:i'],
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'tiktok_audited') {
                Setting::set($key, $request->boolean('tiktok_audited') ? '1' : '0', 'integrations');
                continue;
            }
            if (filled($value) || $value === '0') {
                Setting::set($key, $value, 'integrations');
            }
        }

        return back()->with('status', 'Integration settings saved.');
    }

    public function connect(string $platform): RedirectResponse
    {
        abort_unless(in_array($platform, ['facebook', 'twitter', 'youtube', 'tiktok'], true), 404);

        return match ($platform) {
            'facebook' => $this->redirectToFacebook(),
            'twitter' => $this->redirectToTwitter(),
            'youtube' => $this->redirectToYoutube(),
            'tiktok' => $this->redirectToTiktok(),
        };
    }

    public function callback(string $platform, Request $request): RedirectResponse
    {
        abort_unless(in_array($platform, ['facebook', 'twitter', 'youtube', 'tiktok'], true), 404);

        try {
            match ($platform) {
                'facebook' => $this->handleFacebookCallback(),
                'twitter' => $this->handleTwitterCallback($request),
                'youtube' => $this->handleYoutubeCallback($request),
                'tiktok' => $this->handleTiktokCallback($request),
            };
        } catch (\Throwable $e) {
            \Log::error("SocialIntegrationController: {$platform} connect failed — " . $e->getMessage());
            return redirect()->route('admin.integrations.index')->with('error', ucfirst($platform) . ' could not be connected: ' . $e->getMessage());
        }

        return redirect()->route('admin.integrations.index')->with('status', ucfirst($platform) . ' connected.');
    }

    public function disconnect(SocialAccount $account)
    {
        // Deleting the local row alone doesn't invalidate the token at the
        // platform — it stays live (Facebook Page tokens effectively don't
        // expire; Google/X/TikTok issue long-lived refresh tokens) and
        // usable by anyone who captured it, even though the admin believes
        // they've disconnected the account. Best-effort revoke first; a
        // failed revoke call still proceeds to delete the local record
        // (revocation failing shouldn't trap the admin unable to disconnect
        // at all), but we at least try instead of never asking.
        $this->revokeToken($account);

        $account->delete();

        return back()->with('status', ucfirst($account->platform) . ' disconnected.');
    }

    private function revokeToken(SocialAccount $account): void
    {
        try {
            match ($account->platform) {
                'facebook', 'instagram' => Http::withToken($account->access_token)
                    ->delete('https://graph.facebook.com/' . self::GRAPH_VERSION . '/me/permissions'),
                'twitter' => Http::asForm()->withBasicAuth(
                    Setting::get('twitter_client_id') ?: config('services.twitter.client_id'),
                    Setting::get('twitter_client_secret') ?: config('services.twitter.client_secret'),
                )->post('https://api.twitter.com/2/oauth2/revoke', [
                    'token' => $account->access_token,
                    'token_type_hint' => 'access_token',
                ]),
                'youtube' => Http::asForm()->post('https://oauth2.googleapis.com/revoke', [
                    'token' => $account->access_token,
                ]),
                'tiktok' => Http::asForm()->post('https://open.tiktokapis.com/v2/oauth/revoke/', [
                    'client_key' => Setting::get('tiktok_client_id') ?: config('services.tiktok.client_id'),
                    'client_secret' => Setting::get('tiktok_client_secret') ?: config('services.tiktok.client_secret'),
                    'token' => $account->access_token,
                ]),
                default => null,
            };
        } catch (\Throwable $e) {
            \Log::warning("SocialIntegrationController: failed to revoke {$account->platform} token before disconnect — " . $e->getMessage());
        }
    }

    public function retryDispatch(SocialPostDispatch $dispatch)
    {
        $account = SocialAccount::active($dispatch->platform);

        if (!$account) {
            return back()->with('error', ucfirst($dispatch->platform) . ' is not connected — connect it in Integrations first.');
        }

        $dispatch->update(['status' => 'queued', 'social_account_id' => $account->id, 'error_message' => null]);
        PublishCommunityPostToSocialJob::dispatch($dispatch);

        return back()->with('status', 'Re-queued.');
    }

    // ===== Facebook + Instagram (Instagram rides on the same Meta OAuth
    // token — there is no separate Instagram login for a Business account,
    // see InstagramPublisher) =====

    private function redirectToFacebook(): RedirectResponse
    {
        Config::set('services.facebook.client_id', Setting::get('facebook_client_id') ?: config('services.facebook.client_id'));
        Config::set('services.facebook.client_secret', Setting::get('facebook_client_secret') ?: config('services.facebook.client_secret'));
        Config::set('services.facebook.redirect', route('admin.integrations.callback', 'facebook'));

        return Socialite::driver('facebook')
            ->scopes(['pages_show_list', 'pages_manage_posts', 'pages_read_engagement', 'business_management', 'instagram_basic', 'instagram_content_publish'])
            ->redirect();
    }

    private function handleFacebookCallback(): void
    {
        Config::set('services.facebook.client_id', Setting::get('facebook_client_id') ?: config('services.facebook.client_id'));
        Config::set('services.facebook.client_secret', Setting::get('facebook_client_secret') ?: config('services.facebook.client_secret'));
        Config::set('services.facebook.redirect', route('admin.integrations.callback', 'facebook'));

        $socialUser = Socialite::driver('facebook')->user();

        // Exchange the short-lived user token Socialite gives us for a
        // long-lived one before deriving Page tokens from it, otherwise
        // the resulting Page token inherits the ~2hr expiry too.
        $exchanged = Http::get('https://graph.facebook.com/' . self::GRAPH_VERSION . '/oauth/access_token', [
            'grant_type' => 'fb_exchange_token',
            'client_id' => config('services.facebook.client_id'),
            'client_secret' => config('services.facebook.client_secret'),
            'fb_exchange_token' => $socialUser->token,
        ])->json('access_token', $socialUser->token);

        $pages = Http::withToken($exchanged)->get('https://graph.facebook.com/' . self::GRAPH_VERSION . '/me/accounts')->json('data', []);

        abort_if(empty($pages), 422, 'No Facebook Pages found for this account — you need to manage at least one Page.');

        // Single-org app — the first Page the admin manages is the one
        // Community Posts get published to.
        $page = $pages[0];

        SocialAccount::updateOrCreate(
            ['platform' => 'facebook', 'external_account_id' => $page['id']],
            [
                'display_name' => $page['name'],
                'access_token' => $page['access_token'],
                'connected_by' => Auth::id(),
                'status' => 'connected',
                'last_error' => null,
            ]
        );

        $igLookup = Http::withToken($page['access_token'])->get('https://graph.facebook.com/' . self::GRAPH_VERSION . "/{$page['id']}", [
            'fields' => 'instagram_business_account',
        ])->json('instagram_business_account.id');

        if ($igLookup) {
            $igUsername = Http::withToken($page['access_token'])->get('https://graph.facebook.com/' . self::GRAPH_VERSION . "/{$igLookup}", [
                'fields' => 'username',
            ])->json('username');

            SocialAccount::updateOrCreate(
                ['platform' => 'instagram', 'external_account_id' => $igLookup],
                [
                    'display_name' => $igUsername ? "@{$igUsername}" : 'Instagram',
                    'access_token' => $page['access_token'],
                    'extra' => ['page_id' => $page['id']],
                    'connected_by' => Auth::id(),
                    'status' => 'connected',
                    'last_error' => null,
                ]
            );
        }
    }

    // ===== X (Twitter) — OAuth 2.0 + PKCE, no maintained Socialite driver
    // exists for X's newer posting scopes, so this talks to the token
    // endpoint directly =====

    private function redirectToTwitter(): RedirectResponse
    {
        $verifier = Str::random(64);
        $state = Str::random(32);
        session(['twitter_oauth_verifier' => $verifier, 'twitter_oauth_state' => $state]);

        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => Setting::get('twitter_client_id') ?: config('services.twitter.client_id'),
            'redirect_uri' => route('admin.integrations.callback', 'twitter'),
            'scope' => 'tweet.read tweet.write users.read offline.access',
            'state' => $state,
            'code_challenge' => $challenge,
            'code_challenge_method' => 'S256',
        ]);

        return redirect("https://twitter.com/i/oauth2/authorize?{$query}");
    }

    private function handleTwitterCallback(Request $request): void
    {
        abort_unless($request->get('state') === session('twitter_oauth_state'), 422, 'Invalid OAuth state.');

        $clientId = Setting::get('twitter_client_id') ?: config('services.twitter.client_id');
        $clientSecret = Setting::get('twitter_client_secret') ?: config('services.twitter.client_secret');

        $token = Http::asForm()->withBasicAuth($clientId, $clientSecret)->post('https://api.twitter.com/2/oauth2/token', [
            'grant_type' => 'authorization_code',
            'code' => $request->get('code'),
            'redirect_uri' => route('admin.integrations.callback', 'twitter'),
            'code_verifier' => session('twitter_oauth_verifier'),
        ]);

        abort_if($token->failed(), 422, $token->json('error_description', 'X token exchange failed.'));

        $accessToken = $token->json('access_token');
        $refreshToken = $token->json('refresh_token');
        $expiresIn = $token->json('expires_in');

        $me = Http::withToken($accessToken)->get('https://api.twitter.com/2/users/me')->json('data');

        SocialAccount::updateOrCreate(
            ['platform' => 'twitter', 'external_account_id' => $me['id']],
            [
                'display_name' => '@' . ($me['username'] ?? $me['id']),
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_expires_at' => $expiresIn ? now()->addSeconds($expiresIn) : null,
                'connected_by' => Auth::id(),
                'status' => 'connected',
                'last_error' => null,
            ]
        );

        session()->forget(['twitter_oauth_verifier', 'twitter_oauth_state']);
    }

    // ===== YouTube — the official Google API client, not Socialite's
    // login-oriented Google driver (needs the youtube.upload scope +
    // resumable upload support that only the full client provides) =====

    private function googleClient(): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId(Setting::get('youtube_client_id') ?: config('services.youtube.client_id'));
        $client->setClientSecret(Setting::get('youtube_client_secret') ?: config('services.youtube.client_secret'));
        $client->setRedirectUri(route('admin.integrations.callback', 'youtube'));
        $client->setScopes(['https://www.googleapis.com/auth/youtube.upload', 'https://www.googleapis.com/auth/youtube.readonly']);
        $client->setAccessType('offline');
        $client->setPrompt('consent'); // forces a refresh_token every time, not just on first-ever consent

        return $client;
    }

    private function redirectToYoutube(): RedirectResponse
    {
        $state = Str::random(32);
        session(['youtube_oauth_state' => $state]);

        $client = $this->googleClient();
        $client->setState($state);

        return redirect($client->createAuthUrl());
    }

    private function handleYoutubeCallback(Request $request): void
    {
        abort_unless($request->get('state') === session('youtube_oauth_state'), 422, 'Invalid OAuth state.');
        session()->forget('youtube_oauth_state');

        $client = $this->googleClient();
        $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));

        abort_if(isset($token['error']), 422, $token['error_description'] ?? 'YouTube token exchange failed.');
        abort_if(empty($token['refresh_token']), 422, 'Google did not return a refresh token — revoke this app\'s access at myaccount.google.com/permissions and try connecting again.');

        $client->setAccessToken($token);
        $channel = (new YouTubeService($client))->channels->listChannels('snippet', ['mine' => true])->getItems()[0] ?? null;

        abort_if(!$channel, 422, 'No YouTube channel found on this Google account.');

        SocialAccount::updateOrCreate(
            ['platform' => 'youtube', 'external_account_id' => $channel->getId()],
            [
                'display_name' => $channel->getSnippet()->getTitle(),
                'access_token' => $token['access_token'],
                'refresh_token' => $token['refresh_token'],
                'token_expires_at' => now()->addSeconds($token['expires_in'] ?? 3600),
                'connected_by' => Auth::id(),
                'status' => 'connected',
                'last_error' => null,
            ]
        );
    }

    // ===== TikTok — Content Posting API, no usable PHP SDK exists =====

    private function redirectToTiktok(): RedirectResponse
    {
        $state = Str::random(32);
        session(['tiktok_oauth_state' => $state]);

        $query = http_build_query([
            'client_key' => Setting::get('tiktok_client_id') ?: config('services.tiktok.client_id'),
            'scope' => 'user.info.basic,video.publish',
            'response_type' => 'code',
            'redirect_uri' => route('admin.integrations.callback', 'tiktok'),
            'state' => $state,
        ]);

        return redirect("https://www.tiktok.com/v2/auth/authorize/?{$query}");
    }

    private function handleTiktokCallback(Request $request): void
    {
        abort_unless($request->get('state') === session('tiktok_oauth_state'), 422, 'Invalid OAuth state.');

        $token = Http::asForm()->post('https://open.tiktokapis.com/v2/oauth/token/', [
            'client_key' => Setting::get('tiktok_client_id') ?: config('services.tiktok.client_id'),
            'client_secret' => Setting::get('tiktok_client_secret') ?: config('services.tiktok.client_secret'),
            'code' => $request->get('code'),
            'grant_type' => 'authorization_code',
            'redirect_uri' => route('admin.integrations.callback', 'tiktok'),
        ]);

        abort_if($token->failed(), 422, $token->json('error_description', 'TikTok token exchange failed.'));

        $accessToken = $token->json('access_token');
        $openId = $token->json('open_id');

        $info = Http::withToken($accessToken)->get('https://open.tiktokapis.com/v2/user/info/', [
            'fields' => 'display_name',
        ])->json('data.user');

        SocialAccount::updateOrCreate(
            ['platform' => 'tiktok', 'external_account_id' => $openId],
            [
                'display_name' => $info['display_name'] ?? 'TikTok',
                'access_token' => $accessToken,
                'refresh_token' => $token->json('refresh_token'),
                'token_expires_at' => now()->addSeconds($token->json('expires_in', 86400)),
                'connected_by' => Auth::id(),
                'status' => 'connected',
                'last_error' => null,
            ]
        );

        session()->forget('tiktok_oauth_state');
    }
}
